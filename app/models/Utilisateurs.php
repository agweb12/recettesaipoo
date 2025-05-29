<?php
// app/models/Utilisateur.php

namespace App\Models;

use App\Core\Model;
use PDO;

class Utilisateurs extends Model {
    protected $table = 'utilisateur';

    /**
     * Récupère un utilisateur par son ID
     * @param int $id ID de l'utilisateur
     * @return array|false L'utilisateur ou false si non trouvé
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Créer un nouvel utilisateur
     * @param array $data Les données de l'utilisateur (nom, prenom, email, mot_de_passe)
     * @return bool|int L'ID du nouvel utilisateur ou false en cas d'erreur
     */
    public function createRegistration($data) : bool|int
    {
        $sql = "INSERT INTO {$this->table} (nom, prenom, email, mot_de_passe, date_inscription) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom'], PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $data['prenom'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':mot_de_passe', $data['mot_de_passe'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Valider les données d'inscription
     * @param array $data Les données à valider
     * @return array Les erreurs de validation
     */
    public function validateRegistrationData($data) : array
    {
        $errors = [];

        // Validation du nom
        $regexNom = "/^\p{L}[\p{L}\s-]*$/u";
        if (empty($data['nom'])) {
            $errors['nom'] = "Le champ nom est requis";
        } elseif (!preg_match($regexNom, $data['nom'])) {
            $errors['nom'] = "Le nom ne peut contenir que des lettres";
        } elseif (strlen($data['nom']) < 2) {
            $errors['nom'] = "Le nom doit contenir au moins 2 caractères";
        } elseif (strlen($data['nom']) > 50) {
            $errors['nom'] = "Le nom ne doit pas dépasser 50 caractères";
        }
        
        // Validation du prénom
        if (empty($data['prenom'])) {
            $errors['prenom'] = "Le champ prénom est requis";
        } elseif (!preg_match($regexNom, $data['prenom'])) {
            $errors['prenom'] = "Le prénom ne peut contenir que des lettres";
        } elseif (strlen($data['prenom']) < 2) {
            $errors['prenom'] = "Le prénom doit contenir au moins 2 caractères";
        } elseif (strlen($data['prenom']) > 50) {
            $errors['prenom'] = "Le prénom ne doit pas dépasser 50 caractères";
        }
        
        // Validation de l'email
        if (empty($data['email'])) {
            $errors['email'] = "Le champ email est requis";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide";
        } elseif ($this->emailExists($data['email'])) {
            $errors['email'] = "Cette adresse email est déjà utilisée";
        }
        
        // Validation du mot de passe
        $regexPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (empty($data['password'])) {
            $errors['password'] = "Le champ mot de passe est requis";
        } elseif (!preg_match($regexPassword, $data['password'])) {
            $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
        }
        
        return $errors;
    }

    /**
     * Valider les données de connexion
     * @param array $data Les données à valider
     * @return array Les erreurs de validation
     */
    public function validateLoginData($data) : array
    {
        $errors = [];
        
        if (empty($data['email'])) {
            $errors['email'] = "Le champ email est requis";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide";
        }
        
         $regexPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (empty($data['password'])) {
            $errors['password'] = "Le champ mot de passe est requis";
        } elseif (!preg_match($regexPassword, $data['password'])) {
            $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
        }
        
        return $errors;
    }

    /**
     * Rechercher un utilisateur par email
     * @param string $email L'email de l'utilisateur
     * @return array|false Les données de l'utilisateur ou false si non trouvé
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Authentifier un utilisateur
     * @param string $email L'email de l'utilisateur
     * @param string $password Le mot de passe en clair
     * @return array|false Les données de l'utilisateur si authentifié, false sinon
     */
    public function authenticate($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Retourner les données utilisateur sans le mot de passe
            unset($user['mot_de_passe']);
            return $user;
        }
        return false;
    }

    /**
     * Vérifier si un email existe déjà
     * @param string $email L'email à vérifier
     * @return bool True si l'email existe, false sinon
     */
    public function emailExists($email) : bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Changer le mot de passe d'un utilisateur
     * @param int $id L'ID de l'utilisateur
     * @param string $newPassword Le nouveau mot de passe (déjà hashé)
     * @return bool True si le changement a réussi, false sinon
     */
    public function updatePassword($id, $newPassword) : bool
    {
        $sql = "UPDATE {$this->table} SET mot_de_passe = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprimer un utilisateur et ses données associées
     * @param int $id L'ID de l'utilisateur
     * @return bool True si la suppression a réussi, false sinon
     */
    public function deleteUser($id) : bool
    {
        try {
            $this->db->beginTransaction();
            
            // Supprimer les favoris
            $stmt1 = $this->db->prepare("DELETE FROM recette_favorite WHERE id_utilisateur = :id");
            $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt1->execute();
            
            // Supprimer les ingrédients personnels
            $stmt2 = $this->db->prepare("DELETE FROM liste_personnelle_ingredients WHERE id_utilisateur = :id");
            $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt2->execute();
            
            // Supprimer l'utilisateur
            $stmt3 = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt3->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt3->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Récupérer les recettes favorites d'un utilisateur
     * @param int $userId L'ID de l'utilisateur
     * @return array Les recettes favorites
     */
    public function getFavoriteRecipes($userId) : array
    {
        $sql = "SELECT r.*, c.nom as categorie_nom, c.couleur as categorie_couleur, c.couleurTexte as couleurTexte 
                FROM recette_favorite rf
                JOIN recette r ON rf.id_recette = r.id
                JOIN categorie c ON r.id_categorie = c.id
                WHERE rf.id_utilisateur = :id_utilisateur
                ORDER BY rf.date_enregistrement DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}