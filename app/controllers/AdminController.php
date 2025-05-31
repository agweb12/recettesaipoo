<?php
// app/controllers/AdminController.php
namespace App\Controllers;

use PDO;
use App\Core\Database;
use App\Core\Controller;
use App\Models\Administrateurs;

class AdminController extends Controller {
    
    private $adminModel;
    
    public function __construct() {
        // Vérifier si l'administrateur est connecté
        if(!$this->isAdminLoggedIn() && $_SERVER['REQUEST_URI'] !== '/recettesaipoo/admin/login') {
            $this->redirect(RACINE_SITE . 'admin/login');
            exit;
        }
        
        $this->adminModel = new Administrateurs();
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
        // Connexion à la base de données
        // Remplacer new Database() par Database::getInstance()
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
        
        $this->view('admin/dashboard', [
            'titlePage' => "Dashboard Admin - Recettes AI",
            'descriptionPage' => "Tableau de bord de l'équipe Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'counters' => $counters
        ]);
    }
}