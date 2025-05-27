<?php
// app/controllers/ApiController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ingredients;

/**
 * ApiController pour gérer les requêtes AJAX
 */
class ApiController extends Controller {
    private $ingredientModel;

    public function __construct() {
        $this->ingredientModel = new Ingredients();
    }

    /**
     * Recherche d'ingrédients via AJAX
     */
    public function searchIngredients() {
        // En-têtes pour autoriser les requêtes AJAX
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');

        // Récupérer le terme de recherche
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Utiliser le modèle Ingredients pour rechercher
        $ingredients = $this->ingredientModel->searchIngredients($search);

        // Renvoyer les résultats au format JSON
        echo json_encode($ingredients);
        exit; // Important pour éviter que le reste du framework ne soit exécuté
    }
}