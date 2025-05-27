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

    // Méthode pour récupérer les recettes populaires
    public function getPopularRecipes($limit = 3) {
        $sql = "SELECT r.*, COUNT(rf.id_recette) AS nb_favoris FROM {$this->table} r 
        LEFT JOIN recette_favorite rf ON r.id = rf.id_recette GROUP BY r.id ORDER BY nb_favoris DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Méthode pour récupérer les recettes les plus récentes
    public function getRecentRecipes($limit = 3) {
        $sql = "SELECT * FROM {$this->table} ORDER BY date_creation DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}