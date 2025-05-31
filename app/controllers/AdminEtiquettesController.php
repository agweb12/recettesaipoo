<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Etiquettes;
use App\Utils\Utils;

class AdminEtiquettesController extends Controller {
    private $etiquetteModel;
    
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
        
        $this->etiquetteModel = new Etiquettes();
    }
    
    /**
     * Affiche la liste des étiquettes
     */
    public function index() {
        // Récupération de la liste des étiquettes avec le nombre de recettes associées
        $etiquettes = $this->etiquetteModel->getAllEtiquettesWithUsage();
        
        // Message de notification (info/erreur)
        $info = isset($_GET['info']) ? htmlspecialchars($_GET['info']) : '';
        $infoType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
        
        $this->view('admin/etiquettes/index', [
            'titlePage' => "Gestion des Étiquettes",
            'descriptionPage' => "Gérer les étiquettes disponibles dans Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'etiquettes' => $etiquettes,
            'info' => $info,
            'infoType' => $infoType,
            'etiquetteEdit' => null
        ]);
    }
    
    /**
     * Affiche le formulaire d'édition d'une étiquette
     */
    public function edit($id) {
        $etiquette = $this->etiquetteModel->findById($id);
        
        if (!$etiquette) {
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Étiquette introuvable.&type=danger');
            exit();
        }
        
        // Récupération de la liste des étiquettes avec le nombre de recettes associées
        $etiquettes = $this->etiquetteModel->getAllEtiquettesWithUsage();
        
        $this->view('admin/etiquettes/index', [
            'titlePage' => "Modifier une étiquette",
            'descriptionPage' => "Modifier une étiquette existante",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'etiquettes' => $etiquettes,
            'etiquetteEdit' => $etiquette,
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'info'
        ]);
    }
    
    /**
     * Traite le formulaire d'ajout d'une étiquette
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/etiquettes');
            exit();
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        $descriptif = Utils::sanitize($_POST['descriptif']);
        
        // Vérification des champs
        if (empty($nom)) {
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Le nom de l\'étiquette est obligatoire.&type=danger');
            exit();
        }
        
        // Vérifier si le nom existe déjà
        if ($this->etiquetteModel->existsByName($nom)) {
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Une étiquette avec ce nom existe déjà.&type=danger');
            exit();
        }
        
        // Insertion de la nouvelle étiquette
        $etiquetteData = [
            'nom' => $nom,
            'descriptif' => $descriptif,
            'id_admin' => $_SESSION['admin']['id']
        ];
        
        $lastId = $this->etiquetteModel->create($etiquetteData);
        
        if ($lastId) {
            // Enregistrement de l'action dans le journal
            $this->etiquetteModel->logAdminAction($_SESSION['admin']['id'], 'etiquette', $lastId, 'ajout');
            
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Nouvelle étiquette ajoutée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Erreur lors de l\'ajout de l\'étiquette.&type=danger');
        }
    }
    
    /**
     * Met à jour une étiquette
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/etiquettes');
            exit();
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        $descriptif = Utils::sanitize($_POST['descriptif']);
        
        // Vérification des champs
        if (empty($nom)) {
            $this->redirect(RACINE_SITE . 'admin/etiquettes/edit/' . $id . '?info=Le nom de l\'étiquette est obligatoire.&type=danger');
            exit();
        }
        
        // Vérifier si le nom existe déjà (sauf pour l'étiquette en cours)
        if ($this->etiquetteModel->existsByNameExcept($nom, $id)) {
            $this->redirect(RACINE_SITE . 'admin/etiquettes/edit/' . $id . '?info=Une étiquette avec ce nom existe déjà.&type=danger');
            exit();
        }
        
        // Mise à jour de l'étiquette
        $etiquetteData = [
            'nom' => $nom,
            'descriptif' => $descriptif,
            'id_admin' => $_SESSION['admin']['id']
        ];
        
        $result = $this->etiquetteModel->update($id, $etiquetteData);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->etiquetteModel->logAdminAction($_SESSION['admin']['id'], 'etiquette', $id, 'modification');
            
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Étiquette mise à jour avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/etiquettes/edit/' . $id . '?info=Erreur lors de la mise à jour de l\'étiquette.&type=danger');
        }
    }
    
    /**
     * Supprime une étiquette
     */
    public function delete($id) {
        // Vérifier si l'étiquette est utilisée dans des recettes
        if ($this->etiquetteModel->isUsedInRecipes($id)) {
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Cette étiquette est utilisée dans une ou plusieurs recettes et ne peut pas être supprimée.&type=warning');
            exit();
        }
        
        $result = $this->etiquetteModel->delete($id);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->etiquetteModel->logAdminAction($_SESSION['admin']['id'], 'etiquette', $id, 'suppression');
            
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Étiquette supprimée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/etiquettes?info=Erreur lors de la suppression de l\'étiquette.&type=danger');
        }
    }
}