<?php
// app/controllers/HomeController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recettes;
use App\Models\Ingredients;
/**
 * HomeController gère la page d'accueil et le traitement des ingrédients.
 * Il interagit avec les modèles Recettes et Ingredients pour récupérer les données nécessaires.
 */
class HomeController extends Controller {
    private $recetteModel; // Modèle pour interagir avec les recettes
    private $ingredientModel; // Modèle pour interagir avec les ingrédients

    public function __construct() {
        $this->recetteModel = new Recettes(); // Instanciation du modèle Recettes
        $this->ingredientModel = new Ingredients(); // Instanciation du modèle Ingrédients
    }

    public function index() {
        // Traitement du formulaire d'ingrédients
        if($this->isLoggedIn() && isset($_POST['submit_ingredients']) && !empty($_POST['ingredients'])){
            // Vérifier qu'au moins un ingrédient valide est présent
            $hasValidIngredient = false;
            foreach($_POST['ingredients'] as $ingredient){
                if(!empty($ingredient)){
                    $hasValidIngredient = true; // Au moins un ingrédient est valide
                    break; // Pour éviter de continuer à vérifier les autres ingrédients si un valide est trouvé
                }
            }

            if($hasValidIngredient){
                $this->processIngredientForm();
                return; // Redirection déjà effectuée dans processIngredientForm
            }

        }

        // Récupération des recettes populaires et récentes
        $popularRecipes = $this->recetteModel->getPopularRecipes(3);
        $recentRecipes = $this->recetteModel->getRecentRecipes(3);

        // Chargement de la vue avec les données
        $this->view('accueil', [
            'titlePage' => "Recettes AI",
            'descriptionPage' => "Recettes AI est un site qui vous permet de trouver des recettes de cuisine en fonction des ingrédients que vous avez chez vous.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "Recettes AI, recette, ai, intelligence artificielle, cuisine, ingrédients, recettes, trouver une recette",
            'popularRecipes' => $popularRecipes,
            'recentRecipes' => $recentRecipes,
            'user' => isset($_SESSION['user']) ? $_SESSION['user'] : null // Passer les informations de l'utilisateur
        ]);
    }

    private function processIngredientForm(){
        $userId = $_SESSION['user']['id']; // Récupération de l'id de l'utilisateur
        $ingredients = $_POST['ingredients']; // Récupération des ingrédients du formulaire

        // Supprimer les ingrédients existants puis ajouter les nouveaux
        // Je supprime les ingrédients actuels de l'utilisatuer afin qu'à chaque soumission du formulaire, cela remplace la liste d'ingrédients précédente
        $this->ingredientModel->deleteUserIngredients($userId); // Suppression des ingrédients existants de l'utilisateur
        $this->ingredientModel->addUserIngredients($userId, $ingredients); // Ajout des nouveaux ingrédients de l'utilisateur

        // Redirection vers la page des recettes
        $this->redirect(RACINE_SITE . 'recettes?formIngredients=1');
    }
}