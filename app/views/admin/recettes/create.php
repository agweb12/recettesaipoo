<div class="add-form-container">
    <h3>Ajouter une nouvelle recette</h3>
    <form method="POST" action="<?= RACINE_SITE ?>admin/recettes/store" class="add-form" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <!-- Informations générales -->
        <div class="form-section">
            <h4>Informations générales</h4>
            <div class="form-row">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom de la recette *</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="id_categorie" class="form-label">Catégorie *</label>
                    <select class="form-control" id="id_categorie" name="id_categorie" required>
                        <option value="">Sélectionner une catégorie</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id'] ?>"><?= htmlspecialchars($categorie['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="temps_preparation" class="form-label">Temps de préparation (minutes) *</label>
                    <input type="number" class="form-control" id="temps_preparation" name="temps_preparation" min="1" required>
                </div>
                <div class="form-group">
                    <label for="temps_cuisson" class="form-label">Temps de cuisson (minutes) *</label>
                    <input type="number" class="form-control" id="temps_cuisson" name="temps_cuisson" min="0" required>
                </div>
                <div class="form-group">
                    <label for="difficulte" class="form-label">Difficulté *</label>
                    <select class="form-control" id="difficulte" name="difficulte" required>
                        <option value="facile">Facile</option>
                        <option value="moyenne">Moyenne</option>
                        <option value="difficile">Difficile</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="image_url" class="form-label">Image</label>
                    <input type="file" class="form-control" id="image_url" name="image_url">
                    <small>Formats acceptés : JPG, PNG, WEBP</small>
                </div>
            </div>
        </div>
        
        <!-- Description et instructions -->
        <div class="form-section">
            <h4>Description et instructions</h4>
            <div class="form-row">
                <div class="form-group">
                    <label for="descriptif" class="form-label">Description *</label>
                    <textarea class="form-control" id="descriptif" name="descriptif" rows="3" required></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="instructions" class="form-label">Instructions *</label>
                    <textarea class="form-control" id="instructions" name="instructions" rows="6" required></textarea>
                    <small>Utilisez ## pour séparer les étapes (ex: ##Étape 1. ##Étape 2.)</small>
                </div>
            </div>
        </div>
        
        <!-- Étiquettes -->
        <div class="form-section">
            <h4>Étiquettes</h4>
            <div class="form-row tags-container">
                <?php foreach ($etiquettes as $etiquette): ?>
                    <div class="tag-item">
                        <input type="checkbox" id="etiquette_<?= $etiquette['id'] ?>" name="etiquettes[]" value="<?= $etiquette['id'] ?>">
                        <label for="etiquette_<?= $etiquette['id'] ?>"><?= htmlspecialchars($etiquette['nom']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Ingrédients -->
        <div class="form-section">
            <h4>Ingrédients</h4>
            <div id="ingredients-container">
                <div class="ingredient-row">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ingrédient</label>
                            <select class="form-control" name="ingredient_id[]">
                                <option value="">Sélectionner un ingrédient</option>
                                <?php foreach ($ingredients as $ingredient): ?>
                                    <option value="<?= $ingredient['id'] ?>"><?= htmlspecialchars($ingredient['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantité</label>
                            <input type="number" class="form-control" name="quantite[]" step="0.01" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unité</label>
                            <select class="form-control" name="unite_id[]">
                                <option value="">Sélectionner une unité</option>
                                <?php foreach ($unites as $unite): ?>
                                    <option value="<?= $unite['id'] ?>"><?= htmlspecialchars($unite['nom']) ?> (<?= htmlspecialchars($unite['abreviation']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group ingredient-actions">
                            <button type="button" class="btn-remove-ingredient">Supprimer</button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-ingredient" class="btn-secondary">Ajouter un ingrédient</button>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Ajouter la recette</button>
            <button type="reset" class="btn-secondary">Réinitialiser</button>
        </div>
    </form>
</div>