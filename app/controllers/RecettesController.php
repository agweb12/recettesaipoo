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
        $this->view('recettes', [
            'titlePage' => "Toutes les recettes - Recettes AI",
            'descriptionPage' => "Découvrez toutes nos recettes",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "recettes, cuisine"
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
}