<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recettes;

class RecettesController extends Controller {
    private $recetteModel;

    public function __construct() {
        $this->recetteModel = new Recettes();
    }

    public function index() {
        // Logique pour afficher toutes les recettes
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