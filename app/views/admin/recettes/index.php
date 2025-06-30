<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>admin/dashboard">Dashboard</a>
            <p>/</p>
            <p><?= $titlePage ?></p>
        </div>
    </section>
</section>

<section class="admin-section">
    <div class="admin-container">
        <?php if (!empty($info)): ?>
            <?= alert($info, $infoType) ?>
        <?php endif; ?>

        <?php if (!$recetteEdit): ?>
            <!-- Formulaire d'ajout d'une recette -->
            <?php require_once(__DIR__ . '/create.php'); ?>
        <?php else: ?>
            <!-- Formulaire de modification d'une recette -->
            <?php require_once(__DIR__ . '/edit.php'); ?>
        <?php endif; ?>
        
        <!-- Tableau des recettes -->
        <div class="recipes-table-container">
            <h3>Liste des recettes</h3>
            <div class="table-responsive">
                <table class="recipes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Durée</th>
                            <th>Difficulté</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recettes)): ?>
                            <tr>
                                <td colspan="7">Aucune recette trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recettes as $recette): ?>
                                <tr>
                                    <td><?= $recette['id'] ?></td>
                                    <td><?= htmlspecialchars($recette['nom']) ?></td>
                                    <td>
                                        <span class="category-badge" style="background-color: <?= htmlspecialchars($recette['couleur_categorie']) ?>">
                                            <?= htmlspecialchars($recette['categorie']) ?>
                                        </span>
                                    </td>
                                    <td><?= $recette['temps_preparation'] + $recette['temps_cuisson'] ?> min</td>
                                    <td>
                                        <span class="difficulty-badge difficulty-<?= $recette['difficulte'] ?>">
                                            <?= ucfirst($recette['difficulte']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($recette['date_creation'])) ?></td>
                                    <td class="actions">
                                        <a href="<?= RACINE_SITE ?>recettes/recette?id=<?= $recette['id'] ?>" class="btn-view" target="_blank">Voir</a>
                                        <a href="<?= RACINE_SITE ?>admin/recettes/edit/<?= $recette['id'] ?>" class="btn-edit">Modifier</a>
                                        <form method="POST" action="<?= RACINE_SITE ?>admin/recettes/delete/<?= $recette['id'] ?>" class="delete-form" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminRecipe.js"></script>