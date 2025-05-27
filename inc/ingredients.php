<?php
require_once('functions.php'); // Inclure le fichier de fonctions

// En-têtes pour autoriser les requêtes AJAX et définir le format de réponse
header('Content-Type: application/json');

// Vérifier si une recherche a été effectuée
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Connexion à la base de données
$pdo = connexionBDD();

// Requête pour rechercher les ingrédients correspondant à la recherche
$stmt = $pdo->prepare("SELECT id, nom FROM ingredient WHERE nom LIKE :search ORDER BY nom ASC LIMIT 10");
$stmt->execute(['search' => '%' . $search . '%']);
$ingredients = $stmt->fetchAll();

// Retourner les résultats au format JSON
echo json_encode($ingredients);