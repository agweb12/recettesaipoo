<?php
namespace App\Core;

use App\Core\Database;

class RateLimiter {
    private static $db;
    
    private static function getDb() {
        if (!self::$db) {
            self::$db = Database::getInstance()->getPdo();
        }
        return self::$db;
    }
    
    /**
     * Compte les tentatives de connexion dans la période donnée
     * @param string $ip L'adresse IP
     * @param int $timeLimit Période en secondes (par défaut 900 = 15 minutes)
     * @return int Nombre de tentatives
     */
    private static function getAttempts(string $ip, int $timeLimit = 900): int {
        $db = self::getDb();
        $timeThreshold = date('Y-m-d H:i:s', time() - $timeLimit);
        
        $sql = "SELECT COUNT(*) as count FROM login_attempts 
                WHERE ip_address = :ip AND attempt_time > :time_limit";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $stmt->bindParam(':time_limit', $timeThreshold, \PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (int)$result['count'];
    }
    
    /**
     * Vérifie si une IP est bloquée
     * @param string $ip L'adresse IP
     * @param int $maxAttempts Nombre maximum de tentatives (par défaut 5)
     * @param int $timeLimit Période en secondes (par défaut 900 = 15 minutes)
     * @return bool True si bloqué
     */
    public static function isBlocked(string $ip, int $maxAttempts = 5, int $timeLimit = 900): bool {
        return self::getAttempts($ip, $timeLimit) >= $maxAttempts;
    }
    
    /**
     * Enregistre une tentative de connexion
     * @param string $ip L'adresse IP
     * @param string $email L'email utilisé
     * @param bool $success Si la connexion a réussi
     * @param string $userType Type d'utilisateur (user/admin)
     * @return bool True si enregistré avec succès
     */
    public static function recordAttempt(string $ip, string $email, bool $success, string $userType = 'user'): bool {
        $db = self::getDb();
        
        $sql = "INSERT INTO login_attempts (ip_address, email, success, user_type, attempt_time) 
                VALUES (:ip, :email, :success, :user_type, NOW())";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':success', $success, \PDO::PARAM_BOOL);
        $stmt->bindParam(':user_type', $userType, \PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    /**
     * Obtient le temps restant avant de pouvoir réessayer
     * @param string $ip L'adresse IP
     * @param int $timeLimit Période en secondes
     * @return int Secondes restantes
     */
    public static function getTimeRemaining(string $ip, int $timeLimit = 900): int {
        $db = self::getDb();
        
        $sql = "SELECT MAX(attempt_time) as last_attempt FROM login_attempts 
                WHERE ip_address = :ip ORDER BY attempt_time DESC LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch();
        if (!$result || !$result['last_attempt']) {
            return 0;
        }
        
        $lastAttempt = strtotime($result['last_attempt']);
        $timeRemaining = ($lastAttempt + $timeLimit) - time();
        
        return max(0, $timeRemaining);
    }
    
    /**
     * Nettoie les anciennes tentatives (plus de 24h)
     * @return bool True si nettoyage réussi
     */
    public static function cleanOldAttempts(): bool {
        $db = self::getDb();
        $timeThreshold = date('Y-m-d H:i:s', time() - 86400); // 24 heures
        
        $sql = "DELETE FROM login_attempts WHERE attempt_time < :time_threshold";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':time_threshold', $timeThreshold, \PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    /**
     * Réinitialise les tentatives pour une IP (après connexion réussie)
     * @param string $ip L'adresse IP
     * @return bool True si réinitialisé avec succès
     */
    public static function resetAttempts(string $ip): bool {
        $db = self::getDb();
        
        $sql = "DELETE FROM login_attempts WHERE ip_address = :ip";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ip', $ip, \PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    /**
     * Obtient les statistiques des tentatives pour une IP
     * @param string $ip L'adresse IP
     * @return array Statistiques
     */
    public static function getAttemptStats(string $ip): array {
        $db = self::getDb();
        
        $sql = "SELECT 
                    COUNT(*) as total_attempts,
                    SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_attempts,
                    SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed_attempts,
                    MAX(attempt_time) as last_attempt
                FROM login_attempts 
                WHERE ip_address = :ip";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch() ?: [
            'total_attempts' => 0,
            'successful_attempts' => 0,
            'failed_attempts' => 0,
            'last_attempt' => null
        ];
    }
}