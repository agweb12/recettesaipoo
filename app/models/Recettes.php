<?php
// app/models/Recettes.php
namespace App\Models;
use App\Core\Model; // Importation de la classe Model pour étendre ses fonctionnalités
use PDO; // Importation de la classe PDO pour interagir avec la base de données
/**
 * Classe Recettes qui étend la classe Model pour interagir avec la table des recettes.
 * Elle contient des méthodes pour récupérer les recettes populaires et récentes.
 */
class Recettes extends Model{
    protected $table = 'recette'; // Nom de la table dans la base de données

    /**
     * Récupère les 3 recettes les plus populaires
     * @param int $limit Nombre de recettes à récupérer
     * @return array Les recettes populaires
     */
    public function getPopularRecipes($limit = 3) : array
    {
        $sql = "SELECT r.*, COUNT(rf.id_recette) AS nb_favoris FROM {$this->table} r 
        LEFT JOIN recette_favorite rf ON r.id = rf.id_recette GROUP BY r.id ORDER BY nb_favoris DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les 3 recettes les plus récentes
     * @param int $limit Nombre de recettes à récupérer
     * @return array Les recettes récentes
     */
    public function getRecentRecipes($limit = 3) : array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY date_creation DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les IDs des recettes favorites d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Les IDs des recettes favorites
     */
    public function getUserFavoriteIds($userId) 
    {
        $sql = "SELECT id_recette FROM recette_favorite WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $result ?: [];
    }

    /**
     * Récupère les recettes correspondant aux ingrédients d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Les recettes correspondant aux ingrédients
     */
    public function getRecipesByUserIngredients($userId) 
    {
        $sql = "SELECT DISTINCT r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, 
                r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, 
                c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte, 
                COUNT(DISTINCT lri.id_ingredient) AS nombre_ingredients_correspondants
                FROM recette r
                JOIN liste_recette_ingredients lri ON r.id = lri.id_recette
                JOIN categorie c ON r.id_categorie = c.id
                WHERE lri.id_ingredient IN (
                    SELECT id_ingredient
                    FROM liste_personnelle_ingredients lpi
                    WHERE lpi.id_utilisateur = :id_utilisateur) 
                GROUP BY r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, r.difficulte, r.image_url, 
                    c.nom, c.id, c.couleur
                ORDER BY nombre_ingredients_correspondants DESC, r.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère le nombre total d'ingrédients d'une recette
     * @param int $recipeId ID de la recette
     * @return int Le nombre total d'ingrédients
     */
    public function countRecipeIngredients($recipeId) 
    {
        $sql = "SELECT COUNT(*) AS nombre_ingredients_total 
                FROM liste_recette_ingredients 
                WHERE id_recette = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Récupère des recettes filtrées selon des critères
     * @param array $whereConditions Conditions WHERE de la requête
     * @param array $filterParams Paramètres des filtres
     * @return array Les recettes filtrées
     */
    public function getFilteredRecipes($whereConditions, $filterParams) 
    {
        $sql = "SELECT r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, 
                r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, 
                c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte
                FROM recette r
                JOIN categorie c ON r.id_categorie = c.id";
        
        // Ajouter les conditions WHERE si elles existent
        if(count($whereConditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        
        // Ajouter la clause ORDER BY pour trier les résultats
        $sql .= " ORDER BY r.date_creation DESC";
        
        $stmt = $this->db->prepare($sql);
        
        // Lier tous les paramètres de filtre
        foreach($filterParams as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}