<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>admin/dashboard">Dashboard</a>
            <p>/</p>
            <a class="crumb" href="<?= RACINE_SITE ?>admin/categories">Catégories</a>
            <p>/</p>
            <p>Modifier</p>
        </div>
    </section>
</section>

<section class="admin-section">
    <div class="admin-container">
        <?php if (isset($_GET['info'])): ?>
            <?= alert($_GET['info'], $_GET['type'] ?? 'info') ?>
        <?php endif; ?>

        <!-- Formulaire de modification d'une catégorie -->
        <div class="edit-form-container">
            <h3>Modifier la catégorie</h3>
            <form method="POST" action="<?= RACINE_SITE ?>admin/categories/update/<?= $categorie['id'] ?>" class="edit-form" enctype="multipart/form-data">
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom" class="form-label">Nom de la catégorie *</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($categorie['nom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="couleur" class="form-label">Couleur</label>
                            <div class="color-picker-container">
                                <input type="color" class="form-control color-picker" id="couleur" name="couleur" value="<?= htmlspecialchars($categorie['couleur']) ?>">
                                <span class="color-preview" id="color-preview" style="background-color: <?= htmlspecialchars($categorie['couleur']) ?>"></span>
                            </div>
                            <small>Cette couleur sera utilisée pour identifier visuellement la catégorie</small>
                        </div>
                        <select name="couleurTexte" id="couleurTexte">
                            <option value="#121212" <?= $categorie['couleurTexte'] == '#121212' ? 'selected' : '' ?>>Texte Noir</option>
                            <option value="#FFFFFF" <?= $categorie['couleurTexte'] == '#FFFFFF' ? 'selected' : '' ?>>Texte Blanc</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="descriptif" class="form-label">Description</label>
                            <textarea class="form-control" id="descriptif" name="descriptif" rows="3"><?= htmlspecialchars($categorie['descriptif']) ?></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="image_url" class="form-label">Image (optionnelle)</label>
                            <?php if ($categorie['image_url']): ?>
                                <div class="current-image">
                                    <img src="<?= RACINE_SITE . $categorie['image_url'] ?>" alt="<?= htmlspecialchars($categorie['nom']) ?>" style="max-width: 150px; max-height: 100px;">
                                    <input type="hidden" name="image_url_actuelle" value="<?= $categorie['image_url'] ?>">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image_url" name="image_url">
                            <small>Laissez vide pour conserver l'image actuelle. Formats acceptés : JPG, PNG, WEBP</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Mettre à jour la catégorie</button>
                    <a href="<?= RACINE_SITE ?>admin/categories" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminCategorie.js"></script>