<?php
// app/controllers/AdminAuthController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Administrateurs;

class AdminAuthController extends Controller {
    private $adminModel;

    public function __construct() {
        // Vous devrez créer un modèle Admin
        $this->adminModel = new Administrateurs();
    }

    /**
     * Affiche la page de connexion administrateur et gère le traitement du formulaire
     * @return void
     */
    public function login() : void
    {
        // Si l'administrateur est déjà connecté, rediriger vers le dashboard
        if($this->isAdminLoggedIn()){
            $this->redirect(RACINE_SITE . 'admin/dashboard');
            return;
        }

        $errors = [];
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'unknown';

        // Traitement du formulaire de connexion
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            // Validation CSRF
            if (!$this->validateCSRF()) {
                return;
            }
            
            // Vérification du rate limiting (plus strict pour les admins)
            if (\App\Core\RateLimiter::isBlocked($clientIp, 3, 1800)) { // 3 tentatives max, 30 minutes
                $timeRemaining = \App\Core\RateLimiter::getTimeRemaining($clientIp, 1800);
                $minutes = ceil($timeRemaining / 60);
                
                $errors['general'] = "Trop de tentatives de connexion admin. Réessayez dans {$minutes} minute(s).";
                
                // Log critique pour les tentatives admin
                error_log("CRITIQUE: Tentative de connexion admin bloquée - IP: {$clientIp} - Temps restant: {$timeRemaining}s");
                
                $this->view('admin/connexion', [
                    'titlePage' => "Connexion Admin - Recettes AI",
                    'descriptionPage' => "Connectez-vous à l'interface d'administration de Recettes AI",
                    'indexPage' => "noindex",
                    'followPage' => "nofollow",
                    'errors' => $errors
                ]);
                return;
            }

            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validation des données
            if(empty($email) || !isset($email)) {
                // Vérifier si l'email est vide ou non défini
                $errors['email'] = "L'email est requis";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "L'email n'est pas valide";
            }

            if(empty($password) || !isset($password)) {
                // Vérifier si le mot de passe est vide ou non défini
                $errors['password'] = "Le mot de passe est requis";
            } elseif(strlen($password) < 8) {
                $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères";
            }

            // Si pas d'erreurs, tenter l'authentification
            if(empty($errors)) {
                $admin = $this->adminModel->authenticate($email, $password);

                // Enregistrer la tentative
                \App\Core\RateLimiter::recordAttempt($clientIp, $email, (bool)$admin, 'admin');

                if($admin) {
                    // Connexion admin réussie - réinitialiser les tentatives
                    \App\Core\RateLimiter::resetAttempts($clientIp);
                    
                    $_SESSION['admin'] = $admin;
                    
                    // Régénérer le token CSRF après connexion
                    \App\Core\CSRF::regenerateToken();
                    
                    error_log("Connexion admin réussie - IP: {$clientIp} - Email: {$email} - Role: {$admin['role']}");
                    
                    $this->redirect(RACINE_SITE . 'admin/dashboard');
                    return;
                } else {
                    // Authentification admin échouée
                    $errors['general'] = "Email ou mot de passe incorrect";
                    
                    // Log critique pour les tentatives admin échouées
                    error_log("CRITIQUE: Tentative de connexion admin échouée - IP: {$clientIp} - Email: {$email}");
                }
            } else {
                // Enregistrer même les tentatives avec des données invalides
                \App\Core\RateLimiter::recordAttempt($clientIp, $email ?? 'invalid', false, 'admin');
            }
        }

        // Afficher la vue de connexion avec les erreurs éventuelles
        $this->view('admin/connexion', [
            'titlePage' => "Connexion Admin - Recettes AI",
            'descriptionPage' => "Connectez-vous à l'interface d'administration de Recettes AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'errors' => $errors
        ]);
    }

    /**
     * Déconnecte l'administrateur
     * @return void
     */
    public function logout() : void
    {
        unset($_SESSION['admin']);
        $this->redirect(RACINE_SITE . 'admin/login');
    }

    /**
     * Affiche les statistiques de sécurité (pour les super admins)
     */
    public function securityStats() {
        if (!$this->isAdminLoggedIn() || $_SESSION['admin']['role'] !== 'superadmin') {
            $this->redirect(RACINE_SITE . 'admin/login');
            return;
        }
        
        $db = \App\Core\Database::getInstance()->getPdo();
        
        // Statistiques des dernières 24h
        $sql = "SELECT 
                    ip_address,
                    COUNT(*) as attempts,
                    SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed_attempts,
                    MAX(attempt_time) as last_attempt,
                    user_type
                FROM login_attempts 
                WHERE attempt_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                GROUP BY ip_address, user_type
                ORDER BY failed_attempts DESC, attempts DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $securityStats = $stmt->fetchAll();
        
        $this->view('admin/security/stats', [
            'titlePage' => "Statistiques de Sécurité - Admin",
            'securityStats' => $securityStats
        ]);
    }
}