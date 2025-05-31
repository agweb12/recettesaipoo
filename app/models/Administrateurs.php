<?php
// app/models/Administrateurs.php
namespace App\Models;

use PDO;
use App\Core\Model;

class Administrateurs extends Model{
    protected $table = "administrateur";

    /**
     * Authentifie un administrateur
     * @param string $email Email de l'administrateur
     * @param string $password Mot de passe de l'administrateur
     * @return array|bool Les données de l'administrateur ou false si échec
     */
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['mot_de_passe'])) {
            // Ne pas retourner le mot de passe
            unset($admin['mot_de_passe']);
            return $admin;
        }
        
        return false;
    }

    /**
     * Récupère un administrateur par son ID
     * @param int $id ID de l'administrateur
     * @return array|bool Les données de l'administrateur ou false si non trouvé
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            unset($admin['mot_de_passe']);
            return $admin;
        }
        
        return false;
    }


    // ADMINISTRATIVE FUNCTIONS
    /**
     * Récupère tous les administrateurs
     * @return array
     */
    public function getAllAdmins() : array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie si un email existe déjà 
     * @param string $email Email à vérifier
     * @return bool
     */
    public function emailExists(string $email) : bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Vérifie si un email existe déjà pour un autre administrateur que celui spécifié
     * @param string $email Email à vérifier
     * @param int $excludeId ID de l'administrateur à exclure de la vérification
     * @return bool
     */
    public function emailExistsForOthers(string $email, int $excludeId) : bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email AND id != :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Crée un nouvel administrateur
     * @param array $data Les données de l'administrateur
     * @return int|bool L'ID de l'administrateur créé ou false en cas d'échec
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (nom, prenom, email, mot_de_passe, role) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, :role)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':mot_de_passe', $data['mot_de_passe']);
        $stmt->bindParam(':role', $data['role']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour un administrateur existant
     * @param int $id L'ID de l'administrateur à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool True si la mise à jour a réussi, sinon false
     */
    public function update(int $id, array $data) : bool
    {
        $fields = [];
        $params = [];
        
        // Construction dynamique des champs à mettre à jour
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $params[':id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Supprime un administrateur
     * @param int $id L'ID de l'administrateur à supprimer
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
     * Récupère les actions d'un administrateur
     * @param int $adminId L'ID de l'administrateur
     * @return array Les actions de l'administrateur
     */
    public function getAdminActions(int $adminId) : array
    {
        $sql = "SELECT * FROM administrateur_actions WHERE id_admin = :id_admin ORDER BY date_action DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_admin', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
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