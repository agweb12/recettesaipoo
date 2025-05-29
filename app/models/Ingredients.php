<?php
//app/models/Ingredients.php
namespace App\Models;
use App\Core\Model; // Importation de la classe Model pour étendre ses fonctionnalités
use PDO; // Importation de la classe PDO pour interagir avec la base de données
/**
 * Classe Ingredients qui étend la classe Model pour interagir avec la table des ingrédients.
 * Elle contient des méthodes pour gérer les ingrédients d'un utilisateur.
 */
class Ingredients extends Model {
    protected $table = 'ingredient';

    /**
     * Récupère les ingrédients d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Les ingrédients de l'utilisateur
     */
    public function getUserIngredients($userId) : array
    {
        $sql = "SELECT i.id, i.nom FROM liste_personnelle_ingredients lpi
                JOIN {$this->table} i ON lpi.id_ingredient = i.id
                WHERE lpi.id_utilisateur = :id_utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les IDs des ingrédients d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Les IDs des ingrédients de l'utilisateur
     */
    public function getUserIngredientsIds($userId) : array
    {
        $sql = "SELECT id_ingredient FROM liste_personnelle_ingredients 
                WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Supprime un ingrédient spécifique de la liste personnelle d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $ingredientId ID de l'ingrédient
     * @return bool True si supprimé avec succès, False sinon
     */
    public function deleteUserIngredient($userId, $ingredientId): bool
    {
        $sql = "DELETE FROM liste_personnelle_ingredients 
                WHERE id_utilisateur = :id_utilisateur AND id_ingredient = :id_ingredient";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':id_ingredient', $ingredientId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprimer tous les ingrédients d'un utilisateur
     * @param int $userId L'ID de l'utilisateur dont les ingrédients doivent être supprimés
     */
    public function deleteUserIngredients($userId): int // int retourne le nombre de lignes affectées
    {
        $sql = "DELETE FROM liste_personnelle_ingredients WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Ajouter des ingrédients pour un utilisateur
     * @param int $userId L'ID de l'utilisateur
     * @param array $ingredients Un tableau d'ingrédients à ajouter
     */

     public function addUserIngredients(int $userId, array $ingredients): int // retourne le nombre de lignes affectées
    {
        $sql = "INSERT INTO liste_personnelle_ingredients (id_utilisateur, id_ingredient, quantite, date_creation) VALUES (:id_utilisateur, :id_ingredient, :quantite, NOW())";
        $stmt = $this->db->prepare($sql);

        foreach($ingredients as $ingredientId){
            if(empty($ingredientId)) continue; // Si l'ingrédient est vide, je passe à l'itération suivante
            
            // Je vérifie si l'ingrédient existe
            if($this->ingredientExists($ingredientId)){
                $stmt->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':id_ingredient', $ingredientId, PDO::PARAM_INT);
                $stmt->bindValue(':quantite', 1, PDO::PARAM_INT); // J'assigne une quantité par défaut de 1
                $result = $stmt->execute() && $result; // $stmt->execute() retourne true si l'insertion a réussi, && $result permet de conserver le résultat de l'exécution précédente
            }
            else {
                // Si l'ingrédient n'existe pas, je peux choisir de lancer une exception ou de continuer
                // Lancer une exception
                throw new \Exception("L'ingrédient avec l'ID $ingredientId n'existe pas.");
            }
        }
        // $result est un booléen qui indique si l'insertion a réussi pour tous les ingrédients
        return $result;
    }

    /**
     * Vérifie si un ingrédient existe dans la base de données
     * @param int $idIngredient L'ID de l'ingrédient à vérifier
     * @return bool Retourne true si l'ingrédient existe, false sinon
     */
    public function ingredientExists($ingredientId)
    {
        $sql = "SELECT id FROM ingredient WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $ingredientId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }


    /**
     * Recherche des ingrédients par nom
     * @param string $search Le terme de recherche
     * @return array Les ingrédients correspondants
     */
    public function searchIngredients(string $search) : array
    {
        $sql = "SELECT id, nom FROM {$this->table} WHERE nom LIKE :search ORDER BY nom ASC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les ingrédients
     * @return array Un tableau contenant tous les ingrédients
     */
    public function getAllIngredients()
    {
        return $this->findAll();
    }
}