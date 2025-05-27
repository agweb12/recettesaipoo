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

     public function addUserIngredients($userId, array $ingredients): int // retourne le nombre de lignes affectées
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
     * Récupère tous les ingrédients
     * @return array Un tableau contenant tous les ingrédients
     */
    public function getAllIngredients()
    {
        return $this->findAll();
    }
}