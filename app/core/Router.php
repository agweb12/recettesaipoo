<?php
// app/core/Router.php
namespace App\Core; // Définition de l'espace de noms pour la classe Router
// La classe Router est responsable de la gestion des routes de l'application
class Router {
    protected $routes = []; // Tableau pour stocker les routes

    /**
     * Ajoute une route à l'application
     * @param string $route La route (peut contenir des motifs pour les paramètres)
     * @param string $controller Le contrôleur à utiliser
     * @param string $action L'action (méthode) à appeler
     * @param string $method La méthode HTTP (GET, POST, etc.)
     */
    public function add($route, $controller, $action, $method = 'GET') {
        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action,
            'method' => $method
        ];
    }

    /**
     * Traite la requête entrante
     * @param string $url L'URL demandée
     */
    public function dispatch($url) {
        // Etape 1 : Nettoyer et préparer l'URL
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = parse_url($url, PHP_URL_PATH);
        $url = trim($url, '/');

        // Etape 2 : Supprimer le chemin de base
        $basePath = 'recettesaipoo';
        if(strpos($url, $basePath) === 0) {
            // S'assurer que c'est bien au début et pas au milieu d'un autre segment
            $afterBase = substr($url, strlen($basePath));
            if (empty($afterBase) || $afterBase[0] === '/') {
                $url = ltrim($afterBase, '/');
            }
        }
        
        // Si l'URL est vide après traitement, utiliser la route racine
        if (empty($url)) {
            $url = '';
        }
        
        // Journaliser l'URL après traitement (pour déboguer)
        error_log("URL après traitement : '{$url}'");
        
        // Méthode HTTP actuelle
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Variables pour stocker le contrôleur et l'action trouvés
        $foundController = null;
        $foundAction = null;
        $params = [];
        
        // 1. Chercher une correspondance exacte d'abord (plus rapide)
        if (array_key_exists($url, $this->routes) && 
            ($this->routes[$url]['method'] === $requestMethod || $this->routes[$url]['method'] === 'ANY')) {
            $foundController = $this->routes[$url]['controller'];
            $foundAction = $this->routes[$url]['action'];
            error_log("Route exacte trouvée : {$url} => {$foundController}::{$foundAction}");
        } else {
            // 2. Chercher des routes avec des paramètres
            foreach ($this->routes as $pattern => $route) {
                // Si la méthode HTTP ne correspond pas, passer à la route suivante
                if ($route['method'] !== $requestMethod && $route['method'] !== 'ANY') {
                    continue;
                }
                
                // Convertir le motif de route en expression régulière correctement
                // Gérer spécifiquement le pattern ([0-9]+)
                $patternAsRegex = preg_quote($pattern, '#');
                $patternAsRegex = str_replace('\([0-9]\+\)', '([0-9]+)', $patternAsRegex);
                $regexPattern = '#^' . $patternAsRegex . '$#';
                
                error_log("Tentative de correspondance avec le pattern: $regexPattern pour l'URL: $url");
                
                if (preg_match($regexPattern, $url, $matches)) {
                    $foundController = $route['controller'];
                    $foundAction = $route['action'];
                    
                    // Extraire les paramètres
                    array_shift($matches); // Supprimer la correspondance complète
                    $params = $matches;
                    
                    error_log("Route avec paramètres trouvée : {$pattern} => {$foundController}::{$foundAction}");
                    error_log("Paramètres : " . print_r($params, true));
                    break;
                }
            }
        }

        // 3. Appeler le contrôleur et l'action s'ils ont été trouvés
        if ($foundController) {
            if (class_exists($foundController)) {
                $controllerInstance = new $foundController();
                
                if (method_exists($controllerInstance, $foundAction)) {
                    // Appeler la méthode avec les paramètres
                    call_user_func_array([$controllerInstance, $foundAction], $params);
                } else {
                    $this->notFound("Action non trouvée: {$foundAction} dans le contrôleur {$foundController}");
                }
            } else {
                $this->notFound("Contrôleur non trouvé: {$foundController}");
            }
        } else {
            $this->notFound("Route non trouvée pour l'URL: {$url}");
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