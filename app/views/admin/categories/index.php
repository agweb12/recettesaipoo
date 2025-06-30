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

        <!-- Actions -->
        <div class="actions-bar">
            <a href="<?= RACINE_SITE ?>admin/categories/create" class="cta">
                <i class="fi fi-sr-plus"></i> Ajouter une catégorie
            </a>
        </div>

        <!-- Tableau des catégories -->
        <div class="categories-table-container">
            <h3>Liste des catégories</h3>
            <div class="table-responsive">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Couleur</th>
                            <th>Couleur Texte</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Utilisée dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="8">Aucune catégorie trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $categorie): ?>
                                <tr>
                                    <td><?= $categorie['id'] ?></td>
                                    <td><?= htmlspecialchars($categorie['nom']) ?></td>
                                    <td>
                                        <div class="color-preview-cell" style="background-color: <?= htmlspecialchars($categorie['couleur']) ?>" title="<?= htmlspecialchars($categorie['couleur']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="color-preview-cell" style="background-color: <?= htmlspecialchars($categorie['couleurTexte']) ?>" title="<?= htmlspecialchars($categorie['couleurTexte']) ?>"></div>
                                    </td>
                                    <td class="description-cell">
                                        <?= !empty($categorie['descriptif']) ? htmlspecialchars(substr($categorie['descriptif'], 0, 50)) . (strlen($categorie['descriptif']) > 50 ? '...' : '') : '<em>Aucune description</em>' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($categorie['image_url'])): ?>
                                            <img src="<?= RACINE_SITE . $categorie['image_url'] ?>" alt="<?= htmlspecialchars($categorie['nom']) ?>" class="thumbnail">
                                        <?php else: ?>
                                            <em>Aucune image</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cette catégorie">
                                            <?= $categorie['nb_recettes'] ?> recette(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="<?= RACINE_SITE ?>admin/categories/edit/<?= $categorie['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($categorie['nb_recettes'] == 0): ?>
                                        <form method="post" action="<?= RACINE_SITE ?>admin/categories/delete/<?= $categorie['id'] ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">Supprimer</button>
                                        </form>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cette catégorie est utilisée et ne peut pas être supprimée" disabled>Supprimer</button>
                                        <?php endif; ?>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminCategorie.js"></script>