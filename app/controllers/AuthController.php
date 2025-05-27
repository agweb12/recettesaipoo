<?php
// app/controllers/AuthController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Utilisateurs;

class AuthController extends Controller {
    private $userModel;

    public function __construct(){
        $this->userModel = new Utilisateurs();
    }

    /**
     * Affiche la page de connexion et gère le traitement du formulaire de connexion.
     */
    /**
     * @return void
     */
    public function login(): void
    {
        // Si l'utilisateur est déjà connecté, rediriger vers l'accueil
        if($this->isLoggedIn()){
            $this->redirect(RACINE_SITE);
            return;
        }

        $errors = [];

        // Traitement du formulaire de connexion
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $loginData = [
                'email' => htmlspecialchars(trim($_POST['email'] ?? '')),
                'password' => htmlspecialchars(trim($_POST['password'] ?? ''))
            ];

            // Validation des données
            $errors = $this->userModel->validateLoginData($loginData); // validateLoginData est une méthode à créer dans le modèle Utilisateurs pour valider les données de connexion

            if(empty($errors)){
                // Si les données sont valides, tenter la connexion
                $user = $this->userModel->authenticate($loginData['email'], $loginData['password']); // authenticate est une méthode à créer dans le modèle Utilisateurs pour vérifier les identifiants de l'utilisateur

                if($user){
                    // Connexion réussie
                    $_SESSION['user'] = $user;

                    $this->redirect(RACINE_SITE);
                    return;
                } else {
                    $errors['general'] = $this->alert("Identifiants incorrects. Veuillez vérifier votre email et mot de passe.", "danger");
                    $errors['email'] = $this->alert("Identifiants incorrects. Veuillez vérifier votre email et mot de passe.", "danger");
                    $errors['password'] = $this->alert("Identifiants incorrects. Veuillez vérifier votre email et mot de passe.", "danger");
                }
            }
        }

        // Chargement de la vue de connexion
        $this->view('connexion', [
            'titlePage' => "Se Connecter - Recettes AI",
            'descriptionPage' => "Se connecter pour accéder à votre compte Recette AI.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "connexion, login, Recettes AI",
            'errors' => $errors,
            'formData' => $_POST ?? []
        ]);
    }

    /**
     * Affiche la page d'inscription et gère le traitement du formulaire d'inscription.
     */
    /**
     * @return void
     */
    public function register(): void
    {
        // Si l'utilisateur est déjà connecté, rediriger vers l'accueil
        if($this->isLoggedIn()){
            $this->redirect(RACINE_SITE);
            return;
        }

        $info = "";
        $errors = [];

        // Traitement du formulaire d'inscription
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $registrationData = [
                'nom' => htmlspecialchars(trim($_POST['nom'] ?? '')),
                'prenom' => htmlspecialchars(trim($_POST['prenom'] ?? '')),
                'email' => htmlspecialchars(trim($_POST['email'] ?? '')),
                'password' => htmlspecialchars(trim($_POST['password'] ?? ''))
            ];

            // Validation des données
            $errors = $this->userModel->validateRegistrationData($registrationData);

            if (empty($errors)) {
                // Préparation des données pour l'insertion
                $userData = [
                    'nom' => strtoupper($registrationData['nom']),
                    'prenom' => ucfirst(strtolower($registrationData['prenom'])),
                    'email' => $registrationData['email'],
                    'mot_de_passe' => password_hash($registrationData['password'], PASSWORD_DEFAULT),
                    'date_inscription' => date('Y-m-d H:i:s')
                ];

                // Tentative de création de l'utilisateur
                $userId = $this->userModel->createRegistration($userData); // createRegistration est une méthode 
                
                if ($userId) {
                    $info = "Inscription réussie ! <a href='" . RACINE_SITE . "connexion' class='cta'>Connectez-vous ici</a>";
                    // Effacer les données du formulaire après succès
                    $_POST = [];
                } else {
                    $errors['general'] = "Une erreur est survenue lors de l'inscription";
                }
            }
        }

        // Chargement de la vue d'inscription
        $this->view('inscription', [
            'titlePage' => "Inscription - Recettes AI",
            'descriptionPage' => "S'inscrire sur Recette AI pour trouver des recettes selon vos ingrédients.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "inscription, register, Recettes AI",
            'info' => $info,
            'errors' => $errors,
            'formData' => $_POST ?? []
        ]);
    }

    /**
     * Déconnecte l'utilisateur en supprimant les données de session.
     * Redirige ensuite vers la page d'accueil.
     */
    /**
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->redirect(RACINE_SITE);
    }
}