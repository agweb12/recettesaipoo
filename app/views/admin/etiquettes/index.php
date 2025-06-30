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

        <?php if (!$etiquetteEdit): ?>
            <!-- Formulaire d'ajout d'une étiquette -->
            <div class="add-form-container">
                <h3>Ajouter une nouvelle étiquette</h3>
                <form method="POST" action="<?= RACINE_SITE ?>admin/etiquettes/store" class="add-form">
                    <?= csrf_field() ?>
                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'étiquette *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                                <small>Le nom doit être unique et descriptif (ex: "Sans gluten 🚫🌾")</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Descriptif</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3"></textarea>
                                <small>Une courte description de l'étiquette (ex: "Adapté aux intolérants")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter l'étiquette</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'une étiquette -->
            <div class="edit-form-container">
                <h3>Modifier l'étiquette</h3>
                <form method="POST" action="<?= RACINE_SITE ?>admin/etiquettes/update/<?= $etiquetteEdit['id'] ?>" class="edit-form">
                    <?= csrf_field() ?>
                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'étiquette *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($etiquetteEdit['nom']) ?>" required>
                                <small>Le nom doit être unique et descriptif (ex: "Sans gluten 🚫🌾")</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Descriptif</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3"><?= htmlspecialchars($etiquetteEdit['descriptif'] ?? '') ?></textarea>
                                <small>Une courte description de l'étiquette (ex: "Adapté aux intolérants")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour l'étiquette</button>
                        <a href="<?= RACINE_SITE ?>admin/etiquettes" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tableau des étiquettes -->
        <div class="etiquettes-table-container">
            <h3>Liste des étiquettes</h3>
            <div class="table-responsive">
                <table class="etiquettes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Descriptif</th>
                            <th>Date de création</th>
                            <th>Utilisée dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($etiquettes)): ?>
                            <tr>
                                <td colspan="6">Aucune étiquette trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($etiquettes as $etiquette): ?>
                                <tr>
                                    <td><?= $etiquette['id'] ?></td>
                                    <td><?= htmlspecialchars($etiquette['nom']) ?></td>
                                    <td><?= htmlspecialchars($etiquette['descriptif'] ?? '') ?></td>
                                    <td><?= date('d/m/Y', strtotime($etiquette['date_creation'])) ?></td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cette étiquette">
                                            <?= $etiquette['nb_recettes'] ?> recette(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="<?= RACINE_SITE ?>admin/etiquettes/edit/<?= $etiquette['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($etiquette['nb_recettes'] == 0): ?>
                                            <form method="POST" action="<?= RACINE_SITE ?>admin/etiquettes/delete/<?= $etiquette['id'] ?>" class="delete-form" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette étiquette ?')">Supprimer</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cette étiquette est utilisée et ne peut pas être supprimée" disabled>Supprimer</button>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminEtiquette.js"></script>