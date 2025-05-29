<?php
// public/index.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Démarrer la session

define("ROOT_DIR", dirname(__DIR__)); // Définir le répertoire racine de l'application
define("RACINE_SITE", "http://localhost/recettesaipoo/"); // Définir la racine du site

// Autoloader / fonction d'autoload des classes
spl_autoload_register(function($className) {
    // Remplacer les backslashes par des slashes pour la compatibilité avec les chemins de fichiers
    $className = str_replace('\\', '/', $className);

    // Supprimer le préfixe "App/" du chemin de classe
    if (strpos($className, 'App/') === 0) {
        $className = substr($className, 4);
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

// Initialiser le routeur
$router = new \App\Core\Router();

// Définir les routes
$router->add('', '\App\Controllers\HomeController', 'index');
$router->add('accueil', '\App\Controllers\HomeController', 'index');
$router->add('recettes', '\App\Controllers\RecettesController', 'index');
$router->add('recettes/recette', '\App\Controllers\RecettesController', 'show');
$router->add('contact', '\App\Controllers\StaticPagesController', 'contact');
$router->add('connexion', '\App\Controllers\AuthController', 'login');
$router->add('inscription', '\App\Controllers\AuthController', 'register');
$router->add('deconnexion', '\App\Controllers\AuthController', 'logout');
$router->add('profil/monCompte', '\App\Controllers\ProfileController', 'account');
$router->add('mentions-legales', '\App\Controllers\StaticPagesController', 'mentionsLegales');
$router->add('politique-confidentialite', '\App\Controllers\StaticPagesController', 'politiqueConfidentialite');
$router->add('cgu', '\App\Controllers\StaticPagesController', 'cgu');
$router->add('api/favoris', '\App\Controllers\ApiController', 'favoris');
$router->add('api/ingredients', '\App\Controllers\ApiController', 'searchIngredientsAuto');
// Ajouter d'autres routes

// Dispatcher
$router->dispatch($_SERVER['REQUEST_URI']);