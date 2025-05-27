<?php
// app/core/Model.php
namespace App\Core; // Définition de l'espace de noms pour la classe Model
use App\Core\Database; // Importation de la classe Database pour la connexion à la base de données
use PDO; // Importation de la classe PDO pour interagir avec la base de données
/**
 * Classe Model qui sert de base pour les modèles de l'application.
 * Elle gère la connexion à la base de données et fournit des méthodes génériques pour interagir avec les tables.
 */
class Model {
    protected $db; // protected permet de protéger la variable $db pour qu'elle ne soit accessible qu'aux classes qui étendent Model
    protected $table; // Nom de la table dans la base de données, à définir dans les classes qui étendent Model
    
    // Le constructeur initialise l'instance PDO de la base de données
    public function __construct() {
        $this->db = Database::getInstance()->getPdo(); // Database::getInstance() retourne l'instance de la classe Database et getPdo() retourne l'instance PDO
    }
    
    // Méthodes findAll et findById pour récupérer des enregistrements
    // Ces méthodes sont génériques et peuvent être utilisées pour n'importe quelle table
    // $this représente l'instance de la classe qui étend Model
    // Donc lorsque je fait $this->db, je fais référence à l'instance PDO de la base de données
    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // Autres méthodes génériques CRUD à déterminer
}