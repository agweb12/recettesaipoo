<?php
require_once('functions.php');

// Je vérifie si l'utilisateur est connecté
if(!isLoggedIn()){
    header('Content-Type: application/json');
    // json_encode() permet de convertir un tableau PHP en JSON
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter cette recette aux favoris']);
    exit;
}

// Je vérifie si la requête est une requête POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
    exit;
}

// Je récupère les données de la requête envoyée
if(!isset($_POST['id_recette']) || !is_numeric($_POST['id_recette']) || !isset($_POST['action'])){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$recipeId = intval($_POST['id_recette']); // Récupération de l'ID de la recette
$action = $_POST['action']; // 'add' ou 'remove'
$userId = $_SESSION['user']['id']; // Récupération de l'ID de l'utilisateur connecté

$pdo = connexionBDD(); // Connexion à la base de données

try{
    if($action === 'add'){
        // Je vérifie si la recette est déjà dans les favoris
        $sqlCheckFavorite = "SELECT COUNT(*) as count_favoris FROM recette_favorite WHERE id_utilisateur = :id_utilisateur AND id_recette = :id_recette";
        $stmtCheckFavorite = $pdo->prepare($sqlCheckFavorite);
        $stmtCheckFavorite->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmtCheckFavorite->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmtCheckFavorite->execute();
        $resultCheckFavorite = $stmtCheckFavorite->fetch();

        if($resultCheckFavorite['count_favoris'] == 0){
            // J'ajoute la recette aux favoris
            $sqlAddFavorite = "INSERT INTO recette_favorite (id_utilisateur, id_recette, date_enregistrement) 
                            VALUES (:id_utilisateur, :id_recette, NOW())";
            $stmtAddFavorite = $pdo->prepare($sqlAddFavorite);
            $stmtAddFavorite->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
            $stmtAddFavorite->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
            $stmtAddFavorite->execute();

            // Je renvoie une réponse JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Recette ajoutée aux favoris']);
        } else{
            // Je renvoie une réponse JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cette recette est déjà dans vos favoris']);
        }
    } elseif ($action === 'remove') {
        // Supprimer des favoris
        $stmtRemoveFavorite = $pdo->prepare('DELETE FROM recette_favorite WHERE id_utilisateur = :id_utilisateur AND id_recette = :id_recette');
        $stmtRemoveFavorite->execute([
            ':id_utilisateur' => $userId,
            ':id_recette' => $recipeId
        ]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Recette retirée des favoris']);

    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
} catch (PDOException $e){
    // En cas d'erreur, je renvoie une réponse JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout aux favoris : ' . $e->getMessage()]);
}
?>