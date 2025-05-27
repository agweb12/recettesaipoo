<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recettes;
use App\Models\Ingredients;
use App\Models\Etiquettes;
use App\Models\Categories;

class RecettesController extends Controller {
    private $recetteModel;
    private $ingredientModel;
    private $etiquetteModel;
    private $categorieModel;

    public function __construct() {
        $this->recetteModel = new Recettes();
        $this->ingredientModel = new Ingredients();
        $this->etiquetteModel = new Etiquettes();
        $this->categorieModel = new Categories();
    }

    public function index() {
        $whereConditions = []; // Tableau pour stocker les conditions WHERE de la requête SQL
        $filterParams = []; // Tableau pour stocker les paramètres de filtre
        // Logique pour afficher toutes les recettes
        $recettes= []; // Tableau pour stocker les recettes
        $ingredientsUtilisateur = []; // Tableau pour stocker les ingrédients de l'utilisateur
        $recettesRecherche = []; // Tableau pour stocker les recettes de recherche
        $recettesFavorisIds = []; // Tableau pour stocker les IDs des recettes favorites de l'utilisateur

        // Si l'utilisateur est connecté
        if($this->isLoggedIn()){
            $userId = $_SESSION['user']['id'];

            // Récupérer les favoris de l'utilisateur
            $recettesFavorisIds = $this->recetteModel->getUserFavoriteIds($userId);

            // Si le formulaire d'ingrédients a été soumis
            if(isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1){
                // Récupérer les ingrédients de l'utilisateur
                $ingredientsUtilisateur = $this->ingredientModel->getUserIngredients($userId);
                
                // Récupérer les recettes correspondant aux ingrédients de l'utilisateur
                $recettes = $this->recetteModel->getRecipesByUserIngredients($userId);
            }
        }
        
        // Traiter les recherches et les filtres
        if(isset($_GET['search']) || $this->hasFilters($_GET)) {
            // Construire les conditions de recherche
            if(isset($_GET['search']) && !empty($_GET['search'])) {
                $searchTerm = $_GET['search'];
                $whereConditions[] = "(r.nom LIKE :search OR r.descriptif LIKE :search)";
                $filterParams[':search'] = "%{$searchTerm}%";
            }
            
            // Traiter les filtres
            $this->processFilters($_GET, $whereConditions, $filterParams);
            
            // Récupérer les recettes filtrées
            $recettesRecherche = $this->recetteModel->getFilteredRecipes($whereConditions, $filterParams);
        }
        
        // Charger la vue avec les données
        $this->view('recettes', [
            'titlePage' => "Toutes les recettes - Recettes AI",
            'descriptionPage' => "Découvrez toutes nos recettes de cuisine sur Recettes AI",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "recettes, cuisine, ingrédients, filtres, recherche",
            'recettes' => $recettes,
            'recettesRecherche' => $recettesRecherche,
            'ingredientsUtilisateur' => $ingredientsUtilisateur,
            'recettesFavorisIds' => $recettesFavorisIds,
            'etiquettes' => $this->etiquetteModel->getAllEtiquettes(),
            'categories' => $this->categorieModel->getAllCategories()
        ]);
    }

    public function show() {
        // Logique pour afficher une recette spécifique
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("HTTP/1.0 404 Not Found");
            echo "Recette non trouvée";
            return;
        }
        
        $recette = $this->recetteModel->findById($id);
        $this->view('recette', [
            'titlePage' => $recette['nom'] . " - Recettes AI",
            'recette' => $recette
        ]);
    }

    /**
     * Vérifie si la requête contient des filtres
     */
    private function hasFilters($request) {
        $filterTypes = ['difficulte', 'temps_preparation', 'temps_cuisson', 'categorie', 'etiquette'];
        foreach($filterTypes as $type) {
            $paramName = "filtre_{$type}";
            if(isset($request[$paramName]) && !empty($request[$paramName])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Traite les filtres de la requête et construit les conditions WHERE
     */
    private function processFilters($request, &$whereConditions, &$filterParams) {
        $filterTypes = ['difficulte', 'temps_preparation', 'temps_cuisson', 'categorie', 'etiquette'];
        foreach($filterTypes as $type) {
            $paramName = "filtre_{$type}";
            if(isset($request[$paramName]) && !empty($request[$paramName])) {
                $values = explode(',', $request[$paramName]);
                
                if($type === 'etiquette') {
                    $whereConditions[] = "r.id IN (
                        SELECT DISTINCT re.id_recette 
                        FROM recette_etiquette re 
                        WHERE re.id_etiquette IN (" . implode(',', array_map('intval', $values)) . ")
                    )";
                }
                else if($type === 'temps_preparation' || $type === 'temps_cuisson') {
                    $timeConditions = [];
                    foreach($values as $value) {
                        $value = intval($value);
                        if($value === 15) {
                            $timeConditions[] = "r.{$type} <= 15";
                        } else if($value === 30) {
                            $timeConditions[] = "r.{$type} <= 30";
                        } else if($value === 60) {
                            $timeConditions[] = "r.{$type} <= 60";
                        } else if($value === 120) {
                            $timeConditions[] = "r.{$type} > 60";
                        }
                    }
                    if(count($timeConditions) > 0) {
                        $whereConditions[] = "(" . implode(' OR ', $timeConditions) . ")";
                    }
                }
                else {
                    $placeholders = [];
                    foreach($values as $i => $val) {
                        $placeholder = ":{$type}_{$i}";
                        $placeholders[] = $placeholder;
                        $filterParams[$placeholder] = $val;
                    }
                    $whereConditions[] = "r.{$type} IN (" . implode(',', $placeholders) . ")";
                }
            }
        }
    }
}