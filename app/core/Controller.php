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

        // Détermine s'il s'agit d'une vue admin ou non
        $isAdminView = strpos($view, 'admin/') === 0;

        // Inclure l'en-tête approprié
        if ($isAdminView) {
            include_once ROOT_DIR . '/app/views/headerAdmin.php';
        } else {
            include_once ROOT_DIR . '/app/views/header.php';
        }

        // Si la vue n'est pas celle de connexion ou d'inscription et que l'utilisateur n'est pas connecté,
        // incluez la modal de connexion
        if (!$isAdminView && $view !== 'connexion' && $view !== 'inscription' && !$this->isLoggedIn()) {
            include_once ROOT_DIR . '/app/views/modalConnexion.php';
        }

        // Inclusion du contenu principal
        include_once ROOT_DIR . '/app/views/' . $view . '.php';

        // Inclusion du pied de page approprié
        if ($isAdminView) {
            include_once ROOT_DIR . '/app/views/footerAdmin.php';
        } else {
            include_once ROOT_DIR . '/app/views/footer.php';
        }
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