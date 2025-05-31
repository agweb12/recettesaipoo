<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>admin/dashboard">Dashboard</a>
            <p>/</p>
            <a class="crumb" href="<?= RACINE_SITE ?>admin/categories">Catégories</a>
            <p>/</p>
            <p>Ajouter</p>
        </div>
    </section>
</section>

<section class="admin-section">
    <div class="admin-container">
        <?php if (isset($_GET['info'])): ?>
            <?= alert($_GET['info'], $_GET['type'] ?? 'info') ?>
        <?php endif; ?>

        <!-- Formulaire d'ajout d'une catégorie -->
        <div class="add-form-container">
            <h3>Ajouter une nouvelle catégorie</h3>
            <form method="POST" action="<?= RACINE_SITE ?>admin/categories/store" class="add-form" enctype="multipart/form-data">
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom" class="form-label">Nom de la catégorie *</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="couleur" class="form-label">Couleur</label>
                            <div class="color-picker-container">
                                <input type="color" class="form-control color-picker" id="couleur" name="couleur" value="#FFFFFF">
                                <span class="color-preview" id="color-preview"></span>
                            </div>
                            <small>Cette couleur sera utilisée pour identifier visuellement la catégorie</small>
                        </div>
                        <select name="couleurTexte" id="couleurTexte">
                            <option value="#121212">Texte Noir</option>
                            <option value="#FFFFFF">Texte Blanc</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="descriptif" class="form-label">Description</label>
                            <textarea class="form-control" id="descriptif" name="descriptif" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="image_url" class="form-label">Image (optionnelle)</label>
                            <input type="file" class="form-control" id="image_url" name="image_url">
                            <small>Formats acceptés : JPG, PNG, WEBP. Taille recommandée : 400x300 pixels</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Ajouter la catégorie</button>
                    <a href="<?= RACINE_SITE ?>admin/categories" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminCategorie.js"></script>