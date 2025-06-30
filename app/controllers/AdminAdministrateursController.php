<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Administrateurs;
use App\Utils\Utils;

class AdminAdministrateursController extends Controller {
    private $adminModel;
    
    public function __construct() {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!isset($_SESSION['admin'])) {
            $this->redirect(RACINE_SITE . 'admin/login');
            exit();
        }
        
        // Vérifier si l'utilisateur est un superadmin
        if ($_SESSION['admin']['role'] !== 'superadmin') {
            $this->redirect(RACINE_SITE . 'admin/dashboard');
            exit();
        }
        
        $this->adminModel = new Administrateurs();
    }
    
    /**
     * Affiche la liste des administrateurs
     */
    public function index() {
        $admins = $this->adminModel->getAllAdmins();
        
        // Message de notification (info/erreur)
        $info = isset($_GET['info']) ? htmlspecialchars($_GET['info']) : '';
        $infoType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        
        // Vérifier s'il faut afficher les actions des administrateurs
        $actions = [];
        if (isset($_GET['action']) && $_GET['action'] === 'viewActions') {
            $actions = $this->adminModel->getAdminActions($_SESSION['admin']['id']);
        }
        
        $this->view('admin/administrateurs/index', [
            'titlePage' => "Gestion des Administrateurs",
            'descriptionPage' => "Gérer les administrateurs de Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'admins' => $admins,
            'actions' => $actions,
            'info' => $info,
            'infoType' => $infoType,
            'adminEdit' => null,
            'viewActions' => isset($_GET['action']) && $_GET['action'] === 'viewActions'
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'un administrateur
     */
    public function create() {
        $admins = $this->adminModel->getAllAdmins();
        
        $this->view('admin/administrateurs/index', [
            'titlePage' => "Ajouter un administrateur",
            'descriptionPage' => "Ajouter un nouvel administrateur dans Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'admins' => $admins,
            'adminEdit' => null,
            'actions' => [],
            'isCreating' => true,
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'success'
        ]);
    }
    
    /**
     * Affiche le formulaire d'édition d'un administrateur
     */
    public function edit($id) {
        $admin = $this->adminModel->findById($id);
        
        if (!$admin) {
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Administrateur introuvable.&type=danger');
            exit();
        }
        
        // Récupération de la liste des administrateurs pour le tableau
        $admins = $this->adminModel->getAllAdmins();
        
        $this->view('admin/administrateurs/index', [
            'titlePage' => "Modifier un administrateur",
            'descriptionPage' => "Modifier un administrateur existant",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'admins' => $admins,
            'adminEdit' => $admin,
            'actions' => [],
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'success'
        ]);
    }
    
    /**
     * Traite le formulaire de création d'un administrateur
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/administrateurs');
            exit();
        }
        
        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }

        $nom = Utils::sanitize($_POST['nom']);
        $prenom = Utils::sanitize($_POST['prenom']);
        $email = Utils::sanitize($_POST['email']);
        $motDePasse = Utils::sanitize($_POST['mot_de_passe']);
        $role = Utils::sanitize($_POST['role']);
        
        // Validation des données
        $errors = $this->validateAdminData($nom, $prenom, $email, $motDePasse, true);
        
        if (!empty($errors)) {
            $this->redirect(RACINE_SITE . 'admin/administrateurs/create?info=' . urlencode(implode(" ", $errors)) . '&type=danger');
            exit();
        }
        
        // Vérifier si l'email existe déjà
        if ($this->adminModel->emailExists($email)) {
            $this->redirect(RACINE_SITE . 'admin/administrateurs/create?info=Cette adresse email est déjà utilisée.&type=danger');
            exit();
        }
        
        // Formater le nom et le prénom
        $nom = strtoupper($nom);
        $prenom = ucfirst(strtolower($prenom));
        
        // Hashage du mot de passe
        $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);
        
        // Préparation des données de l'administrateur
        $adminData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $hashedPassword,
            'role' => $role
        ];
        
        // Création de l'administrateur
        $adminId = $this->adminModel->create($adminData);
        
        if ($adminId) {
            // Enregistrement de l'action dans le journal
            $this->adminModel->logAdminAction($_SESSION['admin']['id'], 'administrateur', $adminId, 'ajout');
            
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Nouvel administrateur ajouté avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/administrateurs/create?info=Erreur lors de l\'ajout de l\'administrateur.&type=danger');
        }
    }
    
    /**
     * Met à jour un administrateur
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/administrateurs');
            exit();
        }

        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }

        $nom = Utils::sanitize($_POST['nom']);
        $prenom = Utils::sanitize($_POST['prenom']);
        $email = Utils::sanitize($_POST['email']);
        $motDePasse = isset($_POST['mot_de_passe']) ? Utils::sanitize($_POST['mot_de_passe']) : '';
        $role = isset($_POST['role']) ? Utils::sanitize($_POST['role']) : null;
        
        // Validation des données (pas besoin de mot de passe requis pour la mise à jour)
        $errors = $this->validateAdminData($nom, $prenom, $email, $motDePasse, false);
        
        if (!empty($errors)) {
            $this->redirect(RACINE_SITE . 'admin/administrateurs/edit/' . $id . '?info=' . urlencode(implode(" ", $errors)) . '&type=danger');
            exit();
        }
        
        // Vérifier si l'email existe déjà pour un autre administrateur
        if ($this->adminModel->emailExistsForOthers($email, $id)) {
            $this->redirect(RACINE_SITE . 'admin/administrateurs/edit/' . $id . '?info=Cette adresse email est déjà utilisée par un autre administrateur.&type=danger');
            exit();
        }
        
        // Formater le nom et le prénom
        $nom = strtoupper($nom);
        $prenom = ucfirst(strtolower($prenom));
        
        // Préparation des données de l'administrateur
        $adminData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email
        ];
        
        // Traitement spécial pour le rôle (uniquement si l'admin est superadmin et ne modifie pas son propre compte)
        if ($_SESSION['admin']['role'] === 'superadmin' && $id != $_SESSION['admin']['id'] && $role !== null) {
            $adminData['role'] = $role;
        }
        
        // Si un nouveau mot de passe est fourni, on le hash
        if (!empty($motDePasse)) {
            $adminData['mot_de_passe'] = password_hash($motDePasse, PASSWORD_DEFAULT);
        }
        
        // Mise à jour de l'administrateur
        $result = $this->adminModel->update($id, $adminData);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->adminModel->logAdminAction($_SESSION['admin']['id'], 'administrateur', $id, 'modification');
            
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Administrateur mis à jour avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/administrateurs/edit/' . $id . '?info=Erreur lors de la mise à jour de l\'administrateur.&type=danger');
        }
    }
    
    /**
     * Supprime un administrateur
     */
    public function delete($id) {
        // Vérifier si la méthode est POST pour éviter les suppressions accidentelles
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/administrateurs');
            exit();
        }

        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }
        
        // Vérifier qu'un administrateur ne tente pas de supprimer son propre compte
        if ($id == $_SESSION['admin']['id']) {
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Vous ne pouvez pas supprimer votre propre compte.&type=warning');
            exit();
        }
        
        // Vérifier le rôle de l'administrateur à supprimer
        $adminToDelete = $this->adminModel->findById($id);
        
        if (!$adminToDelete) {
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Administrateur introuvable.&type=danger');
            exit();
        }
        
        // Seul un superadmin peut supprimer un autre superadmin
        if ($adminToDelete['role'] === 'superadmin' && $_SESSION['admin']['role'] !== 'superadmin') {
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Vous n\'avez pas les droits pour supprimer un superadmin.&type=warning');
            exit();
        }
        
        // Suppression de l'administrateur
        $result = $this->adminModel->delete($id);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->adminModel->logAdminAction($_SESSION['admin']['id'], 'administrateur', $id, 'suppression');
            
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Administrateur supprimé avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/administrateurs?info=Erreur lors de la suppression de l\'administrateur.&type=danger');
        }
    }
    
    /**
     * Affiche les actions d'un administrateur
     */
    public function viewActions() {
        $adminId = $_SESSION['admin']['id'];
        $actions = $this->adminModel->getAdminActions($adminId);
        
        $admins = $this->adminModel->getAllAdmins();
        
        $this->view('admin/administrateurs/index', [
            'titlePage' => "Actions des administrateurs",
            'descriptionPage' => "Journal des actions des administrateurs",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'admins' => $admins,
            'actions' => $actions,
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'success',
            'adminEdit' => null,
            'viewActions' => true
        ]);
    }
    
    /**
     * Valide les données d'un administrateur
     * @param string $nom Le nom
     * @param string $prenom Le prénom
     * @param string $email L'email
     * @param string $motDePasse Le mot de passe
     * @param bool $isPasswordRequired Indique si le mot de passe est requis
     * @return array Les erreurs de validation
     */
    private function validateAdminData($nom, $prenom, $email, $motDePasse, $isPasswordRequired = true) {
        $errors = [];
        
        // Validation du nom
        $regexNom = "/^\p{L}[\p{L}\s-]*$/u";
        if (!preg_match($regexNom, $nom)) {
            $errors[] = "Le nom ne doit contenir que des lettres, des espaces et des tirets.";
        } elseif (strlen($nom) > 50) {
            $errors[] = "Le nom ne doit pas dépasser 50 caractères.";
        } elseif (strlen($nom) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères.";
        }
        
        // Validation du prénom
        $regexPrenom = "/^\p{L}[\p{L}\s-]*$/u";
        if (!preg_match($regexPrenom, $prenom)) {
            $errors[] = "Le prénom ne doit contenir que des lettres, des espaces et des tirets.";
        } elseif (strlen($prenom) > 50) {
            $errors[] = "Le prénom ne doit pas dépasser 50 caractères.";
        } elseif (strlen($prenom) < 2) {
            $errors[] = "Le prénom doit contenir au moins 2 caractères.";
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        } elseif (strlen($email) > 100) {
            $errors[] = "L'email ne doit pas dépasser 100 caractères.";
        } elseif (strlen($email) < 5) {
            $errors[] = "L'email doit contenir au moins 5 caractères.";
        }
        
        // Validation du mot de passe si requis ou si fourni
        if ($isPasswordRequired || !empty($motDePasse)) {
            // Règles strictes pour les mots de passe
            $regexPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/";
            if (!preg_match($regexPassword, $motDePasse)) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            } elseif (strlen($motDePasse) > 255) {
                $errors[] = "Le mot de passe ne doit pas dépasser 255 caractères.";
            }
        }
        
        return $errors;
    }
}