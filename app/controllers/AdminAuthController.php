<?php
// app/controllers/AdminAuthController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Administrateurs;

class AdminAuthController extends Controller {
    private $adminModel;

    public function __construct() {
        // Vous devrez créer un modèle Admin
        $this->adminModel = new Administrateurs();
    }

    /**
     * Affiche la page de connexion administrateur et gère le traitement du formulaire
     * @return void
     */
    public function login() : void
    {
        // Si l'administrateur est déjà connecté, rediriger vers le dashboard
        if($this->isAdminLoggedIn()){
            $this->redirect(RACINE_SITE . 'admin/dashboard');
            return;
        }

        $errors = [];

        // Traitement du formulaire de connexion
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validation des données
            if(empty($email) || !isset($email)) {
                // Vérifier si l'email est vide ou non défini
                $errors['email'] = "L'email est requis";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "L'email n'est pas valide";
            }

            if(empty($password) || !isset($password)) {
                // Vérifier si le mot de passe est vide ou non défini
                $errors['password'] = "Le mot de passe est requis";
            } elseif(strlen($password) < 8) {
                $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères";
            }

            // Si pas d'erreurs, tenter l'authentification
            if(empty($errors)) {
                $admin = $this->adminModel->authenticate($email, $password);

                if($admin) {
                    // Connexion réussie
                    $_SESSION['admin'] = $admin;
                    $this->redirect(RACINE_SITE . 'admin/dashboard');
                    return;
                } else {
                    // Authentification échouée
                    $errors['general'] = "Email ou mot de passe incorrect";
                }
            }
        }

        // Afficher la vue de connexion avec les erreurs éventuelles
        $this->view('admin/connexion', [
            'titlePage' => "Connexion Admin - Recettes AI",
            'descriptionPage' => "Connectez-vous à l'interface d'administration de Recettes AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'errors' => $errors
        ]);
    }

    /**
     * Déconnecte l'administrateur
     * @return void
     */
    public function logout() : void
    {
        unset($_SESSION['admin']);
        $this->redirect(RACINE_SITE . 'admin/login');
    }
}