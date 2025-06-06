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
        // Vérifier si le tableau d'ingrédients est vide
        if (empty($ingredients) || count(array_filter($ingredients)) === 0) {
            return 0; // Retourner 0 si aucun ingrédient valide
        }
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
            return (int)$result; // Convertir le résultat booléen en entier (1 pour succès, 0 pour échec)
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

    // ADMINISTRATIVE FUNCTIONS

    /**
     * Récupère tous les ingrédients avec informations d'utilisation
     * @return array Tous les ingrédients avec leur utilisation
     */
    public function getAllIngredientsWithUsage() : array
    {
        $sql = "SELECT i.*, 
                (SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_ingredient = i.id) AS nb_recettes,
                (SELECT COUNT(*) FROM liste_personnelle_ingredients WHERE id_ingredient = i.id) AS nb_listes_perso
                FROM {$this->table} i
                ORDER BY i.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie si un ingrédient avec ce nom existe déjà
     * @param string $name Le nom à vérifier
     * @return bool True si le nom existe déjà, sinon false
     */
    public function existsByName(string $name) : bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE nom = :nom";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $name);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Vérifie si un ingrédient avec ce nom existe déjà, sauf pour l'ID spécifié
     * @param string $name Le nom à vérifier
     * @param int $id L'ID à exclure de la vérification
     * @return bool True si le nom existe déjà pour un autre ID, sinon false
     */
    public function existsByNameExcept(string $name, int $id) : bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE nom = :nom AND id != :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $name);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Crée un nouvel ingrédient
     * @param array $data Les données de l'ingrédient
     * @return int|bool L'ID de l'ingrédient créé ou false en cas d'échec
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (nom, id_admin) VALUES (:nom, :id_admin)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':id_admin', $data['id_admin'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour un ingrédient existant
     * @param int $id L'ID de l'ingrédient à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool True si la mise à jour a réussi, sinon false
     */
    public function update(int $id, array $data) : bool
    {
        $sql = "UPDATE {$this->table} SET nom = :nom, id_admin = :id_admin WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':id_admin', $data['id_admin'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime un ingrédient
     * @param int $id L'ID de l'ingrédient à supprimer
     * @return bool True si la suppression a réussi, sinon false
     */
    public function delete(int $id) : bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Vérifie si un ingrédient est utilisé dans des recettes
     * @param int $id L'ID de l'ingrédient à vérifier
     * @return bool True si l'ingrédient est utilisé, sinon false
     */
    public function isUsedInRecipes(int $id) : bool
    {
        $sql = "SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_ingredient = :id_ingredient";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_ingredient', $id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Vérifie si un ingrédient est dans des listes personnelles
     * @param int $id L'ID de l'ingrédient à vérifier
     * @return bool True si l'ingrédient est dans des listes personnelles, sinon false
     */
    public function isInPersonalLists(int $id) : bool
    {
        $sql = "SELECT COUNT(*) FROM liste_personnelle_ingredients WHERE id_ingredient = :id_ingredient";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_ingredient', $id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Enregistre une action d'administrateur dans le journal
     * @param int $adminId L'ID de l'administrateur
     * @param string $table La table modifiée
     * @param int $elementId L'ID de l'élément modifié
     * @param string $action Le type d'action (ajout, modification, suppression)
     * @return bool True si l'enregistrement a réussi, sinon false
     */
    public function logAdminAction(int $adminId, string $table, int $elementId, string $action) : bool
    {
        $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) 
                VALUES (:id_admin, :table_modifiee, :id_element, :action)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_admin', $adminId, PDO::PARAM_INT);
        $stmt->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
        $stmt->bindParam(':id_element', $elementId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}