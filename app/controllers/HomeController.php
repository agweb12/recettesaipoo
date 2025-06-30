<?php
// app/controllers/HomeController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recettes;
use App\Models\Ingredients;
use App\Helpers\StructuredData;

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
        $isLoggedIn = $this->isLoggedIn();

        // Récupérer les recettes populaires au début
        $popularRecipes = $this->recetteModel->getPopularRecipes(3);

        // Traitement du formulaire d'ingrédients
        if($isLoggedIn && isset($_POST['submit_ingredients']) && !empty($_POST['ingredients'])){
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

        // Générer les données structurées
        $structuredData = [
            'website' => StructuredData::getWebSiteData(),
            'organization' => StructuredData::getOrganizationData(),
            'navigation' => StructuredData::getSiteNavigationData()
        ];
        
        // Si on a des recettes populaires, ajouter les données de liste
        if (!empty($popularRecipes)) {
            $structuredData['popularRecipes'] = StructuredData::getRecipeListData($popularRecipes);
        }

        $recentRecipes = $this->recetteModel->getRecentRecipes(3);

        // Chargement de la vue avec les données
        $this->view('accueil', [
            'titlePage' => "Recettes AI - Trouvez des recettes avec vos ingrédients",
            'descriptionPage' => "Recettes AI, Découvrez des recettes personnalisées selon les ingrédients que vous avez chez vous. Réduisez le gaspillage alimentaire avec Recettes AI.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "Recettes AI, recette, cuisine facile, anti-gaspillage, recettes personnalisées, assistant culinaire",
            'isLoggedIn' => $isLoggedIn,
            'user' => isset($_SESSION['user']) ? $_SESSION['user'] : null,
            'popularRecipes' => $popularRecipes,
            'recentRecipes' => $recentRecipes,
            'structuredData' => $structuredData
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