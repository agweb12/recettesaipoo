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
    public function getPopularRecipes(int $limit = 3) : array
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
    public function getRecentRecipes(int $limit = 3) : array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY date_creation DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère une recette par son ID avec les détails de la catégorie
     * @param int $id ID de la recette
     * @return array|false Les détails de la recette ou false si non trouvée
     */
    public function getRecipeById(int $recipeId) : array|false
    {
        $sql = "SELECT r.id, r.nom, r.descriptif, r.instructions, r.temps_preparation, r.temps_cuisson, 
                r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, 
                c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte
                FROM {$this->table} r
                JOIN categorie c ON r.id_categorie = c.id
                WHERE r.id = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Vérifie si une recette est dans les favoris d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $recipeId ID de la recette
     * @return bool True si la recette est dans les favoris, sinon false
     */
    public function isRecipeFavorite(int $userId, int $recipeId) : bool
    {
        $sql = "SELECT COUNT(*) as count_favoris FROM recette_favorite 
                WHERE id_utilisateur = :id_utilisateur 
                AND id_recette = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return ($result['count_favoris'] > 0);
    }

    /**
     * Récupère les ingrédients d'une recette
     * @param int $recipeId ID de la recette
     * @return array Les ingrédients de la recette
     */
    public function getRecipeIngredients(int $recipeId) : array
    {
        $sql = "SELECT i.id, i.nom, lri.quantite, um.abreviation as unite, um.nom as nomUnite 
                FROM ingredient i
                JOIN liste_recette_ingredients lri ON i.id = lri.id_ingredient
                JOIN unite_mesure um ON lri.id_unite = um.id
                WHERE lri.id_recette = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les unités de mesure
     * @return array Les unités de mesure
     */
    public function getAllUniteMesure() : array
    {
        $sql = "SELECT id, nom, abreviation FROM unite_mesure ORDER BY nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Récupère les IDs des recettes favorites d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Les IDs des recettes favorites
     */
    public function getUserFavoriteIds(int $userId) : array
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
    public function getRecipesByUserIngredients(int $userId) : array
    {
        $sql = "SELECT DISTINCT r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, 
                r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, 
                c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte, 
                COUNT(DISTINCT lri.id_ingredient) AS nombre_ingredients_correspondants
                FROM {$this->table} r
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
    public function countRecipeIngredients(int $recipeId) : int
    {
        $sql = "SELECT COUNT(*) AS nombre_ingredients_total 
                FROM liste_recette_ingredients 
                WHERE id_recette = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn(); // Retourne le nombre total d'ingrédients
    }

    /**
     * Récupère des recettes filtrées selon des critères
     * @param array $whereConditions Conditions WHERE de la requête
     * @param array $filterParams Paramètres des filtres
     * @return array Les recettes filtrées
     */
    public function getFilteredRecipes(array $whereConditions, array $filterParams) : array
    {
        $sql = "SELECT r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, 
                r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, 
                c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte
                FROM {$this->table} r
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

    /**
     * Récupère les recettes favorites d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Les recettes favorites
     */
    public function getUserFavorites($userId): array 
    {
        $sql = "SELECT r.*, c.nom as categorie_nom, c.couleur as categorie_couleur, c.couleurTexte as couleurTexte 
                FROM recette_favorite rf
                JOIN recette r ON rf.id_recette = r.id
                JOIN categorie c ON r.id_categorie = c.id
                WHERE rf.id_utilisateur = :id_utilisateur
                ORDER BY rf.date_enregistrement DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ajoute une recette aux favoris d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $recipeId ID de la recette
     * @return bool True si l'ajout a réussi, sinon false
     */
    public function addToFavorites(int $userId, int $recipeId): bool 
    {
        $sql = "INSERT INTO recette_favorite (id_utilisateur, id_recette, date_enregistrement) 
                VALUES (:id_utilisateur, :id_recette, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprime une recette des favoris d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $recipeId ID de la recette
     * @return bool True si la suppression a réussi, sinon false
     */
    public function removeFromFavorites(int $userId, int $recipeId): bool 
    {
        $sql = "DELETE FROM recette_favorite 
                WHERE id_utilisateur = :id_utilisateur AND id_recette = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();

        // Vérifiez que des lignes ont été affectées
        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime toutes les recettes favorites d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return bool True si supprimé avec succès, False sinon
     */
    public function removeAllFavorites(int $userId): bool
    {
        $sql = "DELETE FROM recette_favorite WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}