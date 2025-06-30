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

        <?php if (!$ingredientEdit): ?>
            <!-- Formulaire d'ajout d'un ingrédient -->
            <div class="add-form-container">
                <h3>Ajouter un nouvel ingrédient</h3>
                <form method="POST" action="<?= RACINE_SITE ?>admin/ingredients/store" class="add-form">
                    <?= csrf_field() ?>
                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'ingrédient *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                                <small>Le nom doit être unique et descriptif (ex: "farine de blé" plutôt que "farine")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter l'ingrédient</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'un ingrédient -->
            <div class="edit-form-container">
                <h3>Modifier l'ingrédient</h3>
                <form method="POST" action="<?= RACINE_SITE ?>admin/ingredients/update/<?= $ingredientEdit['id'] ?>" class="edit-form">
                    <?= csrf_field() ?>
                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'ingrédient *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($ingredientEdit['nom']) ?>" required>
                                <small>Le nom doit être unique et descriptif (ex: "farine de blé" plutôt que "farine")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour l'ingrédient</button>
                        <a href="<?= RACINE_SITE ?>admin/ingredients" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tableau des ingrédients -->
        <div class="ingredients-table-container">
            <h3>Liste des ingrédients</h3>
            <div class="table-responsive">
                <table class="ingredients-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Date de création</th>
                            <th>Utilisé dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ingredients)): ?>
                            <tr>
                                <td colspan="5">Aucun ingrédient trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ingredients as $ingredient): ?>
                                <tr>
                                    <td><?= $ingredient['id'] ?></td>
                                    <td><?= htmlspecialchars($ingredient['nom']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($ingredient['date_creation'])) ?></td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cet ingrédient">
                                            <?= $ingredient['nb_recettes'] ?> recette(s)
                                        </span>
                                        <span class="usage-badge" title="Nombre de listes personnelles contenant cet ingrédient">
                                            <?= $ingredient['nb_listes_perso'] ?> liste(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="<?= RACINE_SITE ?>admin/ingredients/edit/<?= $ingredient['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($ingredient['nb_recettes'] == 0 && $ingredient['nb_listes_perso'] == 0): ?>
                                            <form method="POST" action="<?= RACINE_SITE ?>admin/ingredients/delete/<?= $ingredient['id'] ?>" class="delete-form" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet ingrédient ?')">Supprimer</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cet ingrédient est utilisé et ne peut pas être supprimé" disabled>Supprimer</button>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminIngredient.js"></script>