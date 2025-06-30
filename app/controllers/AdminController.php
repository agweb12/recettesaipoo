<?php
// app/controllers/AdminController.php
namespace App\Controllers;

use PDO;
use App\Core\Database;
use App\Core\Controller;
use App\Models\Administrateurs;
use App\Models\Analytics;

class AdminController extends Controller {
    
    private $adminModel;
    private $analyticsModel;
    
    public function __construct() {
        // Vérifier si l'administrateur est connecté
        if(!$this->isAdminLoggedIn() && $_SERVER['REQUEST_URI'] !== '/recettesaipoo/admin/login') {
            $this->redirect(RACINE_SITE . 'admin/login');
            exit;
        }
        
        $this->adminModel = new Administrateurs();
        $this->analyticsModel = new Analytics();
    }
    
    /**
     * Affiche le tableau de bord administrateur
     */
    /**
     * @return void
     */
    public function dashboard() : void
    {
        // Connexion à la base de données
        $db = Database::getInstance();
        
        // Récupération des compteurs pour chaque table
        $counters = array();
        
        // Catégories
        $stmt = $db->query("SELECT COUNT(*) as total FROM categorie");
        $counters['categories'] = $stmt->fetch()['total'];
        
        // Utilisateurs
        $stmt = $db->query("SELECT COUNT(*) as total FROM utilisateur");
        $counters['utilisateurs'] = $stmt->fetch()['total'];
        
        // Administrateurs
        $stmt = $db->query("SELECT COUNT(*) as total FROM administrateur");
        $counters['administrateurs'] = $stmt->fetch()['total'];
        
        // Recettes
        $stmt = $db->query("SELECT COUNT(*) as total FROM recette");
        $counters['recettes'] = $stmt->fetch()['total'];
        
        // Étiquettes
        $stmt = $db->query("SELECT COUNT(*) as total FROM etiquette");
        $counters['etiquettes'] = $stmt->fetch()['total'];
        
        // Unités de mesure
        $stmt = $db->query("SELECT COUNT(*) as total FROM unite_mesure");
        $counters['unites'] = $stmt->fetch()['total'];
        
        // Ingrédients
        $stmt = $db->query("SELECT COUNT(*) as total FROM ingredient");
        $counters['ingredients'] = $stmt->fetch()['total'];

        // Récupération des données analytiques
        $analytics = [
            'topRecipes' => $this->analyticsModel->getTopRecipesByFavorites(5),
            'userStats' => $this->analyticsModel->getUserStats(),
            'popularCategories' => $this->analyticsModel->getPopularCategories(),
            'popularIngredients' => $this->analyticsModel->getPopularIngredients(),
            'activityByMonth' => $this->analyticsModel->getActivityByMonth(),
            'favoritesTrends' => $this->analyticsModel->getFavoritesTrends(),
            'recipeStats' => $this->analyticsModel->getRecipeStats(),
            'mostActiveUsers' => $this->analyticsModel->getMostActiveUsers(),
            'popularTags' => $this->analyticsModel->getPopularTags(),
            'growthData' => $this->analyticsModel->getGrowthData(),
            // Nouvelles statistiques (optionnel)
            'performanceStats' => $this->analyticsModel->getRecipePerformanceStats(),
            'engagementStats' => $this->analyticsModel->getUserEngagementStats()
        ];
        
        $this->view('admin/dashboard', [
            'titlePage' => "Dashboard Admin - Recettes AI",
            'descriptionPage' => "Tableau de bord de l'équipe Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'counters' => $counters,
            'analytics' => $analytics
        ]);
    }

    /**
     * Page dédiée aux analyses détaillées
     */
    public function analytics() : void {
        // Récupération de toutes les données analytiques
        $analytics = [
            'topRecipes' => $this->analyticsModel->getTopRecipesByFavorites(20),
            'userStats' => $this->analyticsModel->getUserStats(),
            'popularCategories' => $this->analyticsModel->getPopularCategories(),
            'popularIngredients' => $this->analyticsModel->getPopularIngredients(),
            'activityByMonth' => $this->analyticsModel->getActivityByMonth(),
            'favoritesTrends' => $this->analyticsModel->getFavoritesTrends(),
            'recipeStats' => $this->analyticsModel->getRecipeStats(),
            'mostActiveUsers' => $this->analyticsModel->getMostActiveUsers(),
            'popularTags' => $this->analyticsModel->getPopularTags(),
            'growthData' => $this->analyticsModel->getGrowthData(),
            // Nouvelles statistiques (optionnel)
            'performanceStats' => $this->analyticsModel->getRecipePerformanceStats(),
            'engagementStats' => $this->analyticsModel->getUserEngagementStats()
        ];

        $this->view('admin/analytics', [
            'titlePage' => "Analyses & Statistiques - Admin",
            'descriptionPage' => "Analyses détaillées de l'utilisation de Recettes AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'analytics' => $analytics
        ]);
    }
}