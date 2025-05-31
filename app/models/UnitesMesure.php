<?php
namespace App\Models;
use App\Core\Model;
use PDO;

/**
 * Classe UnitesMesure qui étend la classe Model pour interagir avec la table des unités de mesure.
 */
class UnitesMesure extends Model {
    protected $table = 'unite_mesure';

    /**
     * Récupère toutes les unités de mesure
     * @return array Toutes les unités de mesure
     */
    public function getAllUnitesMesure() : array
    {
        $sql = "SELECT id, nom, abreviation FROM {$this->table} ORDER BY nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère toutes les unités de mesure avec le nombre de recettes qui les utilisent
     * @return array Unités de mesure avec informations d'utilisation
     */
    public function getAllUnitesMesureWithUsage() : array
    {
        $sql = "SELECT um.*, 
                (SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_unite = um.id) AS nb_recettes
            FROM {$this->table} um
            ORDER BY um.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie si une unité avec ce nom ou cette abréviation existe déjà
     * @param string $name Le nom à vérifier
     * @param string $abbr L'abréviation à vérifier
     * @return bool True si le nom ou l'abréviation existe déjà, sinon false
     */
    public function existsByNameOrAbbreviation(string $name, string $abbr) : bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE nom = :nom OR abreviation = :abreviation";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $name);
        $stmt->bindParam(':abreviation', $abbr);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Vérifie si une unité avec ce nom ou cette abréviation existe déjà, sauf pour l'ID spécifié
     * @param string $name Le nom à vérifier
     * @param string $abbr L'abréviation à vérifier
     * @param int $id L'ID à exclure de la vérification
     * @return bool True si le nom ou l'abréviation existe déjà pour un autre ID, sinon false
     */
    public function existsByNameOrAbbreviationExcept(string $name, string $abbr, int $id) : bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE (nom = :nom OR abreviation = :abreviation) AND id != :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $name);
        $stmt->bindParam(':abreviation', $abbr);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
    
    /**
     * Crée une nouvelle unité de mesure
     * @param array $data Les données de l'unité
     * @return int|bool L'ID de l'unité créée ou false en cas d'échec
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (nom, abreviation) VALUES (:nom, :abreviation)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':abreviation', $data['abreviation']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour une unité de mesure existante
     * @param int $id L'ID de l'unité à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool True si la mise à jour a réussi, sinon false
     */
    public function update(int $id, array $data) : bool
    {
        $sql = "UPDATE {$this->table} SET nom = :nom, abreviation = :abreviation WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':abreviation', $data['abreviation']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime une unité de mesure
     * @param int $id L'ID de l'unité à supprimer
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
     * Vérifie si une unité de mesure est utilisée dans des recettes
     * @param int $id L'ID de l'unité à vérifier
     * @return bool True si l'unité est utilisée, sinon false
     */
    public function isUsedInRecipes(int $id) : bool
    {
        $sql = "SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_unite = :id_unite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_unite', $id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
}