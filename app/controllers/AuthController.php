<?php
// app/controllers/AuthController.php
namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller {
    
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

        $errorGeneral = "";
        $errorEmail = "";
        $errorMdp = "";

        // Traitement du formulaire de connexion
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->processLogin($errorGeneral, $errorEmail, $errorMdp);
        }

        // Chargement de la vue de connexion
        $this->view('connexion', [
            'titlePage' => 'Se connecter',
            'descriptionPage' => 'Se connecter pour accéder à votre compte Recette AI.',
            'indexPage' => 'index',
            'followPage' => 'follow',
            'keywordsPage' => 'connexion, recette AI, se connecter',
            'errorGeneral' => $errorGeneral,
            'errorEmail' => $errorEmail,
            'errorMdp' => $errorMdp
        ]);
    }

    public function register(): void
    {
        // Si l'utilisateur est déjà connecté, rediriger vers l'accueil
        if($this->isLoggedIn()){
            $this->redirect(RACINE_SITE);
            return;
        }

        $info = "";
        $errorGeneral = "";
        $errorNom = "";
        $errorPrenom = "";
        $errorEmail = "";
        $errorMdp = "";

        // Traitement du formulaire d'inscription
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this-processRegistration($info, $errorGeneral, $errorGeneral, $errorNom, $errorPrenom, $errorEmail, $errorMdp);
        }

        // Chargement de la vue d'inscription
        $this->view('inscription', [
            'titlePage' => "S'inscrire",
            'descriptionPage' => "S'inscrire sur Recette AI, pour trouver des recettes de cuisine en fonction des ingrédients que vous avez chez vous.",
            'indexPage' => "index",
            'followPage' => "follow",
            'info' => $info,
            'errorGeneral' => $errorGeneral,
            'errorNom' => $errorNom,
            'errorPrenom' => $errorPrenom,
            'errorEmail' => $errorEmail,
            'errorMdp' => $errorMdp
        ]);
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->redirect(RACINE_SITE);
    }

    public function modalLogin() 
    {
        // Traitement AJAX pour la connexion via modal
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $errorGeneral = "";
            $errorEmail = "";
            $errorMdp = "";

            $result = $this->processModalLogin();

            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }

    private function processLogin(&$errorGeneral, &$errorEmail, &$errorMdp): void
    {
        $email = htmlspecialchars($_POST['email']);
        $mdp = htmlspecialchars($_POST['password']);
        $verification = true;

        // Validation des champs
        if(empty(trim($email)) || empty(trim($mdp))){
            $verification = false;
            $errorGeneral = $this->alert("Veuillez remplir tous les champs", "danger");
        } else{
            // Validation de l'email
            if(!isset($email)){
                $verification = false;
                $errorEmail = $this->alert("Le champ email est vide", "danger");
            }
            else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $verification = false;
                $errorEmail = $this->alert("L'email n'est pas valide", "danger");
            }

            // Validation du mot de passe
            
        }
    }
}