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
     * Récupère les étiquettes d'une recette
     * @param int $recipeId ID de la recette
     * @return array Les étiquettes de la recette
     */
    public function getRecipeEtiquettes($recipeId) : array
    {
        $sql = "SELECT e.id, e.nom FROM {$this->table} e 
                JOIN recette_etiquette re ON e.id = re.id_etiquette 
                WHERE re.id_recette = :id_recette";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}