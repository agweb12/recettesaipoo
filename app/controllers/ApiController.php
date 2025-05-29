<?php
// app/controllers/ApiController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recettes;
use App\Models\Ingredients;

/**
 * ApiController pour gérer les requêtes AJAX
 */
class ApiController extends Controller {
    private $ingredientModel;
    private $recetteModel;

    public function __construct() {
        $this->recetteModel = new Recettes();
        $this->ingredientModel = new Ingredients();
    }

    /**
     * Gestion des favoris (ajout/suppression)
     * @return void
     */
    public function favoris(): void 
    {
        // Vérification si l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Vous devez être connecté pour gérer vos favoris'], 401);
            return;
        }

        // Vérification de la méthode et des paramètres
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
            return;
        }

        if (!isset($_POST['id_recette']) || !is_numeric($_POST['id_recette']) || !isset($_POST['action'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Paramètres manquants ou invalides'], 400);
            return;
        }

        $recipeId = intval($_POST['id_recette']);
        $action = $_POST['action'];
        $userId = $_SESSION['user']['id'];

        // Traitement de l'action
        try {
            if ($action === 'add') {
                // Vérification si déjà en favoris
                if ($this->recetteModel->isRecipeFavorite($userId, $recipeId)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Cette recette est déjà dans vos favoris']);
                    return;
                }

                // Ajout aux favoris
                $result = $this->recetteModel->addToFavorites($userId, $recipeId);
                if ($result) {
                    $this->jsonResponse(['success' => true, 'message' => 'Recette ajoutée aux favoris']);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Erreur lors de l\'ajout aux favoris'], 500);
                }
            } elseif ($action === 'remove') {
                // Suppression des favoris
                $result = $this->recetteModel->removeFromFavorites($userId, $recipeId);
                if ($result) {
                    $this->jsonResponse(['success' => true, 'message' => 'Recette retirée des favoris']);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression des favoris'], 500);
                }
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Action non reconnue'], 400);
            }
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Recherche d'ingrédients pour l'autocomplétion
     * @return void
     */
    public function searchIngredientsAuto(): void 
    {
        // Vérification de la requête
        if (!isset($_GET['search']) || empty($_GET['search'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Paramètre de recherche manquant', 'ingredients' => []], 400);
            return;
        }

        $search = htmlspecialchars($_GET['search']);
        $ingredients = $this->ingredientModel->searchIngredients($search);
        
        $this->jsonResponse(['success' => true, 'ingredients' => $ingredients]);
    }

    /**
     * Envoie une réponse JSON avec le code HTTP spécifié
     * @param mixed $data Données à envoyer
     * @param int $statusCode Code HTTP
     * @return void
     */
    private function jsonResponse($data, int $statusCode = 200): void 
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}