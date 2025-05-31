<?php
namespace App\Models;
use App\Core\Model;
use PDO;

/**
 * Classe Etiquettes qui étend la classe Model pour interagir avec la table des étiquettes.
 */
class Etiquettes extends Model {
    protected $table = 'etiquette';

    /**
     * Récupère toutes les étiquettes
     * @return array Toutes les étiquettes
     */
    public function getAllEtiquettes() : array
    {
        $sql = "SELECT id, nom FROM {$this->table} ORDER BY nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère toutes les étiquettes avec le nombre de recettes associées
     * @return array Étiquettes avec informations d'utilisation
     */
    public function getAllEtiquettesWithUsage() : array
    {
        $sql = "SELECT e.*, 
                (SELECT COUNT(*) FROM recette_etiquette WHERE id_etiquette = e.id) AS nb_recettes
            FROM {$this->table} e
            ORDER BY e.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les étiquettes d'une recette
     * @param int $recipeId ID de la recette
     * @return array Les étiquettes de la recette
     */
    public function getRecipeEtiquettes($recipeId) : array
    {
        $sql = "SELECT e.id, e.nom, e.descriptif FROM {$this->table} e 
                JOIN recette_etiquette re ON e.id = re.id_etiquette 
                WHERE re.id_recette = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Vérifie si une étiquette avec ce nom existe déjà
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
     * Vérifie si une étiquette avec ce nom existe déjà, sauf pour l'ID spécifié
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
     * Crée une nouvelle étiquette
     * @param array $data Les données de l'étiquette
     * @return int|bool L'ID de l'étiquette créée ou false en cas d'échec
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (nom, descriptif, id_admin) VALUES (:nom, :descriptif, :id_admin)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':descriptif', $data['descriptif']);
        $stmt->bindParam(':id_admin', $data['id_admin'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour une étiquette existante
     * @param int $id L'ID de l'étiquette à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool True si la mise à jour a réussi, sinon false
     */
    public function update(int $id, array $data) : bool
    {
        $sql = "UPDATE {$this->table} SET nom = :nom, descriptif = :descriptif, id_admin = :id_admin WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':descriptif', $data['descriptif']);
        $stmt->bindParam(':id_admin', $data['id_admin'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime une étiquette
     * @param int $id L'ID de l'étiquette à supprimer
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
     * Vérifie si une étiquette est utilisée dans des recettes
     * @param int $id L'ID de l'étiquette à vérifier
     * @return bool True si l'étiquette est utilisée, sinon false
     */
    public function isUsedInRecipes(int $id) : bool
    {
        $sql = "SELECT COUNT(*) FROM recette_etiquette WHERE id_etiquette = :id_etiquette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_etiquette', $id, PDO::PARAM_INT);
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