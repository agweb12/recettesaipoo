<?php
// public/index.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session AVANT tout output
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


define("ROOT_DIR", dirname(__DIR__)); // Définir le répertoire racine de l'application
define("RACINE_SITE", "http://localhost/recettesaipoo/"); // Définir la racine du site

// Autoloader / fonction d'autoload des classes
spl_autoload_register(function($className) { // spl_autoload_register est une fonction PHP qui permet de charger automatiquement les classes
    // Remplacer les backslashes par des slashes pour la compatibilité avec les chemins de fichiers
    $className = str_replace('\\', '/', $className);

    // Supprimer le préfixe "App/" du chemin de classe
    if (strpos($className, 'App/') === 0) {
        $className = substr($className, 4); // Retirer "App/" du début du nom de la classe
    }

    $file = ROOT_DIR . '/app/' . $className . '.php';

    if(file_exists($file)) {
        require_once $file;
        return;
    }

    // Ne pas afficher les messages de debug en production
    // echo "Classe non trouvée: $className<br>";
    // echo "Chemin recherché: " . $file . '<br>';
});

require_once ROOT_DIR . '/app/helpers/functions.php';

// Initialiser le routeur
$router = new \App\Core\Router(); // instanciation de la classe Router

// Définir les routes
$router->add('', '\App\Controllers\HomeController', 'index', 'ANY');
$router->add('accueil', '\App\Controllers\HomeController', 'index', 'ANY');
$router->add('recettes', '\App\Controllers\RecettesController', 'index');
$router->add('recettes/recette', '\App\Controllers\RecettesController', 'show');
$router->add('contact', '\App\Controllers\StaticPagesController', 'contact');

// Routes d'authentification : accepter GET et POST
$router->add('connexion', '\App\Controllers\AuthController', 'login', 'ANY');
$router->add('inscription', '\App\Controllers\AuthController', 'register', 'ANY');
$router->add('deconnexion', '\App\Controllers\AuthController', 'logout');

$router->add('profil/monCompte', '\App\Controllers\ProfileController', 'account', 'ANY');
$router->add('mentions-legales', '\App\Controllers\StaticPagesController', 'mentionsLegales');
$router->add('politique-confidentialite', '\App\Controllers\StaticPagesController', 'politiqueConfidentialite');
$router->add('cgu', '\App\Controllers\StaticPagesController', 'cgu');
$router->add('api/favoris', '\App\Controllers\ApiController', 'favoris', 'POST');
$router->add('api/ingredients', '\App\Controllers\ApiController', 'searchIngredientsAuto');

// Routes pour l'administration
$router->add('admin/login', '\App\Controllers\AdminAuthController', 'login', 'ANY');
$router->add('admin/logout', '\App\Controllers\AdminAuthController', 'logout');
$router->add('admin/dashboard', '\App\Controllers\AdminController', 'dashboard');

// Routes pour la gestion des catégories
$router->add('admin/categories', '\App\Controllers\AdminCategoriesController', 'index');
$router->add('admin/categories/create', '\App\Controllers\AdminCategoriesController', 'create', 'ANY');
$router->add('admin/categories/store', '\App\Controllers\AdminCategoriesController', 'store', 'POST');
$router->add('admin/categories/edit/([0-9]+)', '\App\Controllers\AdminCategoriesController', 'edit', 'ANY');
$router->add('admin/categories/update/([0-9]+)', '\App\Controllers\AdminCategoriesController', 'update', 'POST');
$router->add('admin/categories/delete/([0-9]+)', '\App\Controllers\AdminCategoriesController', 'delete', 'POST');

// Routes pour la gestion des étiquettes
$router->add('admin/etiquettes', '\App\Controllers\AdminEtiquettesController', 'index');
$router->add('admin/etiquettes/edit/([0-9]+)', '\App\Controllers\AdminEtiquettesController', 'edit', 'ANY');
$router->add('admin/etiquettes/store', '\App\Controllers\AdminEtiquettesController', 'store', 'POST');
$router->add('admin/etiquettes/update/([0-9]+)', '\App\Controllers\AdminEtiquettesController', 'update', 'POST');
$router->add('admin/etiquettes/delete/([0-9]+)', '\App\Controllers\AdminEtiquettesController', 'delete', 'POST');

