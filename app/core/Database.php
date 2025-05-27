<?php
// app/core/Database.php
namespace App\Core; // Définition de l'espace de noms pour la classe Database
use PDO; // Importation de la classe PDO pour la connexion à la base de données
use PDOException; // Importation de la classe PDOException pour gérer les exceptions liées à PDO
use Exception; // Importation de la classe Exception pour gérer les exceptions générales
/**
 * Classe Database pour gérer la connexion à la base de données
 * Utilise le design pattern Singleton pour garantir qu'il n'y a qu'une seule instance de la classe
 */
class Database {
    private static $instance = null; // Singleton instance c'est-à-dire qu'il n'y aura qu'une seule instance de cette classe
    // La propriété $instance est statique, ce qui signifie qu'elle est partagée entre toutes les instances de la classe
    // Elle est initialisée à null, ce qui signifie qu'aucune instance n'a été créée pour le moment
    // Elle sera utilisée pour stocker l'instance unique de la classe Database
    private $pdo; // PDO instance pour la connexion à la base de données. $pdo est une instance de la classe PDO qui est utilisée pour interagir avec la base de données
    // La propriété $pdo est privée, ce qui signifie qu'elle ne peut être accédée qu'à l'intérieur de la classe Database
    // Elle est utilisée pour stocker l'instance PDO qui sera utilisée pour exécuter des requêtes SQL

    private function __construct(){ // Le constructeur est privé pour empêcher la création d'instances de cette classe en dehors de la méthode getInstance
        $host = "localhost";
        $dbname = "recetteai"; // Vérifiez que c'est bien le bon nom de BDD
        $user = "root";
        $password = "";

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8"; // Data Source Name (DSN) pour la connexion à la base de données
        $options = [ // Options pour la connexion PDO
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    /* *** ELLE CONTIENT 3 FONCTIONS ESSENTIELLES *** */

    // Méthode pour obtenir l'instance de la classe Database
    // Cette méthode est statique, ce qui signifie qu'elle peut être appelée sans créer une instance de la classe
    // Elle vérifie si l'instance existe déjà, sinon elle la crée
    public static function getInstance(){
        // Si l'instance n'existe pas, on la crée
        if(self::$instance === null){ // self:: permet d'accéder aux propriétés et méthodes statiques de la classe
            self::$instance = new self(); 
        }

        return self::$instance; // Retourne l'instance de la classe
    }


    // Méthode pour obtenir l'instance PDO
    // Cette méthode permet d'accéder à l'instance PDO pour exécuter des requêtes SQL
    // Elle est utile pour les classes qui ont besoin d'interagir avec la base de données
    // Elle retourne l'instance PDO
    // qui est utilisée pour préparer et exécuter des requêtes SQL
    // Elle est privée pour empêcher la création d'instances de cette classe en dehors de la méthode getInstance
    public function getPdo(){
        return $this->pdo;
    }

    // Méthode pour exécuter une requête SQL
    // Cette méthode prend une requête SQL et des paramètres optionnels
    // Elle prépare la requête, l'exécute et retourne le résultat
    // Elle est utile pour les classes qui ont besoin d'exécuter des requêtes SQL
    // Elle utilise l'instance PDO pour préparer et exécuter la requête
    // Elle retourne l'objet PDOStatement qui contient le résultat de la requête
    // Elle est publique pour permettre aux classes qui ont besoin d'exécuter des requêtes SQL de le faire
    // Elle prend en paramètre une requête SQL et un tableau de paramètres optionnels
    // Elle utilise la méthode prepare de l'instance PDO pour préparer la requête
    public function query($sql, $params = []){
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt;
    }
}