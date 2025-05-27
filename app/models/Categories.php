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
}