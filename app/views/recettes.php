<!-- Section des recettes -->
<?php if(isset($formIngredients) && $formIngredients == 1): ?>
    <?php if($isLoggedIn): ?>
    <section class="listeRecipesIngredients">
        <!-- Afficher les ingrédients de l'utilisateur -->
        <?php if(!empty($ingredientsUtilisateur)): ?>
        <section class="ingredients-list">
            <h4>Vos ingrédients</h4>
            <div class="ingredient-tags">
                <?php foreach($ingredientsUtilisateur as $ingredient): ?>
                    <span class="ingredient-tag"><?= htmlspecialchars($ingredient['nom']) ?></span>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        <!-- Afficher les recettes correspondantes -->
        <?php if(!empty($recettes)): ?>
        <section class="columnRecipe">
            <h4>Vos recettes correspondantes</h4>
            <div class="recipes">
            <?php foreach($recettes as $recette): ?>
                <div class="recipeBox">
                    <img src="<?= (!empty($recette['image_url'])) ? RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] : RACINE_SITE . 'public/assets/img/femme-cuisine.jpg' ?>" alt="<?= htmlspecialchars($recette['nom']) ?>">
                    <div class="recipe-meta">
                        <span><i class="fi fi-sr-clock"></i> Préparation: <?= $recette['temps_preparation'] ?> min</span>
                        <span><i class="fi fi-sr-flame"></i> Cuisson: <?= $recette['temps_cuisson'] ?> min</span>
                        <span style="background-color:<?= $recette['couleur_categorie'] ?>; color:<?= $recette['couleurTexte'] ?>;  border-radius:3rem; padding:.3rem;"><?= $recette['categorie'] ?></span>
                        <span><i class="fi fi-sr-stats"></i><?= ucfirst($recette['difficulte']) ?></span>
                        <?php if($isLoggedIn): ?>
                            <span>
                                <i class="fi <?= in_array($recette['id'], $recettesFavorisIds) ? 'fi-sr-heart' : 'fi-rr-heart' ?>"></i>
                                Favoris
                            </span>
                        <?php endif; ?>
                        <span><i class="fi fi-sr-list-check"></i>
                        <?= $recette['nombre_ingredients_correspondants'] ?> ingrédient<?= $recette['nombre_ingredients_correspondants'] > 1 ? 's' : '' ?> / <?= $recette['nombre_ingredients_total'] ?>
                        </span>
                    </div>
                    <p><?= htmlspecialchars($recette['nom']) ?></p>
                    <p><?= htmlspecialchars(substr($recette['descriptif'], 0, 100)) ?><?= strlen($recette['descriptif']) > 100 ? '...' : '' ?></p>
                    <a href="<?= RACINE_SITE ?>recettes/recette?id=<?= $recette['id'] ?>" target="_blank">Voir la recette</a>
                </div>
            <?php endforeach;?>
            </div>
        </section>
        <?php elseif(isset($formIngredients) && $formIngredients == 1): ?>
        <section class="no-recipes">
            <h4>Aucune recette trouvée</h4>
            <p>Nous n'avons pas trouvé de recettes correspondant à vos ingrédients. Essayez avec d'autres ingrédients ou consultez notre liste complète de recettes.</p>
        </section>
        <?php endif; ?>
    </section>
    <?php endif; ?>

