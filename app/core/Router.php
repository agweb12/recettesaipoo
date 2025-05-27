<?php
// app/core/Router.php
namespace App\Core; // Définition de l'espace de noms pour la classe Router
// La classe Router est responsable de la gestion des routes de l'application
class Router {
    protected $routes = []; // Tableau pour stocker les routes

    // La méthode add est utilisée pour ajouter une nouvelle route
    // Elle prend en paramètre la route, le contrôleur et l'action associés
    // Elle stocke ces informations dans le tableau $routes
    // Cette méthode est publique pour permettre aux autres classes d'ajouter des routes
    // Exemple d'utilisation : $router->add('accueil', 'HomeController', 'index');
    // Dans ce cas, 'accueil' est la route, 'HomeController' est le contrôleur et 'index' est l'action : l'action c'est la méthode du contrôleur qui sera appelée lorsque l'utilisateur accède à cette route
    // Cette méthode permet d'ajouter une route à l'application
    public function add($route, $controller, $action) { // Ajouter une route
        $this->routes[$route] = ['controller' => $controller, 'action' => $action]; // Stocker le contrôleur et l'action associés à la route
    }

    // La méthode dispatch est responsable de la gestion des requêtes entrantes
    // Elle prend en paramètre l'URL de la requête et détermine quel contrôleur et quelle action doivent être appelés
    // Cette méthode est publique pour permettre à l'application de traiter les requêtes entrantes
    public function dispatch($url) { // dispatch : c'est le processus de traitement de la requête entrante
        // Etape 1 : Nettoyer et préparer l'URL
        $url = filter_var($url, FILTER_SANITIZE_URL); // Nettoyer l'URL pour éviter les injections
        $url = parse_url($url, PHP_URL_PATH); // Extraire le chemin de l'URL et le nettoyer
        $url = trim($url, '/'); // Supprimer les barres obliques au début et à la fin de l'URL

        // Etape 2 : Supprimer le chemin de base
        $basePath = 'recettesaipoo'; // Définir le chemin de base de l'application, par exemple 'recettesaipoo'
        if(strpos($url, $basePath) === 0) { // Vérifier si l'URL commence par le chemin de base
            $url = substr($url, strlen($basePath)); // Supprimer le chemin de base de l'URL
            $url = trim($url, '/'); // Supprimer les barres obliques restantes au début et à la fin de l'URL
            // exemple : si l'URL est 'recettesaipoo/accueil', elle devient 'accueil'
        }
        
        // Si l'URL est vide, rediriger vers la page d'accueil
        if (empty($url)) {
            $url = '';
        }

        // Etape 3 : Vérifier si l'URL correspond à une route définie
        // Vérifier si la route existe
        if (array_key_exists($url, $this->routes)) {
            $controller = $this->routes[$url]['controller']; // Récupérer le contrôleur associé à la route
            $action = $this->routes[$url]['action']; // Récupérer l'action associée à la route

            if (class_exists($controller)) { // Vérifier si le contrôleur existe
                $controllerInstance = new $controller(); // Instancier le contrôleur
                
                if (method_exists($controllerInstance, $action)) { // Vérifier si l'action existe dans le contrôleur
                    $controllerInstance->$action(); // Appeler l'action du contrôleur
                } else {
                    $this->notFound("Action non trouvée: " . $action); // Si l'action n'existe pas, afficher une erreur 404
                }
            } else {
                $this->notFound("Contrôleur non trouvé: " . $controller); // Si le contrôleur n'existe pas, afficher une erreur 404
            }
        } else {
            $this->notFound("Route non trouvée: " . $url); // Si la route n'existe pas, afficher une erreur 404
        }
    }

    private function notFound($message = "Page non trouvée") {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page non trouvée</h1>";
        echo "<p>" . $message . "</p>";
        echo "<p>URL demandée: " . $_SERVER['REQUEST_URI'] . "</p>";
        echo "<p>Routes disponibles: " . implode(', ', array_keys($this->routes)) . "</p>";

    }
}