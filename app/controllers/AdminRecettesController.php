<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recettes;
use App\Models\Categories;
use App\Models\Etiquettes;
use App\Models\Ingredients;
use App\Models\UnitesMesure;
use App\Utils\Utils;

class AdminRecettesController extends Controller {
    private $recetteModel;
    private $categorieModel;
    private $etiquetteModel;
    private $ingredientModel;
    private $uniteMesureModel;
    
    public function __construct() {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!isset($_SESSION['admin'])) {
            $this->redirect(RACINE_SITE . 'admin/login');
            exit();
        }
        
        $this->recetteModel = new Recettes();
        $this->categorieModel = new Categories();
        $this->etiquetteModel = new Etiquettes();
        $this->ingredientModel = new Ingredients();
        $this->uniteMesureModel = new UnitesMesure();
    }
    
    /**
     * Affiche la liste des recettes
     */
    public function index() {
        // Récupérer la liste des recettes avec leur catégorie
        $recettes = $this->recetteModel->getAllRecettesForAdmin();
        
        // Message de notification (info/erreur)
        $info = isset($_GET['info']) ? htmlspecialchars($_GET['info']) : '';
        $infoType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        
        $this->view('admin/recettes/index', [
            'titlePage' => "Gestion des Recettes",
            'descriptionPage' => "Gérer les recettes de Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'recettes' => $recettes,
            'info' => $info,
            'infoType' => $infoType,
            'recetteEdit' => null,
            'categories' => $this->categorieModel->getAllCategories(),
            'etiquettes' => $this->etiquetteModel->getAllEtiquettes(),
            'ingredients' => $this->ingredientModel->getAllIngredients(),
            'unites' => $this->uniteMesureModel->getAllUnitesMesure()
        ]);
    }
    
    /**
     * Affiche le formulaire d'édition d'une recette
     */
    public function edit($id) {
        $recette = $this->recetteModel->findById($id);
        
        if (!$recette) {
            $this->redirect(RACINE_SITE . 'admin/recettes?info=Recette introuvable.&type=danger');
            exit();
        }
        
        // Récupérer les étiquettes associées à cette recette
        $recetteEtiquettes = $this->recetteModel->getRecipeEtiquettesIds($id);
        
        // Récupérer les ingrédients associés à cette recette
        $recetteIngredients = $this->recetteModel->getRecipeIngredientsAdmin($id);
        
        $this->view('admin/recettes/index', [
            'titlePage' => "Modifier une recette",
            'descriptionPage' => "Modifier une recette existante",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'recettes' => $this->recetteModel->getAllRecettesForAdmin(),
            'recetteEdit' => $recette,
            'recetteEtiquettesData' => $recetteEtiquettes,
            'recetteIngredientsData' => $recetteIngredients,
            'categories' => $this->categorieModel->getAllCategories(),
            'etiquettes' => $this->etiquetteModel->getAllEtiquettes(),
            'ingredients' => $this->ingredientModel->getAllIngredients(),
            'unites' => $this->uniteMesureModel->getAllUnitesMesure(),
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'success'
        ]);
    }
    
    /**
     * Traite le formulaire d'ajout d'une recette
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/recettes');
            exit();
        }
        
        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }

        $nom = Utils::sanitize($_POST['nom']);
        $descriptif = Utils::sanitize($_POST['descriptif']);
        $instructions = Utils::sanitize($_POST['instructions']);
        $temps_preparation = intval($_POST['temps_preparation']);
        $temps_cuisson = intval($_POST['temps_cuisson']);
        $difficulte = Utils::sanitize($_POST['difficulte']);
        $id_categorie = intval($_POST['id_categorie']);
        $id_admin = $_SESSION['admin']['id'];
        
        // Gestion de l'image
        $image_url = $this->handleImageUpload();
        
        // Vérification des champs
        if (empty($nom) || empty($descriptif) || empty($instructions)) {
            $this->redirect(RACINE_SITE . 'admin/recettes?info=Veuillez remplir tous les champs obligatoires.&type=danger');
            exit();
        }
        
        // Préparation des données de la recette
        $recetteData = [
            'id_admin' => $id_admin,
            'nom' => $nom,
            'descriptif' => $descriptif,
            'instructions' => $instructions,
            'image_url' => $image_url,
            'temps_preparation' => $temps_preparation,
            'temps_cuisson' => $temps_cuisson,
            'difficulte' => $difficulte,
            'id_categorie' => $id_categorie
        ];
        
        // Insertion de la recette
        $recetteId = $this->recetteModel->create($recetteData);
        
        if ($recetteId) {
            // Associer les étiquettes
            if (isset($_POST['etiquettes']) && is_array($_POST['etiquettes'])) {
                foreach ($_POST['etiquettes'] as $etiquetteId) {
                    $this->recetteModel->addEtiquette($recetteId, $etiquetteId);
                }
            }
            
            // Associer les ingrédients
            if (isset($_POST['ingredient_id']) && is_array($_POST['ingredient_id'])) {
                for ($i = 0; $i < count($_POST['ingredient_id']); $i++) {
                    if (!empty($_POST['ingredient_id'][$i]) && !empty($_POST['quantite'][$i])) {
                        $ingredientId = intval($_POST['ingredient_id'][$i]);
                        $quantite = floatval($_POST['quantite'][$i]);
                        $uniteId = !empty($_POST['unite_id'][$i]) ? intval($_POST['unite_id'][$i]) : null;
                        
                        $this->recetteModel->addIngredient($recetteId, $ingredientId, $quantite, $uniteId);
                    }
                }
            }
            
            // Enregistrement de l'action dans le journal
            $this->recetteModel->logAdminAction($_SESSION['admin']['id'], 'recette', $recetteId, 'ajout');
            
            $this->redirect(RACINE_SITE . 'admin/recettes?info=Nouvelle recette ajoutée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/recettes?info=Erreur lors de l\'ajout de la recette.&type=danger');
        }
    }
    
    /**
     * Met à jour une recette
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/recettes');
            exit();
        }

        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        $descriptif = Utils::sanitize($_POST['descriptif']);
        $instructions = Utils::sanitize($_POST['instructions']);
        $temps_preparation = intval($_POST['temps_preparation']);
        $temps_cuisson = intval($_POST['temps_cuisson']);
        $difficulte = Utils::sanitize($_POST['difficulte']);
        $id_categorie = intval($_POST['id_categorie']);
        
        // Gestion de l'image
        $image_url = isset($_POST['image_url_actuelle']) ? $_POST['image_url_actuelle'] : null;
        
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
            $newImageUrl = $this->handleImageUpload($id);
            if ($newImageUrl) {
                $image_url = $newImageUrl;
            }
        }
        
        // Vérification des champs
        if (empty($nom) || empty($descriptif) || empty($instructions)) {
            $this->redirect(RACINE_SITE . 'admin/recettes/edit/' . $id . '?info=Veuillez remplir tous les champs obligatoires.&type=danger');
            exit();
        }
        
        // Préparation des données de la recette
        $recetteData = [
            'nom' => $nom,
            'descriptif' => $descriptif,
            'instructions' => $instructions,
            'temps_preparation' => $temps_preparation,
            'temps_cuisson' => $temps_cuisson,
            'difficulte' => $difficulte,
            'id_categorie' => $id_categorie
        ];
        
        // Ajouter l'image seulement si elle existe
        if ($image_url) {
            $recetteData['image_url'] = $image_url;
        }
        
        // Mise à jour de la recette
        $result = $this->recetteModel->update($id, $recetteData);
        
        if ($result) {
            // Mettre à jour les étiquettes
            $this->recetteModel->deleteAllEtiquettes($id);
            if (isset($_POST['etiquettes']) && is_array($_POST['etiquettes'])) {
                foreach ($_POST['etiquettes'] as $etiquetteId) {
                    $this->recetteModel->addEtiquette($id, $etiquetteId);
                }
            }
            
            // Mettre à jour les ingrédients
            $this->recetteModel->deleteAllIngredients($id);
            if (isset($_POST['ingredient_id']) && is_array($_POST['ingredient_id'])) {
                for ($i = 0; $i < count($_POST['ingredient_id']); $i++) {
                    if (!empty($_POST['ingredient_id'][$i]) && !empty($_POST['quantite'][$i])) {
                        $ingredientId = intval($_POST['ingredient_id'][$i]);
                        $quantite = floatval($_POST['quantite'][$i]);
                        $uniteId = !empty($_POST['unite_id'][$i]) ? intval($_POST['unite_id'][$i]) : null;
                        
                        $this->recetteModel->addIngredient($id, $ingredientId, $quantite, $uniteId);
                    }
                }
            }
            
            // Enregistrement de l'action dans le journal
            $this->recetteModel->logAdminAction($_SESSION['admin']['id'], 'recette', $id, 'modification');
            
            $this->redirect(RACINE_SITE . 'admin/recettes?info=Recette mise à jour avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/recettes/edit/' . $id . '?info=Erreur lors de la mise à jour de la recette.&type=danger');
        }
    }
    
    /**
     * Supprime une recette
     */
    public function delete($id) {
        // Vérifier que c'est une méthode POST pour éviter les suppressions accidentelles
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/recettes');
            exit();
        }

        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }
        
        $result = $this->recetteModel->delete($id);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->recetteModel->logAdminAction($_SESSION['admin']['id'], 'recette', $id, 'suppression');
            
            $this->redirect(RACINE_SITE . 'admin/recettes?info=Recette supprimée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/recettes?info=Erreur lors de la suppression de la recette.&type=danger');
        }
    }
    
    /**
     * Gère l'upload d'image
     */
    private function handleImageUpload($recetteId = null) {
        if (!isset($_FILES['image_url']) || $_FILES['image_url']['error'] !== 0) {
            return null;
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image_url']['name'];
        $tmp = explode('.', $filename);
        $ext = strtolower(end($tmp));
        
        if (!in_array($ext, $allowed)) {
            return null;
        }
        
        $prefix = $recetteId ? 'recette_' . $recetteId . '_' : 'recette_';
        $newFilename = $prefix . time() . '.' . $ext;
        $uploadDir = ROOT_DIR . '/public/assets/recettes/images/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $destination = $uploadDir . $newFilename;
        
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $destination)) {
            return 'images/' . $newFilename;
        }
        
        return null;
    }
}