<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Utilisateurs;
use App\Utils\Utils;

class AdminUtilisateursController extends Controller {
    private $utilisateurModel;
    
    public function __construct() {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!isset($_SESSION['admin'])) {
            $this->redirect(RACINE_SITE . 'admin/login');
            exit();
        }
        
        // Vérification supplémentaire des droits d'accès si nécessaire
        // Par exemple, restreindre aux administrateurs avec un certain rôle
        
        $this->utilisateurModel = new Utilisateurs();
    }
    
    /**
     * Affiche la liste des utilisateurs
     */
    public function index() {
        // Récupération de la liste des utilisateurs
        $users = $this->utilisateurModel->getAllUsers();
        
        // Message de notification (info/erreur)
        $info = isset($_GET['info']) ? htmlspecialchars($_GET['info']) : '';
        $infoType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        
        $this->view('admin/utilisateurs/index', [
            'titlePage' => "Gestion des Utilisateurs",
            'descriptionPage' => "Gérer les utilisateurs de l'application Recette AI.",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'users' => $users,
            'info' => $info,
            'infoType' => $infoType,
            'utilisateurEdit' => null
        ]);
    }
    
    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit($id) {
        $utilisateur = $this->utilisateurModel->findById($id);
        
        if (!$utilisateur) {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs?info=Utilisateur introuvable.&type=danger');
            exit();
        }
        
        // Récupération de la liste des utilisateurs pour le tableau
        $users = $this->utilisateurModel->getAllUsers();
        
        $this->view('admin/utilisateurs/index', [
            'titlePage' => "Modifier un utilisateur",
            'descriptionPage' => "Modifier un utilisateur existant",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'users' => $users,
            'utilisateurEdit' => $utilisateur,
            'info' => isset($_GET['info']) ? $_GET['info'] : '',
            'infoType' => isset($_GET['type']) ? $_GET['type'] : 'success'
        ]);
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs');
            exit();
        }
        
        $nom = Utils::sanitize($_POST['nom']);
        $prenom = Utils::sanitize($_POST['prenom']);
        $email = Utils::sanitize($_POST['email']);
        $motDePasse = isset($_POST['mot_de_passe']) ? Utils::sanitize($_POST['mot_de_passe']) : '';
        
        // Vérification des champs
        if (empty($nom) || empty($prenom) || empty($email)) {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs/edit/' . $id . '?info=Veuillez remplir tous les champs obligatoires.&type=danger');
            exit();
        }
        
        // Vérifier si l'email existe déjà pour un autre utilisateur
        if ($this->utilisateurModel->emailExistsForOthers($email, $id)) {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs/edit/' . $id . '?info=Cette adresse email est déjà utilisée par un autre utilisateur.&type=danger');
            exit();
        }
        
        // Préparation des données de l'utilisateur
        $userData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email
        ];
        
        // Si un nouveau mot de passe est fourni, on le hash
        if (!empty($motDePasse)) {
            $userData['mot_de_passe'] = password_hash($motDePasse, PASSWORD_DEFAULT);
        }
        
        // Mise à jour de l'utilisateur
        $result = $this->utilisateurModel->updateUser($id, $userData);
        
        if ($result) {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs?info=Utilisateur mis à jour avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs/edit/' . $id . '?info=Erreur lors de la mise à jour de l\'utilisateur.&type=danger');
        }
    }
    
    /**
     * Supprime un utilisateur
     */
    public function delete($id) {
        // Vérifier si la méthode est POST pour éviter les suppressions accidentelles via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs');
            exit();
        }
        
        // Vérifier si l'utilisateur existe
        $utilisateur = $this->utilisateurModel->findById($id);
        if (!$utilisateur) {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs?info=Utilisateur introuvable.&type=danger');
            exit();
        }
        
        // Supprimer l'utilisateur et toutes ses données associées
        $result = $this->utilisateurModel->deleteUser($id);
        
        if ($result) {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs?info=Utilisateur supprimé avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/utilisateurs?info=Erreur lors de la suppression de l\'utilisateur.&type=danger');
        }
    }
}