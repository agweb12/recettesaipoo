<div class="edit-form-container">
    <h3>Modifier la recette</h3>
    <form method="POST" action="<?= RACINE_SITE ?>admin/recettes/update/<?= $recetteEdit['id'] ?>" class="edit-form" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <!-- Informations générales -->
        <div class="form-section">
            <h4>Informations générales</h4>
            <div class="form-row">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom de la recette *</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($recetteEdit['nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="id_categorie" class="form-label">Catégorie *</label>
                    <select class="form-control" id="id_categorie" name="id_categorie" required>
                        <option value="">Sélectionner une catégorie</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id'] ?>" <?= ($categorie['id'] == $recetteEdit['id_categorie']) ? 'selected' : '' ?>><?= htmlspecialchars($categorie['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="temps_preparation" class="form-label">Temps de préparation (minutes) *</label>
                    <input type="number" class="form-control" id="temps_preparation" name="temps_preparation" min="1" value="<?= $recetteEdit['temps_preparation'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="temps_cuisson" class="form-label">Temps de cuisson (minutes) *</label>
                    <input type="number" class="form-control" id="temps_cuisson" name="temps_cuisson" min="0" value="<?= $recetteEdit['temps_cuisson'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="difficulte" class="form-label">Difficulté *</label>
                    <select class="form-control" id="difficulte" name="difficulte" required>
                        <option value="facile" <?= ($recetteEdit['difficulte'] == 'facile') ? 'selected' : '' ?>>Facile</option>
                        <option value="moyenne" <?= ($recetteEdit['difficulte'] == 'moyenne') ? 'selected' : '' ?>>Moyenne</option>
                        <option value="difficile" <?= ($recetteEdit['difficulte'] == 'difficile') ? 'selected' : '' ?>>Difficile</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="image_url" class="form-label">Image</label>
                    <?php if ($recetteEdit['image_url']): ?>
                        <div class="current-image">
                            <img src="<?= RACINE_SITE . 'public/assets/recettes/' . $recetteEdit['image_url'] ?>" alt="<?= htmlspecialchars($recetteEdit['nom']) ?>" style="max-width: 150px; max-height: 100px;">
                            <input type="hidden" name="image_url_actuelle" value="<?= $recetteEdit['image_url'] ?>">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image_url" name="image_url">
                    <small>Laissez vide pour conserver l'image actuelle. Formats acceptés : JPG, PNG, WEBP</small>
                </div>
            </div>
        </div>
        
        <!-- Description et instructions -->
        <div class="form-section">
            <h4>Description et instructions</h4>
            <div class="form-row">
                <div class="form-group">
                    <label for="descriptif" class="form-label">Description *</label>
                    <textarea class="form-control" id="descriptif" name="descriptif" rows="3" required><?= htmlspecialchars($recetteEdit['descriptif']) ?></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="instructions" class="form-label">Instructions *</label>
                    <textarea class="form-control" id="instructions" name="instructions" rows="6" required><?= htmlspecialchars($recetteEdit['instructions']) ?></textarea>
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
                        <input type="checkbox" id="etiquette_<?= $etiquette['id'] ?>" name="etiquettes[]" value="<?= $etiquette['id'] ?>" 
                            <?= in_array($etiquette['id'], $recetteEtiquettesData ?? []) ? 'checked' : '' ?>>
                        <label for="etiquette_<?= $etiquette['id'] ?>"><?= htmlspecialchars($etiquette['nom']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Ingrédients -->
        <div class="form-section">
            <h4>Ingrédients</h4>
            <div id="ingredients-container">
                <?php if (isset($recetteIngredientsData) && !empty($recetteIngredientsData)): ?>
                    <?php foreach ($recetteIngredientsData as $ingredient): ?>
                        <div class="ingredient-row">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Ingrédient</label>
                                    <select class="form-control" name="ingredient_id[]">
                                        <option value="">Sélectionner un ingrédient</option>
                                        <?php foreach ($ingredients as $ing): ?>
                                            <option value="<?= $ing['id'] ?>" <?= ($ing['id'] == $ingredient['id_ingredient']) ? 'selected' : '' ?>><?= htmlspecialchars($ing['nom']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" class="form-control" name="quantite[]" step="0.01" min="0" value="<?= $ingredient['quantite'] ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Unité</label>
                                    <select class="form-control" name="unite_id[]">
                                        <option value="">Sélectionner une unité</option>
                                        <?php foreach ($unites as $unite): ?>
                                            <option value="<?= $unite['id'] ?>" <?= ($unite['id'] == $ingredient['id_unite']) ? 'selected' : '' ?>><?= htmlspecialchars($unite['nom']) ?> (<?= htmlspecialchars($unite['abreviation']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group ingredient-actions">
                                    <button type="button" class="btn-remove-ingredient">Supprimer</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
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
                <?php endif; ?>
            </div>
            <button type="button" id="add-ingredient" class="btn-secondary">Ajouter un ingrédient</button>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Mettre à jour la recette</button>
            <a href="<?= RACINE_SITE ?>admin/recettes" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>