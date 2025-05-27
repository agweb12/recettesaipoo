<?php
// app/core/Controller.php
namespace App\Core;
use App\Core\Router;
class Controller {
    protected function view($view, $data = []) { // $view est le nom de la vue à charger, $data est un tableau de données à passer à la vue
        extract($data); // Extrait les données du tableau $data pour les rendre accessibles dans la vue

        // Gestion de la connexion utilisateur
        if($this->isLoggedIn()){
            $user = $_SESSION['user'];
        }

        // Inclure l'en-tête
        include_once ROOT_DIR . '/app/views/header.php';

        // Inclusion de la modal de connexion si l'utilisateur n'est pas connecté
        if(!$this->isLoggedIn()) {
            include_once ROOT_DIR . '/inc/modalConnexion.php';
        }

        // Inclusion du contenu principal
        include_once ROOT_DIR . '/app/views/' . $view . '.php';

        // Inclusion du pied de page
        include_once ROOT_DIR . '/app/views/footer.php';
    }
    
    protected function redirect($url) {
        header('Location: ' . $url);
        exit();
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user']);
    }
    
    protected function isAdminLoggedIn() {
        return isset($_SESSION['admin']);
    }

    // Méthode pour charger le contrôleur de modal de connexion
    protected function loadModalController(){
        require_once ROOT_DIR . '/inc/modalConnexionController.php';
    }
}