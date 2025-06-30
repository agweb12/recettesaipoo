<?php if(!empty($message)): ?>
<div class="alert alert-<?= $messageType ?>" style="margin:7rem auto auto auto"><?= $message ?></div>
<?php endif; ?>

<section class="toolbar">
    <div class="ctaButtons">
                <a href="#" class="tab-button active" data-tab="favoris-ingredients"><i class="fi fi-sr-heart"></i> Favoris & Ingrédients</a>
        <a href="#" class="tab-button" data-tab="parametres"><i class="fi fi-sr-settings"></i> Paramètres</a>
    </div>
</section>

<!-- Onglet Favoris et Ingrédients -->
<section id="favoris-ingredients" class="allListUsersFavorisAndIngredients tab-content active">
    <!-- Section des ingrédients personnels -->
    <section class="listUserIngredientsPersonals">
        <h2>Mes ingrédients</h2>
        <a href="<?= RACINE_SITE ?>recettes?formIngredients=1" class="cta"><i class="fi fi-sr-cursor"></i> Voir les recettes correspondantes</a>
        <p><?= $_SESSION['user']['prenom'] ?>, vous pouvez supprimer vos ingrédients un par un :</p>
        <?php if(!empty($ingredientsUtilisateur)): ?>
            <div class="ingredients-list">
                <div class="ingredient-tags">
                    <?php foreach($ingredientsUtilisateur as $ingredient): ?>
                        <div class="ingredient-tag">
                            <span><?= htmlspecialchars($ingredient['nom']) ?></span>
                            <!-- Formulaire pour supprimer un ingrédient -->
                            <form method="post" action="" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="supprimer_ingredient">
                                <input type="hidden" name="id_ingredient" value="<?= $ingredient['id'] ?>">
                                <button type="submit" class="remove-btn" title="Retirer cet ingrédient">
                                    <i class="fi fi-sr-cross-small"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Formulaire pour supprimer tous les ingrédients -->
            <form method="post" action="" class="danger-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="supprimer_tous_ingredients">
                <button type="submit" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer tous vos ingrédients ?');">
                    <i class="fi fi-sr-trash"></i> Supprimer tous mes ingrédients
                </button>
            </form>
        <?php else: ?>
            <p class="empty-state">Vous n'avez pas encore ajouté d'ingrédients à votre liste personnelle.</p>
            <a href="<?= RACINE_SITE ?>accueil" class="cta">Ajouter des ingrédients</a>
        <?php endif; ?>
    </section>
    <!-- Section des recettes favorites -->
    <section class="listUserRecipeFavoris">
        <h2>Mes recettes favorites</h2>
        <p><?= $_SESSION['user']['prenom'] ?>, vous pouvez supprimer vos recettes favorites une par une</p>
        <?php if(!empty($recettesFavorites)): ?>
            <div class="recipes">
                <?php foreach($recettesFavorites as $recette): ?>
                    <div class="recipeBox">
                        <?php if(!empty($recette['image_url'])): ?>
                            <img src="<?= RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] ?>" alt="<?= htmlspecialchars($recette['nom']) ?>">
                        <?php else: ?>
                            <img src="<?= RACINE_SITE ?>public/assets/img/femme-cagette-legumes.jpg" alt="Image par défaut">
                        <?php endif; ?>
                        <div class="recipe-meta">
                            <h6><i class="fi fi-sr-clock"></i> <?= $recette['temps_preparation'] + $recette['temps_cuisson'] ?> min</h6>
                            <!-- Formulaire pour supprimer une recette des favoris -->
                            <form method="post" action="" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="supprimer_favori">
                                <input type="hidden" name="id_recette" value="<?= $recette['id'] ?>">
                                <button type="submit" class="favorite-btn is-active profile-favorite-btn" title="Retirer des favoris">
                                    <i class="fi fi-sr-heart" id="heart"></i>
                                </button>
                            </form>
                        </div>
                        <a href="<?= RACINE_SITE ?>recettes/recette?id=<?= $recette['id'] ?>" target="_blank">Voir <?= htmlspecialchars($recette['nom']) ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Formulaire pour supprimer tous les favoris -->
            <form method="post" action="" class="danger-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="supprimer_tous_favoris">
                <button type="submit" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer toutes vos recettes favorites ?');">
                    <i class="fi fi-sr-trash"></i> Supprimer tous mes favoris
                </button>
            </form>
        <?php else: ?>
            <p class="empty-state">Vous n'avez pas encore de recettes favorites. Explorez notre catalogue de recettes pour en ajouter !</p>
            <a href="<?= RACINE_SITE ?>recettes" class="cta">Découvrir des recettes</a>
        <?php endif; ?>
    </section>
</section>

<!-- Onglet Paramètres du compte -->
<section id="parametres" class="settingsUser tab-content">
    <h2>Paramètres de mon compte</h2>
    
    <!-- Informations du compte -->
    <div class="account-info">
        <h3>Mes informations</h3>
        <p><strong>Nom:</strong> <?= htmlspecialchars($_SESSION['user']['nom']) ?></p>
        <p><strong>Prénom:</strong> <?= htmlspecialchars($_SESSION['user']['prenom']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
    </div>
    
    <!-- Formulaire de changement de mot de passe -->
    <div class="change-password">
        <h3>Changer mon mot de passe</h3>
        <form method="post" action="" class="form-settings">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="changer_mot_de_passe">
            
            <div class="form-group">
                <label for="ancien_mot_de_passe">Ancien mot de passe:</label>
                <input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe" required>
            </div>
            
            <div class="form-group">
                <label for="nouveau_mot_de_passe">Nouveau mot de passe:</label>
                <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required minlength="8">
            </div>
            
            <div class="form-group">
                <label for="confirmer_mot_de_passe">Confirmer le nouveau mot de passe:</label>
                <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required minlength="8">
            </div>
            
            <button type="submit" class="cta">Mettre à jour le mot de passe</button>
        </form>
    </div>
    
    <!-- Formulaire de suppression du compte -->
    <div class="delete-account">
        <h3>Supprimer mon compte</h3>
        <p class="warning-text">Attention : Cette action est irréversible et supprimera toutes vos données, y compris vos recettes favorites et votre liste d'ingrédients.</p>
        
        <form method="post" action="" class="form-settings">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="supprimer_compte">
            <input type="hidden" name="confirm_suppression" value="oui">
            
            <button type="submit" class="btn-danger" onclick="return confirm('ATTENTION : Êtes-vous vraiment sûr de vouloir supprimer définitivement votre compte ? Cette action est irréversible.');">
                <i class="fi fi-sr-trash"></i> Supprimer définitivement mon compte
            </button>
        </form>
    </div>
</section>