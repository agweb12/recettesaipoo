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

        <?php if (!$uniteEdit): ?>
            <!-- Formulaire d'ajout d'une unité de mesure -->
            <div class="add-form-container">
                <h3>Ajouter une nouvelle unité de mesure</h3>
                <form method="POST" action="<?= RACINE_SITE ?>admin/unites-mesure/store" class="add-form">
                    <?= csrf_field() ?>
                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'unité *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                                <small>Ex: gramme, litre, cuillère à soupe, etc.</small>
                            </div>
                            <div class="form-group">
                                <label for="abreviation" class="form-label">Abréviation *</label>
                                <input type="text" class="form-control" id="abreviation" name="abreviation" maxlength="10" required>
                                <small>Ex: g, L, cas, etc.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter l'unité de mesure</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'une unité de mesure -->
            <div class="edit-form-container">
                <h3>Modifier l'unité de mesure</h3>
                <form method="POST" action="<?= RACINE_SITE ?>admin/unites-mesure/update/<?= $uniteEdit['id'] ?>" class="edit-form">
                    <?= csrf_field() ?>
                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'unité *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($uniteEdit['nom']) ?>" required>
                                <small>Ex: gramme, litre, cuillère à soupe, etc.</small>
                            </div>
                            <div class="form-group">
                                <label for="abreviation" class="form-label">Abréviation *</label>
                                <input type="text" class="form-control" id="abreviation" name="abreviation" maxlength="10" value="<?= htmlspecialchars($uniteEdit['abreviation']) ?>" required>
                                <small>Ex: g, L, cas, etc.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour l'unité de mesure</button>
                        <a href="<?= RACINE_SITE ?>admin/unites-mesure" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tableau des unités de mesure -->
        <div class="unites-table-container">
            <h3>Liste des unités de mesure</h3>
            <div class="table-responsive">
                <table class="unites-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Abréviation</th>
                            <th>Utilisée dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($unites)): ?>
                            <tr>
                                <td colspan="5">Aucune unité de mesure trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($unites as $unite): ?>
                                <tr>
                                    <td><?= $unite['id'] ?></td>
                                    <td><?= htmlspecialchars($unite['nom']) ?></td>
                                    <td><?= htmlspecialchars($unite['abreviation']) ?></td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cette unité">
                                            <?= $unite['nb_recettes'] ?> recette(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="<?= RACINE_SITE ?>admin/unites-mesure/edit/<?= $unite['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($unite['nb_recettes'] == 0): ?>
                                            <form method="POST" action="<?= RACINE_SITE ?>admin/unites-mesure/delete/<?= $unite['id'] ?>" class="delete-form" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette unité de mesure ?')">Supprimer</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cette unité est utilisée dans des recettes et ne peut pas être supprimée" disabled>Supprimer</button>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminUniteMesure.js"></script>