<?php else: ?>
    <!-- Hero Section -->
    <section class="heroIngredients">
        <h1>Toutes les recettes</h1>
        <div class="boxHeroIngredients">
            <?php if(!$isLoggedIn): ?>
                <p>Inscrivez-vous ou connectez-vous pour trouver vos recettes en fonction de votre liste d'ingrédients</p>
                <button type="button" id="btnModal">Commencez à trouver votre recette du jour</button>
            <?php else: ?>
                <h5>Trouvez votre recette à l'aide du système de filtrage en fonction de vos envies</h5>
            <?php endif; ?>
        </div>
    </section>
    <!-- Hero Section End -->
    <section class="filterAllRecipes">
        <!-- Filtre des recettes -->
        <?php if(!isset($_GET['search'])) : ?>
        <?php else: ?>
        <aside class="filterRecipes">
            <div class="filterTitle">
                <h3>Filtrer les recettes</h3>
                <button id="reset-filters" class="btn-reset">Réinitialiser les filtres</button>
            </div>
            <details>
                <summary>Clique pour voir les filtres</summary>
                <div class="groupCol">
                    <div class="filter-groupRow">
                        <div class="filter-group">
                            <h4>Difficulté</h4>
                            <div class="filter-options">
                                <label><input type="checkbox" class="filter-checkbox" data-type="difficulte" value="facile"> Facile</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="difficulte" value="moyenne"> Moyenne</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="difficulte" value="difficile"> Difficile</label>
                            </div>
                        </div>
                        <div class="filter-group">
                            <h4>Temps de préparation</h4>
                            <div class="filter-options">
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="15"> Moins de 15 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="30"> Moins de 30 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="60"> Moins de 1h</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="120"> Plus de 1h</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-groupRow">
                        <div class="filter-group">
                            <h4>Temps de cuisson</h4>
                            <div class="filter-options">
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="15"> Moins de 15 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="30"> Moins de 30 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="60"> Moins de 1h</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="120"> Plus de 1h</label>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <h4>Catégorie</h4>
                            <details class="filter-options">
                                <summary>Choisir une catégorie</summary>
                                <!-- Afficher les catégories disponibles -->
                                <?php foreach($categories as $categorie): ?>
                                    <label>
                                        <i style="display:block;background-color:<?= $categorie['couleur'] ?>;color:<?= $categorie['couleurTexte'] ?>; border-radius:3rem; width:14px; height:14px;"></i>
                                        <input type="checkbox" class="filter-checkbox" data-type="categorie" value="<?= $categorie['id'] ?>"> 
                                        <?= html_entity_decode($categorie['nom'], ENT_QUOTES, "utf-8") ?>
                                    </label>
                                <?php endforeach; ?>
                            </details>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Étiquettes</h4>
                        <details class="filter-options">
                            <summary>Choisir une étiquette</summary>
                            <!-- Afficher les étiquettes disponibles -->
                            <?php foreach($etiquettes as $etiquette): ?>
                                <label>
                                    <input type="checkbox" class="filter-checkbox" data-type="etiquette" value="<?= $etiquette['id'] ?>"> 
                                    <?= htmlspecialchars($etiquette['nom']) ?>
                                </label>
                            <?php endforeach; ?>
                        </details>
                    </div>
                </div>
            </details>
        </aside>
        <?php endif; ?>
        <!-- Fin du filtre des recettes -->

        <!-- Affichage des recettes -->
        <section class="allRecipes">
            <!-- Barre de recherche -->
            <div class="search-container">
                <form id="search-form" method="get" action="">
                    <div class="inputBox">
                        <i class="fi fi-sr-search"></i>
                        <input type="text" name="search" id="search-input" placeholder="Rechercher une recette par nom ou descriptif" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                    <button type="submit" class="search-btn">Rechercher</button>
                </form>
            </div>
            <!-- Fin de la barre de recherche -->
            <!-- Affichage des recettes filtrées -->
            <?php if(isset($_GET['search']) || $hasFilters) : ?>
            <section class="recipes">
            <!-- Si des recettes sont trouvées -->
                <?php if(!empty($recettesRecherche)): ?>
                    <?php foreach($recettesRecherche as $recette): ?>
                        <div class="recipeBox"
                            data-difficulte="<?= $recette['difficulte'] ?>" 
                            data-temps_preparation="<?= $recette['temps_preparation'] ?>" 
                            data-temps_cuisson="<?= $recette['temps_cuisson'] ?>" 
                            data-categorie="<?= $recette['id_categorie'] ?>" 
                            data-etiquette="<?= $recette['etiquettes_ids'] ?>">
                            <img src="<?= (!empty($recette['image_url'])) ? RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] : RACINE_SITE . 'public/assets/img/femme-cuisine.jpg' ?>" alt="<?= htmlspecialchars($recette['nom']) ?>">
                            <div class="recipe-meta">
                                <span><i class="fi fi-sr-clock"></i> Préparation: <?= $recette['temps_preparation'] ?> min</span>
                                <span><i class="fi fi-sr-flame"></i> Cuisson: <?= $recette['temps_cuisson'] ?> min</span>
                                <span style="background-color:<?= $recette['couleur_categorie'] ?>; color:<?= $recette['couleurTexte'] ?>; border-radius:3rem; padding:.3rem;"><?= htmlspecialchars($recette['categorie']) ?></span>
                                <span><i class="fi fi-sr-stats"></i> <?= ucfirst($recette['difficulte']) ?></span>
                                <?php if($isLoggedIn): ?>
                                    <span>
                                        <i class="fi <?= in_array($recette['id'], $recettesFavorisIds) ? 'fi-sr-heart' : 'fi-rr-heart' ?>"></i> Favoris
                                    </span>
                                <?php endif; ?>
                                <span><i class="fi fi-sr-list-check"></i> <?= $recette['nb_etiquettes'] ?> étiquette(s)</span>
                            </div>
                            <h4><?= htmlspecialchars($recette['nom']) ?></h4>
                            <p><?= html_entity_decode(substr($recette['descriptif'], 0, 100)) ?><?= strlen($recette['descriptif']) > 100 ? '...' : '' ?></p>
                            <a href="<?= RACINE_SITE ?>recettes/recette?id=<?= $recette['id'] ?>" target="_blank">Voir la recette</a>
                        </div>
                    <?php endforeach; ?>
                <!-- Si aucune recette n'est trouvée -->
                <?php endif; ?>
            </section>
            <!-- Fin de l'affichage des recettes filtrées -->
            <?php else: ?>
            <!-- Si aucune recherche ou filtre n'est appliqué, afficher un message d'état vide -->
            <div class="empty-state">
                <i class="fi fi-sr-search"></i>
                <h3>Trouvez votre recette parfaite</h3>
                <p>Utilisez la barre de recherche et les filtres pour découvrir des recettes qui correspondent à vos envies !</p>
                <p>Vous pouvez rechercher par nom de recette ou par description.</p>
            </div>
            <?php endif; ?>
        </section>
        <!-- Fin de l'affichage des recettes -->
    </section>
