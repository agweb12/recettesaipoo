<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UnitesMesure;
use App\Utils\Utils;

class AdminUnitesMesureController extends Controller {
    private $uniteMesureModel;
    
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
        
        $this->uniteMesureModel = new UnitesMesure();
    }
    
    /**
     * Affiche la liste des unités de mesure
     */
    public function index() {
        // Récupération de la liste des unités de mesure avec leur utilisation
        $unites = $this->uniteMesureModel->getAllUnitesMesureWithUsage();
        
        // Message de notification (info/erreur)
        $info = isset($_GET['info']) ? htmlspecialchars($_GET['info']) : '';
        $infoType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
        
        $this->view('admin/unites-mesure/index', [
            'titlePage' => "Gestion des Unités de Mesure",
            'descriptionPage' => "Gérer les unités de mesure disponibles dans Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'unites' => $unites,
            'info' => $info,
            'infoType' => $infoType,
            'uniteEdit' => null
        ]);
    }
    
    /**
     * Affiche le formulaire d'édition d'une unité de mesure
     */
    public function edit($id) {
        $unite = $this->uniteMesureModel->findById($id);
        
        if (!$unite) {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Unité de mesure introuvable.&type=danger');
            exit();
        }
        
        // Récupération de la liste des unités de mesure avec leur utilisation
        $unites = $this->uniteMesureModel->getAllUnitesMesureWithUsage();
        
        $this->view('admin/unites-mesure/index', [
            'titlePage' => "Modifier une unité de mesure",
            'descriptionPage' => "Modifier une unité de mesure existante",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'unites' => $unites,
            'uniteEdit' => $unite,
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'info'
        ]);
    }
    
    /**
     * Traite le formulaire d'ajout d'une unité de mesure
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure');
            exit();
        }

        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        $abreviation = Utils::sanitize($_POST['abreviation']);
        
        // Vérification des champs
        if (empty($nom) || empty($abreviation)) {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Tous les champs sont obligatoires.&type=danger');
            exit();
        }
        
        // Vérifier si le nom ou l'abréviation existe déjà
        if ($this->uniteMesureModel->existsByNameOrAbbreviation($nom, $abreviation)) {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Une unité de mesure avec ce nom ou cette abréviation existe déjà.&type=danger');
            exit();
        }
        
        // Insertion de la nouvelle unité
        $uniteData = [
            'nom' => $nom,
            'abreviation' => $abreviation
        ];
        
        $lastId = $this->uniteMesureModel->create($uniteData);
        
        if ($lastId) {

            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Nouvelle unité de mesure ajoutée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Erreur lors de l\'ajout de l\'unité de mesure.&type=danger');
        }
    }
    
    /**
     * Met à jour une unité de mesure
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure');
            exit();
        }

        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        $abreviation = Utils::sanitize($_POST['abreviation']);
        
        // Vérification des champs
        if (empty($nom) || empty($abreviation)) {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure/edit/' . $id . '?info=Tous les champs sont obligatoires.&type=danger');
            exit();
        }
        
        // Vérifier si le nom ou l'abréviation existe déjà (sauf pour l'unité en cours)
        if ($this->uniteMesureModel->existsByNameOrAbbreviationExcept($nom, $abreviation, $id)) {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure/edit/' . $id . '?info=Une unité de mesure avec ce nom ou cette abréviation existe déjà.&type=danger');
            exit();
        }
        
        // Mise à jour de l'unité
        $uniteData = [
            'nom' => $nom,
            'abreviation' => $abreviation
        ];
        
        $result = $this->uniteMesureModel->update($id, $uniteData);
        
        if ($result) {

            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Unité de mesure mise à jour avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure/edit/' . $id . '?info=Erreur lors de la mise à jour de l\'unité de mesure.&type=danger');
        }
    }
    
    /**
     * Supprime une unité de mesure
     */
    public function delete($id) {
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure');
            exit();
        }
        
        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }
        
        // Vérifier si l'unité est utilisée dans des recettes
        if ($this->uniteMesureModel->isUsedInRecipes($id)) {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Cette unité de mesure est utilisée dans des recettes et ne peut pas être supprimée.&type=warning');
            exit();
        }
        
        $result = $this->uniteMesureModel->delete($id);
        
        if ($result) {
            
            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Unité de mesure supprimée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/unites-mesure?info=Erreur lors de la suppression de l\'unité de mesure.&type=danger');
        }
    }
}