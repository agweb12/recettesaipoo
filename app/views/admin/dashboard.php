<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>admin/dashboard">Dashboard</a>
            <p>/</p>
            <p><?= $titlePage ?></p>
        </div>
    </section>
</section>
<section class="sectionDashboard">
    <div class="box-dashboard">
        <h2>Tableau de bord</h2>
        <p class="alert alert-warning"><b><?= $_SESSION['admin']['prenom']?></b>, tu es bien connecté en tant que <b><?= $_SESSION['admin']['role']?></b></p>
        <p class="alert alert-info">Tu peux gérer les différentes sections du site Recette AI.</p>
    </div>
</section>
<section class="totalSection">
    <div class="banner-dashboard">
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/categories">
            <h3>Gérer les Catégories</h3>
            <p class="numberItems"><?= $counters['categories'] ?> <?= ($counters['categories'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/utilisateurs">
            <h3>Gérer les Utilisateurs</h3>
            <p class="numberItems"><?= $counters['utilisateurs'] ?> <?= ($counters['utilisateurs'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/administrateurs">
            <h3>Gérer les Administrateurs</h3>
            <p class="numberItems"><?= $counters['administrateurs'] ?> <?= ($counters['administrateurs'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/recettes">
            <h3>Gérer les Recettes</h3>
            <p class="numberItems"><?= $counters['recettes'] ?> <?= ($counters['recettes'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/etiquettes">
            <h3>Gérer les Etiquettes</h3>
            <p class="numberItems"><?= $counters['etiquettes'] ?> <?= ($counters['etiquettes'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/unites-mesure">
            <h3>Gérer les Unités de Mesure</h3>
            <p class="numberItems"><?= $counters['unites'] ?> <?= ($counters['unites'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/ingredients">
            <h3>Gérer les Ingrédients</h3>
            <p class="numberItems"><?= $counters['ingredients'] ?> <?= ($counters['ingredients'] > 1) ? 'items' : 'item' ?></p>
        </a>
    </div>
</section>