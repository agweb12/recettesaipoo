<?php
namespace App\Models;
use App\Core\Model;
use PDO;

/**
 * Classe Categories qui étend la classe Model pour interagir avec la table des catégories.
 */
class Categories extends Model {
    protected $table = 'categorie';

    /**
     * Récupère toutes les catégories
     * @return array Toutes les catégories
     */
    public function getAllCategories() : array
    {
        $sql = "SELECT id, nom, couleur, couleurTexte FROM {$this->table} ORDER BY nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les catégories avec le nombre de recettes associées
     * @return array Catégories avec informations d'utilisation
     */
    public function getAllCategoriesWithUsage() : array
    {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM recette WHERE id_categorie = c.id) AS nb_recettes
            FROM {$this->table} c
            ORDER BY c.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère une catégorie par son ID
     * @param int $id L'ID de la catégorie à récupérer
     * @return array|false La catégorie trouvée ou false si non trouvée
     */
    public function findByIdCategories(int $id) : array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Vérifie si une catégorie avec ce nom existe déjà
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
     * Vérifie si une catégorie avec ce nom existe déjà, sauf pour l'ID spécifié
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
     * Crée une nouvelle catégorie
     * @param array $data Les données de la catégorie
     * @return int|bool L'ID de la catégorie créée ou false en cas d'échec
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (nom, descriptif, couleur, image_url, id_admin, couleurTexte) 
                VALUES (:nom, :descriptif, :couleur, :image_url, :id_admin, :couleurTexte)";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':descriptif', $data['descriptif']);
        $stmt->bindParam(':couleur', $data['couleur']);
        $stmt->bindParam(':image_url', $data['image_url']);
        $stmt->bindParam(':id_admin', $data['id_admin'], PDO::PARAM_INT);
        $stmt->bindParam(':couleurTexte', $data['couleurTexte']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour une catégorie existante
     * @param int $id L'ID de la catégorie à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool True si la mise à jour a réussi, sinon false
     */
    public function update(int $id, array $data) : bool
    {
        $sql = "UPDATE {$this->table} SET nom = :nom, descriptif = :descriptif, couleur = :couleur, couleurTexte = :couleurTexte";
        
        if (isset($data['image_url'])) {
            $sql .= ", image_url = :image_url";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':descriptif', $data['descriptif']);
        $stmt->bindParam(':couleur', $data['couleur']);
        $stmt->bindParam(':couleurTexte', $data['couleurTexte']);
        
        if (isset($data['image_url'])) {
            $stmt->bindParam(':image_url', $data['image_url']);
        }
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime une catégorie
     * @param int $id L'ID de la catégorie à supprimer
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
     * Vérifie si une catégorie est utilisée dans des recettes
     * @param int $id L'ID de la catégorie à vérifier
     * @return bool True si la catégorie est utilisée, sinon false
     */
    public function isUsedInRecipes(int $id) : bool
    {
        $sql = "SELECT COUNT(*) FROM recette WHERE id_categorie = :id_categorie";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_categorie', $id, PDO::PARAM_INT);
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