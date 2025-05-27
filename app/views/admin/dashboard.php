<?php
require_once('../../inc/functions.php');
$titlePage = "Dashboard Admin";
$descriptionPage = "Tableau de bord de l'équipe Recette AI";
$indexPage = "noindex";
$followPage = "nofollow";
$keywordsPage = "";
$info = "";

// Vérification des droits d'accès
if (!isset($_SESSION['admin'])) {
    header('Location: ' . RACINE_SITE . 'index.php');
    exit();
}

// Connexion à la base de données
$pdo = connexionBDD();

// Récupération des compteurs pour chaque table
$counters = array();

// Catégories
$stmt = $pdo->query("SELECT COUNT(*) as total FROM categorie");
$counters['categories'] = $stmt->fetch()['total'];

// Utilisateurs
$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateur");
$counters['utilisateurs'] = $stmt->fetch()['total'];

// Administrateurs
$stmt = $pdo->query("SELECT COUNT(*) as total FROM administrateur");
$counters['administrateurs'] = $stmt->fetch()['total'];

// Recettes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM recette");
$counters['recettes'] = $stmt->fetch()['total'];

// Étiquettes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM etiquette");
$counters['etiquettes'] = $stmt->fetch()['total'];

// Unités de mesure
$stmt = $pdo->query("SELECT COUNT(*) as total FROM unite_mesure");
$counters['unites'] = $stmt->fetch()['total'];

// Ingrédients
$stmt = $pdo->query("SELECT COUNT(*) as total FROM ingredient");
$counters['ingredients'] = $stmt->fetch()['total'];

require_once('../headerAdmin.php');
?>
<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>views/admin/dashboard.php">Dashboard</a>
            <p>/</p>
            <p><?= $titlePage ?></p>
        </div>
    </section>
</section>
<section class="sectionDashboard">
    <div class="box-dashboard">
        <h2>Bienvenue sur le tableau de bord</h2>
        <p>Vous pouvez gérer les différentes sections du site Recette AI.</p>
    </div>
</section>
<section class="totalSection">
    <div class="banner-dashboard">
        <a class="box-dashboard" href="<?= RACINE_SITE ?>views/admin/manageCategories.php">
            <h3>Gérer les Catégories</h3>
            <p class="numberItems"><?= $counters['categories'] ?> <?= ($counters['categories'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>views/admin/manageUtilisateurs.php">
            <h3>Gérer les Utilisateurs</h3>
            <p class="numberItems"><?= $counters['utilisateurs'] ?> <?= ($counters['utilisateurs'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>views/admin/manageAdministrateurs.php">
            <h3>Gérer les Administrateurs</h3>
            <p class="numberItems"><?= $counters['administrateurs'] ?> <?= ($counters['administrateurs'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>views/admin/manageRecettes.php">
            <h3>Gérer les Recettes</h3>
            <p class="numberItems"><?= $counters['recettes'] ?> <?= ($counters['recettes'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>views/admin/manageEtiquettes.php">
            <h3>Gérer les Etiquettes</h3>
            <p class="numberItems"><?= $counters['etiquettes'] ?> <?= ($counters['etiquettes'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>views/admin/manageUnitesMesure.php">
            <h3>Gérer les Unités de Mesure</h3>
            <p class="numberItems"><?= $counters['unites'] ?> <?= ($counters['unites'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>views/admin/manageIngredients.php">
            <h3>Gérer les Ingrédients</h3>
            <p class="numberItems"><?= $counters['ingredients'] ?> <?= ($counters['ingredients'] > 1) ? 'items' : 'item' ?></p>
        </a>
    </div>
</section>

<?php
require_once('../footerAdmin.php');
?>