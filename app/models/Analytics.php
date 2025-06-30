<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Analytics extends Model {
    
    /**
     * Récupère les recettes les plus populaires (par favoris)
     * @param int $limit Nombre de recettes à récupérer
     * @return array
     */
    public function getTopRecipesByFavorites(int $limit = 10): array {
        $sql = "SELECT r.id, r.nom, r.image_url, c.nom as categorie, c.couleur as couleur_categorie,
                       COUNT(rf.id_recette) as nb_favoris,
                       r.date_creation
                FROM recette r
                LEFT JOIN recette_favorite rf ON r.id = rf.id_recette
                LEFT JOIN categorie c ON r.id_categorie = c.id
                GROUP BY r.id, r.nom, r.image_url, c.nom, c.couleur, r.date_creation
                ORDER BY nb_favoris DESC, r.date_creation DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les statistiques des utilisateurs
     * @return array
     */
    public function getUserStats(): array {
        $stats = [];
        
        // Utilisateurs actifs (avec au moins un favori ou un ingrédient)
        $sql = "SELECT COUNT(DISTINCT u.id) as users_actifs
                FROM utilisateur u
                LEFT JOIN recette_favorite rf ON u.id = rf.id_utilisateur
                LEFT JOIN liste_personnelle_ingredients lpi ON u.id = lpi.id_utilisateur
                WHERE rf.id_utilisateur IS NOT NULL OR lpi.id_utilisateur IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['users_actifs'] = $stmt->fetch()['users_actifs'];
        
        // Nouveaux utilisateurs cette semaine
        $sql = "SELECT COUNT(*) as nouveaux_users
                FROM utilisateur 
                WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['nouveaux_users'] = $stmt->fetch()['nouveaux_users'];
        
        // Utilisateurs avec favoris
        $sql = "SELECT COUNT(DISTINCT id_utilisateur) as users_avec_favoris
                FROM recette_favorite";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['users_avec_favoris'] = $stmt->fetch()['users_avec_favoris'];
        
        // Utilisateurs avec liste d'ingrédients
        $sql = "SELECT COUNT(DISTINCT id_utilisateur) as users_avec_ingredients
                FROM liste_personnelle_ingredients";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['users_avec_ingredients'] = $stmt->fetch()['users_avec_ingredients'];
        
        return $stats;
    }
    
    /**
     * Récupère les catégories les plus populaires
     * @return array
     */
    public function getPopularCategories(): array {
        $sql = "SELECT c.nom, c.couleur, COUNT(rf.id_recette) as nb_favoris
                FROM categorie c
                LEFT JOIN recette r ON c.id = r.id_categorie
                LEFT JOIN recette_favorite rf ON r.id = rf.id_recette
                GROUP BY c.id, c.nom, c.couleur
                ORDER BY nb_favoris DESC
                LIMIT 5";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les ingrédients les plus utilisés
     * @return array
     */
    public function getPopularIngredients(): array {
        $sql = "SELECT i.nom, COUNT(lpi.id_ingredient) as nb_utilisations
                FROM ingredient i
                LEFT JOIN liste_personnelle_ingredients lpi ON i.id = lpi.id_ingredient
                GROUP BY i.id, i.nom
                HAVING nb_utilisations > 0
                ORDER BY nb_utilisations DESC
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les statistiques d'activité par mois
     * @return array
     */
    public function getActivityByMonth(): array {
        $sql = "SELECT 
                    DATE_FORMAT(date_inscription, '%Y-%m') as mois,
                    COUNT(*) as nb_inscriptions
                FROM utilisateur 
                WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(date_inscription, '%Y-%m')
                ORDER BY mois ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les tendances des favoris par semaine
     * @return array
     */
    public function getFavoritesTrends(): array {
        $sql = "SELECT 
                    DATE_FORMAT(date_enregistrement, '%Y-%u') as semaine,
                    COUNT(*) as nb_favoris
                FROM recette_favorite 
                WHERE date_enregistrement >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
                GROUP BY DATE_FORMAT(date_enregistrement, '%Y-%u')
                ORDER BY semaine ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les statistiques des recettes
     * @return array
     */
    public function getRecipeStats(): array {
        $stats = [];
        
        // Recettes par difficulté
        $sql = "SELECT difficulte, COUNT(*) as nb_recettes
                FROM recette 
                GROUP BY difficulte
                ORDER BY nb_recettes DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['par_difficulte'] = $stmt->fetchAll();
        
        // Temps de préparation moyen
        $sql = "SELECT 
                    AVG(temps_preparation) as temps_prep_moyen,
                    AVG(temps_cuisson) as temps_cuisson_moyen
                FROM recette";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['temps_moyen'] = $stmt->fetch();
        
        // Recettes ajoutées ce mois
        $sql = "SELECT COUNT(*) as nouvelles_recettes
                FROM recette 
                WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['nouvelles_recettes'] = $stmt->fetch()['nouvelles_recettes'];
        
        return $stats;
    }
    
    /**
     * Récupère les utilisateurs les plus actifs
     * @return array
     */
    public function getMostActiveUsers(): array {
        $sql = "SELECT u.prenom, u.nom, u.email,
                       COUNT(DISTINCT rf.id_recette) as nb_favoris,
                       COUNT(DISTINCT lpi.id_ingredient) as nb_ingredients,
                       u.date_inscription
                FROM utilisateur u
                LEFT JOIN recette_favorite rf ON u.id = rf.id_utilisateur
                LEFT JOIN liste_personnelle_ingredients lpi ON u.id = lpi.id_utilisateur
                GROUP BY u.id, u.prenom, u.nom, u.email, u.date_inscription
                HAVING (nb_favoris + nb_ingredients) > 0
                ORDER BY (nb_favoris + nb_ingredients) DESC
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les étiquettes les plus utilisées
     * @return array
     */
    public function getPopularTags(): array {
        $sql = "SELECT e.nom, COUNT(re.id_recette) as nb_utilisations
                FROM etiquette e
                LEFT JOIN recette_etiquette re ON e.id = re.id_etiquette
                GROUP BY e.id, e.nom
                HAVING nb_utilisations > 0
                ORDER BY nb_utilisations DESC
                LIMIT 8";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les données pour le graphique de croissance (VERSION CORRIGÉE)
     * @return array
     */
    public function getGrowthData(): array {
        // Version simplifiée compatible avec only_full_group_by
        $sql = "SELECT 
                    DATE_FORMAT(date_inscription, '%Y-%m') as mois,
                    COUNT(*) as nouveaux_users
                FROM utilisateur 
                WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(date_inscription, '%Y-%m')
                ORDER BY mois ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        // Calculer le total cumulé en PHP
        $totalUsers = 0;
        $result = [];
        
        foreach ($data as $row) {
            $totalUsers += $row['nouveaux_users'];
            $result[] = [
                'mois' => $row['mois'],
                'nouveaux_users' => $row['nouveaux_users'],
                'total_users' => $totalUsers
            ];
        }
        
        return $result;
    }
    
    /**
     * Récupère les statistiques de performance des recettes
     * @return array
     */
    public function getRecipePerformanceStats(): array {
        $stats = [];
        
        // Top 5 des recettes les plus consultées (en supposant que vous ayez une table de vues)
        $sql = "SELECT r.nom, COUNT(rf.id_recette) as nb_favoris
                FROM recette r
                LEFT JOIN recette_favorite rf ON r.id = rf.id_recette
                GROUP BY r.id, r.nom
                ORDER BY nb_favoris DESC
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['top_recettes'] = $stmt->fetchAll();
        
        // Répartition des recettes par temps de préparation
        $sql = "SELECT 
                    CASE 
                        WHEN temps_preparation <= 15 THEN 'Rapide (≤15min)'
                        WHEN temps_preparation <= 30 THEN 'Moyen (16-30min)'
                        WHEN temps_preparation <= 60 THEN 'Long (31-60min)'
                        ELSE 'Très long (>60min)'
                    END as categorie_temps,
                    COUNT(*) as nb_recettes
                FROM recette
                GROUP BY categorie_temps
                ORDER BY nb_recettes DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['repartition_temps'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    /**
     * Récupère les statistiques d'engagement des utilisateurs
     * @return array
     */
    public function getUserEngagementStats(): array {
        $stats = [];
        
        // Utilisateurs par nombre de favoris
        $sql = "SELECT 
                    CASE 
                        WHEN nb_favoris = 0 THEN 'Aucun favori'
                        WHEN nb_favoris <= 5 THEN '1-5 favoris'
                        WHEN nb_favoris <= 10 THEN '6-10 favoris'
                        ELSE 'Plus de 10 favoris'
                    END as categorie_favoris,
                    COUNT(*) as nb_utilisateurs
                FROM (
                    SELECT u.id, COUNT(rf.id_recette) as nb_favoris
                    FROM utilisateur u
                    LEFT JOIN recette_favorite rf ON u.id = rf.id_utilisateur
                    GROUP BY u.id
                ) as user_favoris
                GROUP BY categorie_favoris";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['repartition_favoris'] = $stmt->fetchAll();
        
        // Utilisateurs par nombre d'ingrédients dans leur liste
        $sql = "SELECT 
                    CASE 
                        WHEN nb_ingredients = 0 THEN 'Aucun ingrédient'
                        WHEN nb_ingredients <= 10 THEN '1-10 ingrédients'
                        WHEN nb_ingredients <= 20 THEN '11-20 ingrédients'
                        ELSE 'Plus de 20 ingrédients'
                    END as categorie_ingredients,
                    COUNT(*) as nb_utilisateurs
                FROM (
                    SELECT u.id, COUNT(lpi.id_ingredient) as nb_ingredients
                    FROM utilisateur u
                    LEFT JOIN liste_personnelle_ingredients lpi ON u.id = lpi.id_utilisateur
                    GROUP BY u.id
                ) as user_ingredients
                GROUP BY categorie_ingredients";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['repartition_ingredients'] = $stmt->fetchAll();
        
        return $stats;
    }
}