// Routes pour la gestion des unités de mesure
$router->add('admin/unites-mesure', '\App\Controllers\AdminUnitesMesureController', 'index');
$router->add('admin/unites-mesure/edit/([0-9]+)', '\App\Controllers\AdminUnitesMesureController', 'edit', 'ANY');
$router->add('admin/unites-mesure/store', '\App\Controllers\AdminUnitesMesureController', 'store', 'POST');
$router->add('admin/unites-mesure/update/([0-9]+)', '\App\Controllers\AdminUnitesMesureController', 'update', 'POST');
$router->add('admin/unites-mesure/delete/([0-9]+)', '\App\Controllers\AdminUnitesMesureController', 'delete', 'POST');

// Routes pour la gestion des ingrédients
$router->add('admin/ingredients', '\App\Controllers\AdminIngredientsController', 'index');
$router->add('admin/ingredients/edit/([0-9]+)', '\App\Controllers\AdminIngredientsController', 'edit', 'ANY');
$router->add('admin/ingredients/store', '\App\Controllers\AdminIngredientsController', 'store', 'POST');
$router->add('admin/ingredients/update/([0-9]+)', '\App\Controllers\AdminIngredientsController', 'update', 'POST');
$router->add('admin/ingredients/delete/([0-9]+)', '\App\Controllers\AdminIngredientsController', 'delete', 'POST');

// Routes pour la gestion des recettes
$router->add('admin/recettes', '\App\Controllers\AdminRecettesController', 'index');
$router->add('admin/recettes/edit/([0-9]+)', '\App\Controllers\AdminRecettesController', 'edit', 'ANY');
$router->add('admin/recettes/store', '\App\Controllers\AdminRecettesController', 'store', 'POST');
$router->add('admin/recettes/update/([0-9]+)', '\App\Controllers\AdminRecettesController', 'update', 'POST');
$router->add('admin/recettes/delete/([0-9]+)', '\App\Controllers\AdminRecettesController', 'delete', 'POST');

// Routes pour la gestion des utilisateurs
$router->add('admin/utilisateurs', '\App\Controllers\AdminUtilisateursController', 'index');
$router->add('admin/utilisateurs/edit/([0-9]+)', '\App\Controllers\AdminUtilisateursController', 'edit', 'ANY');
$router->add('admin/utilisateurs/update/([0-9]+)', '\App\Controllers\AdminUtilisateursController', 'update', 'POST');
$router->add('admin/utilisateurs/delete/([0-9]+)', '\App\Controllers\AdminUtilisateursController', 'delete', 'POST');

// Routes pour la gestion des administrateurs
$router->add('admin/administrateurs', '\App\Controllers\AdminAdministrateursController', 'index');
$router->add('admin/administrateurs/create', '\App\Controllers\AdminAdministrateursController', 'create', 'ANY');
$router->add('admin/administrateurs/store', '\App\Controllers\AdminAdministrateursController', 'store', 'POST');
$router->add('admin/administrateurs/edit/([0-9]+)', '\App\Controllers\AdminAdministrateursController', 'edit', 'ANY');
$router->add('admin/administrateurs/update/([0-9]+)', '\App\Controllers\AdminAdministrateursController', 'update', 'POST');
$router->add('admin/administrateurs/delete/([0-9]+)', '\App\Controllers\AdminAdministrateursController', 'delete', 'POST');
$router->add('admin/administrateurs/view-actions', '\App\Controllers\AdminAdministrateursController', 'viewActions');

// Route pour les statistiques de sécurité
$router->add('admin/security/stats', '\App\Controllers\AdminAuthController', 'securityStats');

$router->add('admin/analytics', '\App\Controllers\AdminController', 'analytics');
// Dispatcher
$router->dispatch($_SERVER['REQUEST_URI']);