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
        error_log("=== DEBUT DEBUG ROUTER ===");
        error_log("URL originale reçue : " . $url);
        error_log("REQUEST_URI : " . $_SERVER['REQUEST_URI']);
        error_log("Méthode HTTP : " . $_SERVER['REQUEST_METHOD']);

        // Etape 1 : Nettoyer et préparer l'URL
        $url = filter_var($url, FILTER_SANITIZE_URL);
        error_log("URL après FILTER_SANITIZE_URL : " . $url);
        $url = parse_url($url, PHP_URL_PATH);
        error_log("URL après parse_url : " . $url);
        $url = trim($url, '/');
        error_log("URL après trim : " . $url);

        // Etape 2 : Supprimer le chemin de base
        $basePath = 'recettesaipoo';
        if(strpos($url, $basePath) === 0) {
            error_log("Chemin de base détecté");
            // S'assurer que c'est bien au début et pas au milieu d'un autre segment
            $afterBase = substr($url, strlen($basePath));
             error_log("Après suppression du chemin de base : " . $afterBase);

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

        // Debug - afficher les routes disponibles pour cette méthode
        $routesForMethod = [];
        foreach ($this->routes as $pattern => $route) {
            if ($route['method'] === $requestMethod || $route['method'] === 'ANY') {
                $routesForMethod[] = $pattern;
            }
        }
        error_log("Routes disponibles pour " . $requestMethod . " : " . implode(', ', $routesForMethod));


        // Variables pour stocker le contrôleur et l'action trouvés
        $foundController = null;
        $foundAction = null;
        $params = [];
        
        // 1. Chercher une correspondance exacte d'abord (plus rapide)
        if (array_key_exists($url, $this->routes)) {
            $route = $this->routes[$url];
            error_log("Route exacte trouvée dans le tableau pour : " . $url);
            if ($route['method'] === $requestMethod || $route['method'] === 'ANY') {
                $foundController = $route['controller'];
                $foundAction = $route['action'];
                error_log("Méthode HTTP compatible - Controller: {$foundController}, Action: {$foundAction}");
            } else {
                error_log("Méthode HTTP incompatible - Route: {$route['method']}, Requête: {$requestMethod}");
            }
        } else {
            error_log("Aucune route exacte trouvée pour : " . $url);
        }

        if (!$foundController) {
            error_log("Recherche de routes avec paramètres...");
            // 2. Chercher des routes avec des paramètres
            foreach ($this->routes as $pattern => $route) {
                if ($route['method'] !== $requestMethod && $route['method'] !== 'ANY') {
                    continue;
                }

                // Remplacer les patterns dynamiques directement
                $pregPattern = str_replace('([0-9]+)', '(\d+)', $pattern);
                $pregPattern = '#^' . str_replace('/', '\/', $pregPattern) . '$#';
                
                error_log("Test pattern: '{$pattern}' => regex: '{$pregPattern}' pour URL: '{$url}'");
                
                if (preg_match($pregPattern, $url, $matches)) {
                    error_log("Match trouvé avec pattern '{$pattern}'");
                    $foundController = $route['controller'];
                    $foundAction = $route['action'];
                    
                    // Extraire les paramètres (retirer le premier élément qui est l'URL complète)
                    array_shift($matches);
                    $params = $matches;
                    
                    break;
                }
            }
        }

        error_log("=== FIN DEBUG ROUTER ===");


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

    private function notFound($message) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page non trouvée</h1>";
        echo "<p>" . $message . "</p>";
        echo "<p>URL demandée: " . $_SERVER['REQUEST_URI'] . "</p>";
        echo "<p>Routes disponibles: " . implode(', ', array_keys($this->routes)) . "</p>";
    }
}