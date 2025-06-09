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

    /**
     * Affiche toutes les recettes avec options de filtrage et de recherche
     * @return void
     */
    public function index() : void
    {
        $whereConditions = []; // Tableau pour stocker les conditions WHERE de la requête SQL
        $filterParams = []; // Tableau pour stocker les paramètres de filtre
        // Logique pour afficher toutes les recettes
        $recettes= []; // Tableau pour stocker les recettes
        $ingredientsUtilisateur = []; // Tableau pour stocker les ingrédients de l'utilisateur
        $recettesRecherche = []; // Tableau pour stocker les recettes de recherche
        $recettesFavorisIds = []; // Tableau pour stocker les IDs des recettes favorites de l'utilisateur
        $formIngredients = isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1; // Vérifie si le formulaire d'ingrédients a été soumis
        $hasFilters = $this->hasFilters($_GET); // Vérifie si des filtres sont appliqués

        // Si l'utilisateur est connecté
        if($this->isLoggedIn()){
            $userId = $_SESSION['user']['id'];

            // Récupérer les favoris de l'utilisateur
            $recettesFavorisIds = $this->recetteModel->getUserFavoriteIds($userId);

            // Si le formulaire d'ingrédients a été soumis
            if(isset($formIngredients)){
                // Récupérer les ingrédients de l'utilisateur
                $ingredientsUtilisateur = $this->ingredientModel->getUserIngredients($userId);
                
                // Récupérer les recettes correspondant aux ingrédients de l'utilisateur
                $recettesIngredients = $this->recetteModel->getRecipesByUserIngredients($userId);

                // Ajouter le nombre total d'ingrédients pour chaque recette
                // array_map permet de parcourir chaque recette et d'ajouter le nombre d'ingrédients
                $recettes = array_map(function($recette){
                    $recette['nombre_ingredients_total'] = $this->recetteModel->countRecipeIngredients($recette['id']);
                    return $recette;
                }, $recettesIngredients);
            }
        }
        
        // Traiter les recherches et les filtres
        if(isset($_GET['search']) || $hasFilters) {
            // Construire les conditions de recherche
            if(isset($_GET['search']) && !empty($_GET['search'])) {
                $searchTerm = $_GET['search'];
                $whereConditions[] = "(r.nom LIKE :search OR r.descriptif LIKE :search)";
                $filterParams[':search'] = "%{$searchTerm}%";
            }
            
            // Traiter les filtres
            $this->processFilters($_GET, $whereConditions, $filterParams);
            
            // Récupérer les recettes filtrées
            $recettesIngredients = $this->recetteModel->getFilteredRecipes($whereConditions, $filterParams);

            // Pour chaque recette, récupérer les étiquettes associées et préparer le tableau d'IDs des recettes
            // On utilise array_map pour parcourir chaque recette et ajouter les étiquettes
            $recettesRecherche = array_map(function($recette) {
                $etiquettes = $this->etiquetteModel->getRecipeEtiquettes($recette['id']);
                
                $etiquettesIds = [];
                foreach($etiquettes as $etiquette) {
                    $etiquettesIds[] = $etiquette['id'];
                }
                
                $recette['etiquettes_ids'] = implode(',', $etiquettesIds);
                $recette['nb_etiquettes'] = count($etiquettes);
                
                return $recette;
            }, $recettesIngredients);
        }

        // Récupérer toutes les catégories et étiquettes pour les filtres
        $categories = $this->categorieModel->getAllCategories();
        $etiquettes = $this->etiquetteModel->getAllEtiquettes();
        
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
            'etiquettes' => $etiquettes,
            'categories' => $categories,
            'formIngredients' => $formIngredients,
            'hasFilters' => $hasFilters
        ]);
    }

    /**
     * Vérifie si la requête contient des filtres
     * @param array $request La requête HTTP
     * @return bool True si des filtres sont présents, sinon false
     */
    private function hasFilters($request) : bool
    {
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
     * @param array $request La requête HTTP
     * @param array $whereConditions Tableau pour stocker les conditions WHERE
     * @param array $filterParams Tableau pour stocker les paramètres de filtre
     * @return void
     */
    private function processFilters($request, &$whereConditions, &$filterParams) : void
    {
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

    /**
     * Affiche une recette spécifique
     * @return void
     */
    public function show() : void
    {
        // Logique pour afficher une recette spécifique
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            // Si l'ID n'est pas valide, on redirige vers la page d'accueil ou on affiche une erreur
            header("HTTP/1.0 404 Not Found");
            echo "<div class='alert alert-danger'>ID de recette invalide</div>";
            return;
        }

        $recipeId = intval($id); // Convertir l'ID en entier pour éviter les injections SQL
        
        // 1. Récupérer la recette
        $recette = $this->recetteModel->getRecipeById($recipeId);

        if (!$recette) {
            header("HTTP/1.0 404 Not Found");
            echo "<div class='alert alert-error'>Recette introuvable</div>";
            return;
        }

        // 2. Récupérer les étiquettes de la recette
        $etiquettes = $this->etiquetteModel->getRecipeEtiquettes($recipeId);

        $etiquettesIds = [];
        foreach($etiquettes as $etiquette) {
            $etiquettesIds[] = $etiquette['id'];
        }

        // 3. Vérifier si la recette est en favoris pour l'utilisateur connecté
        $isFavorite = false;
        $ingredientsUser = [];
        $possedeListIngredients = false;


        if ($this->isLoggedIn()) {
            $userId = $_SESSION['user']['id'];
            $isFavorite = $this->recetteModel->isRecipeFavorite($userId, $recipeId);

            // 4. Récupérer les ingrédients de l'utilisateur
            $ingredientsUser = $this->ingredientModel->getUserIngredientsIds($userId);
            $possedeListIngredients = !empty($ingredientsUser);
        }

        // 5. Récupérer les ingrédients de la recette
        $ingredients = $this->recetteModel->getRecipeIngredients($recipeId);
        $ingredientsList = [];
        
        foreach ($ingredients as $ingredient) {
            $estDisponible = in_array($ingredient['id'], $ingredientsUser);
            
            $ingredientsList[] = [
                'id' => $ingredient['id'],
                'nom' => $ingredient['nom'],
                'quantite' => $ingredient['quantite'],
                'unite' => $ingredient['unite'],
                'disponible' => $estDisponible,
                'possedeListIngredients' => $possedeListIngredients
            ];
        }

        // 6. Récupérer la liste des unités de mesure
        $uniteMesureList = $this->recetteModel->getAllUniteMesure();

        // 7. Charger la vue avec les données
        $this->view('recettes/recette', [
            'titlePage' => htmlspecialchars($recette['nom']) . " - Recettes AI",
            'descriptionPage' => "Découvrez comment préparer " . htmlspecialchars($recette['nom']) . ". " . substr(htmlspecialchars($recette['descriptif']), 0, 150) . "...",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "Recettes AI, recette, ai, intelligence artificielle, cuisine, ingrédients, recettes, trouver une recette",
            'recette' => $recette,
            'etiquettes' => $etiquettes,
            'etiquettesIds' => $etiquettesIds,
            'isFavorite' => $isFavorite,
            'ingredientsList' => $ingredientsList,
            'uniteMesureList' => $uniteMesureList,
            'possedeListIngredients' => $possedeListIngredients
        ]);
    }
}