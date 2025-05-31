<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ingredients;
use App\Utils\Utils;

class AdminIngredientsController extends Controller {
    private $ingredientModel;
    
    public function __construct() {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!isset($_SESSION['admin'])) {
            $this->redirect(RACINE_SITE . 'admin/login');
            exit();
        }
        
        // Vérifier les droits d'accès (page réservée aux superadmin et modérateurs)
        if ($_SESSION['admin']['role'] !== 'superadmin' && $_SESSION['admin']['role'] !== 'moderateur') {
            $this->redirect(RACINE_SITE . 'admin/dashboard');
            exit();
        }
        
        $this->ingredientModel = new Ingredients();
    }
    
    /**
     * Affiche la liste des ingrédients
     */
    public function index() {
        // Récupération de la liste des ingrédients avec leur utilisation
        $ingredients = $this->ingredientModel->getAllIngredientsWithUsage();
        
        // Message de notification (info/erreur)
        $info = isset($_GET['info']) ? htmlspecialchars($_GET['info']) : '';
        $infoType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
        
        $this->view('admin/ingredients/index', [
            'titlePage' => "Gestion des Ingrédients",
            'descriptionPage' => "Gérer les ingrédients disponibles dans Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'ingredients' => $ingredients,
            'info' => $info,
            'infoType' => $infoType,
            'ingredientEdit' => null
        ]);
    }
    
    /**
     * Affiche le formulaire d'édition d'un ingrédient
     */
    public function edit($id) {
        $ingredient = $this->ingredientModel->findById($id);
        
        if (!$ingredient) {
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Ingrédient introuvable.&type=danger');
            exit();
        }
        
        // Récupération de la liste des ingrédients avec leur utilisation
        $ingredients = $this->ingredientModel->getAllIngredientsWithUsage();
        
        $this->view('admin/ingredients/index', [
            'titlePage' => "Modifier un ingrédient",
            'descriptionPage' => "Modifier un ingrédient existant",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'ingredients' => $ingredients,
            'ingredientEdit' => $ingredient,
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'info'
        ]);
    }
    
    /**
     * Traite le formulaire d'ajout d'un ingrédient
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/ingredients');
            exit();
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        
        // Vérification des champs
        if (empty($nom)) {
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Le nom de l\'ingrédient est obligatoire.&type=danger');
            exit();
        }
        
        // Vérifier si le nom existe déjà
        if ($this->ingredientModel->existsByName($nom)) {
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Un ingrédient avec ce nom existe déjà.&type=danger');
            exit();
        }
        
        // Insertion du nouvel ingrédient
        $ingredientData = [
            'nom' => $nom,
            'id_admin' => $_SESSION['admin']['id']
        ];
        
        $lastId = $this->ingredientModel->create($ingredientData);
        
        if ($lastId) {
            // Enregistrement de l'action dans le journal
            $this->ingredientModel->logAdminAction($_SESSION['admin']['id'], 'ingredient', $lastId, 'ajout');
            
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Nouvel ingrédient ajouté avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Erreur lors de l\'ajout de l\'ingrédient.&type=danger');
        }
    }
    
    /**
     * Met à jour un ingrédient
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/ingredients');
            exit();
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        
        // Vérification des champs
        if (empty($nom)) {
            $this->redirect(RACINE_SITE . 'admin/ingredients/edit/' . $id . '?info=Le nom de l\'ingrédient est obligatoire.&type=danger');
            exit();
        }
        
        // Vérifier si le nom existe déjà (sauf pour l'ingrédient en cours)
        if ($this->ingredientModel->existsByNameExcept($nom, $id)) {
            $this->redirect(RACINE_SITE . 'admin/ingredients/edit/' . $id . '?info=Un ingrédient avec ce nom existe déjà.&type=danger');
            exit();
        }
        
        // Mise à jour de l'ingrédient
        $ingredientData = [
            'nom' => $nom,
            'id_admin' => $_SESSION['admin']['id']
        ];
        
        $result = $this->ingredientModel->update($id, $ingredientData);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->ingredientModel->logAdminAction($_SESSION['admin']['id'], 'ingredient', $id, 'modification');
            
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Ingrédient mis à jour avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/ingredients/edit/' . $id . '?info=Erreur lors de la mise à jour de l\'ingrédient.&type=danger');
        }
    }
    
    /**
     * Supprime un ingrédient
     */
    public function delete($id) {
        // Vérifie si la méthode est POST et non GET pour sécuriser la suppression
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/ingredients');
            exit();
        }
        
        // Vérifier si l'ingrédient est utilisé dans des recettes
        if ($this->ingredientModel->isUsedInRecipes($id)) {
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Cet ingrédient est utilisé dans une ou plusieurs recettes et ne peut pas être supprimé.&type=warning');
            exit();
        }
        
        // Vérifier si l'ingrédient est dans des listes personnelles
        if ($this->ingredientModel->isInPersonalLists($id)) {
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Cet ingrédient est présent dans les listes personnelles des utilisateurs et ne peut pas être supprimé.&type=warning');
            exit();
        }
        
        // Supprimer l'ingrédient
        $result = $this->ingredientModel->delete($id);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->ingredientModel->logAdminAction($_SESSION['admin']['id'], 'ingredient', $id, 'suppression');
            
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Ingrédient supprimé avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/ingredients?info=Erreur lors de la suppression de l\'ingrédient.&type=danger');
        }
    }
}