<?php endif; ?>
<!-- Fin de la section des recettes -->
<!-- Carousel des fonctionnalités -->
<section class="features">
    <h2>comment ça marche ?</h2>
    <p>Nous proposons plusieurs solutions pour vous aider à trouver la recette parfaite :</p>
    <div class="features-carousel">
        <div class="features-carousel-container">
            <div class="feature-slide">
                <i class="fi fi-sr-search"></i>
                <strong>Recherche par ingrédients</strong>
                <p>Saisissez les ingrédients que vous avez et nous vous proposerons des recettes adaptées.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-shopping-cart"></i>
                <strong>Liste personnelle d'ingrédients</strong>
                <p>Créez votre liste d'ingrédients à partir des ingrédients existants, et retrouver les recettes correspondantes à votre liste.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-filter"></i>
                <strong>Filtres de recherche</strong>
                <p>Affinez votre recherche en fonction de vos préférences alimentaires (végétarien, sans gluten, etc.).</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-crown"></i>
                <strong>Recettes populaires</strong>
                <p>Découvrez les recettes les plus populaires de notre communauté.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-heart"></i>
                <strong>Favoris</strong>
                <p>Enregistrez vos recettes préférées pour y accéder facilement.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-share"></i>
                <strong>Partage</strong>
                <p>Partagez vos recettes préférées avec vos amis et votre famille.</p>
            </div>
        </div>

        <div class="carousel-nav">
            <button class="carousel-btn prev-btn"><i class="fi fi-sr-angle-left"></i></button>
            <button class="carousel-btn next-btn"><i class="fi fi-sr-angle-right"></i></button>
        </div>

        <div class="carousel-indicators">
            <!-- Créez un indicateur pour chaque slide -->
            <span class="indicator active"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
        </div>
    </div>
</section>