<?php
namespace App\Core;

class CSRF {
    
    /**
     * Génère un token CSRF unique pour la session
     * @return string Le token CSRF
     */
    public static function generateToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valide un token CSRF
     * @param string $token Le token à valider
     * @return bool True si le token est valide, false sinon
     */
    public static function validateToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Génère un champ input hidden avec le token CSRF
     * @return string Le HTML du champ hidden
     */
    public static function getTokenField(): string {
        return '<input type="hidden" name="csrf_token" value="' . self::generateToken() . '">';
    }
    
    /**
     * Vérifie et valide le token CSRF depuis $_POST
     * @return bool True si le token est valide
     */
    public static function validateFromPost(): bool {
        if (!isset($_POST['csrf_token'])) {
            return false;
        }
        
        return self::validateToken($_POST['csrf_token']);
    }
    
    /**
     * Régénère un nouveau token (par exemple après connexion)
     * @return string Le nouveau token
     */
    public static function regenerateToken(): string {
        unset($_SESSION['csrf_token']);
        return self::generateToken();
    }
}