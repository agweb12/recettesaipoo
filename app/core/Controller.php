<?php
// app/core/Controller.php
namespace App\Core;
use App\Core\Router;
class Controller {
    protected function view($view, $data = []) { // $view est le nom de la vue à charger, $data est un tableau de données à passer à la vue
        // Toujours ajouter isLoggedIn et user aux données
        $data['isLoggedIn'] = $this->isLoggedIn();
        $data['user'] = isset($_SESSION['user']) ? $_SESSION['user'] : null;

        extract($data); // Extrait les données du tableau $data pour les rendre accessibles dans la vue

        // Inclure l'en-tête
        include_once ROOT_DIR . '/app/views/header.php';

        // définir la vue 
        if (!isset($view) || empty($view)) {
            $view = 'accueil'; // Vue par défaut si aucune vue n'est spécifiée
        }

        // Si la vue est connexion ou inscription, on n'inclut pas le modal de connexion
        if ($view !== 'connexion' && $view !== 'inscription') {
            // Inclusion de la modal de connexion si l'utilisateur n'est pas connecté
            if(!$this->isLoggedIn()) {
                include_once ROOT_DIR . '/app/views/modalConnexion.php';
            }
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
}