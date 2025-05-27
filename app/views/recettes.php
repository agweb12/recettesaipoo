<!-- Section des recettes -->
<?php if(isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1): ?>
    <?php if(isLoggedIn()): ?>
    <section class="listeRecipesIngredients">
        <!-- Afficher les ingrédients de l'utilisateur -->
        <?php if(!empty($ingredientsUtilisateur)): ?>
        <section class="ingredients-list">
            <h4>Vos ingrédients</h4>
            <div class="ingredient-tags">
                <?php foreach($ingredientsUtilisateur as $ingredient): ?>
                    <span class="ingredient-tag"><?= $ingredient['nom'] ?></span>
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
                    <img src="<?= (!empty($recette['image_url'])) ? RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] : RACINE_SITE . 'public/assets/img/femme-cuisine.jpg' ?>" alt="<?= $recette['nom'] ?>">
                    <div class="recipe-meta">
                        <span><i class="fi fi-sr-clock"></i> Préparation: <?= $recette['temps_preparation'] ?> min</span>
                        <span><i class="fi fi-sr-flame"></i> Cuisson: <?= $recette['temps_cuisson'] ?> min</span>
                        <span style="background-color:<?= $recette['couleur_categorie'] ?>; color:<?= $recette['couleurTexte'] ?>;  border-radius:3rem; padding:.3rem;"><?= $recette['categorie'] ?></span>
                        <span><i class="fi fi-sr-stats"></i><?= ucfirst($recette['difficulte']) ?></span>
                        <?php if(isLoggedIn()): ?>
                            <span>
                                <?php if(in_array($recette['id'], $recettesFavorisIds)): ?>
                                    <i class="fi fi-sr-heart"></i>
                                <?php else: ?>
                                    <i class="fi fi-rr-heart"></i>
                                <?php endif; ?>
                                Favoris
                            </span>
                        <?php endif; ?>
                        <span><i class="fi fi-sr-list-check"></i>
                        <?php 
                            // déterminer le nombre d'ingrédients total de chaque recette
                            $sql = "SELECT COUNT(*) AS nombre_ingredients_total 
                                    FROM liste_recette_ingredients 
                                    WHERE id_recette = :id_recette";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':id_recette', $recette['id']);
                            $stmt->execute();
                            $nombreIngredientsTotal = $stmt->fetchColumn();
                            $recette['nombre_ingredients_total'] = $nombreIngredientsTotal;
                        ?>
                        <?php if($recette['nombre_ingredients_correspondants'] == 1) : ?>
                            <?= $recette['nombre_ingredients_correspondants'] ?> ingrédient / <?= $nombreIngredientsTotal ?? '...' ?>
                        <?php elseif($recette['nombre_ingredients_correspondants'] > 1) : ?>
                            <?= $recette['nombre_ingredients_correspondants'] ?> ingrédients / <?= $nombreIngredientsTotal ?? '...' ?>
                        <?php else : ?>
                            <?= $recette['nombre_ingredients_correspondants'] ?> ingrédient / <?= $nombreIngredientsTotal ?? '...' ?> 
                        <?php endif; ?>
                        </span>
                    </div>
                    <p><?= $recette['nom'] ?></p>
                    <p><?= substr($recette['descriptif'], 0, 100) ?><?= strlen($recette['descriptif']) > 100 ? '...' : '' ?></p>
                    <a href="<?= RACINE_SITE ?>views/recettes/recette.php?id=<?= $recette['id'] ?>" target="_blank">Voir la recette</a>
                </div>
            <?php endforeach;?>
            </div>
        </section>
        <?php elseif(isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1): ?>
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
            <?php if(!isLoggedIn()): ?>
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
                                <?php
                                $pdo = connexionBDD();
                                // Requête pour récupérer toutes les catégories
                                $sql = "SELECT id, nom, couleur, couleurTexte FROM categorie ORDER BY nom";
                                $stmt = $pdo->query($sql);
                                while ($categorie = $stmt->fetch()) {
                                    echo '<label><i style="display:block;background-color:'.$categorie['couleur'].';color:'.$categorie['couleurTexte'].'; border-radius:3rem; width:14px; height:14px;"></i><input type="checkbox" class="filter-checkbox" data-type="categorie" value="' . $categorie['id'] . '"> ' . $categorie['nom'] . '</label>';
                                }
                                ?>
                            </details>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Étiquettes</h4>
                        <details class="filter-options">
                            <summary>Choisir une étiquette</summary>
                            <?php
                            $pdo = connexionBDD();
                            // Requête pour récupérer toutes les étiquettes
                            $sql = "SELECT id, nom FROM etiquette ORDER BY nom";
                            $stmt = $pdo->query($sql);
                            while ($etiquette = $stmt->fetch()) {
                                echo '<label><input type="checkbox" class="filter-checkbox" data-type="etiquette" value="' . $etiquette['id'] . '"> ' . $etiquette['nom'] . '</label>';
                            }
                            ?>
                        </details>
                    </div>
                </div>
            </details>
        </aside>
        <!-- Fin du filtre des recettes -->
        <!-- Affichage des recettes -->
        <section class="allRecipes">
            <!-- Barre de recherche -->
            <div class="search-container">
                <form id="search-form" method="get" action="">
                    <div class="inputBox">
                        <i class="fi fi-sr-search"></i>
                        <input type="text" name="search" id="search-input" placeholder="Rechercher une recette..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                    <button type="submit" class="search-btn">Rechercher</button>
                </form>
            </div>
            <!-- Fin de la barre de recherche -->
            <!-- Affichage des recettes filtrées -->
            <?php if(isset($_GET['search']) || isset($_GET['filtre_difficulte']) || isset($_GET['filtre_temps_preparation']) || isset($_GET['filtre_temps_cuisson']) || isset($_GET['filtre_categorie']) || isset($_GET['filtre_etiquette'])) : ?>
            <section class="recipes">
            <?php
            if(!empty($recettesRecherche)) {
                foreach($recettesRecherche as $recette) {
                    // Récupérer les étiquettes de cette recette
                    $sqlEtiquettes = "SELECT e.id, e.nom FROM etiquette e 
                                    JOIN recette_etiquette re ON e.id = re.id_etiquette 
                                    WHERE re.id_recette = :id_recette";
                    $stmtEtiquettes = $pdo->prepare($sqlEtiquettes);
                    $stmtEtiquettes->execute(['id_recette' => $recette['id']]);
                    $etiquettes = $stmtEtiquettes->fetchAll();
                    
                    $etiquettesIds = [];
                    foreach ($etiquettes as $etiquette) {
                        $etiquettesIds[] = $etiquette['id'];
                    }

                    // Créer un élément de recette avec des attributs data pour le filtrage
                    echo '<div class="recipeBox"
                            data-difficulte="' . $recette['difficulte'] . '" 
                            data-temps_preparation="' . $recette['temps_preparation'] . '" 
                            data-temps_cuisson="' . $recette['temps_cuisson'] . '" 
                            data-categorie="' . $recette['id_categorie'] . '" 
                            data-etiquette="' . implode(',', $etiquettesIds) . '">
                            <img src="' . (!empty($recette['image_url']) ? RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] : RACINE_SITE . 'public/assets/img/femme-cuisine.jpg') . '" alt="' . $recette['nom'] . '">
                            <div class="recipe-meta">
                                <span><i class="fi fi-sr-clock"></i> Préparation: ' . $recette['temps_preparation'] . ' min</span>
                                <span><i class="fi fi-sr-flame"></i> Cuisson: ' . $recette['temps_cuisson'] . ' min</span>
                                <span style="background-color:'.$recette['couleur_categorie'].'; color:'.$recette['couleurTexte'].'; border-radius:3rem; padding:.3rem;">' . $recette['categorie'] . '</span>
                                <span><i class="fi fi-sr-stats"></i> ' . ucfirst($recette['difficulte']) . '</span>';
                    if(isLoggedIn()){
                        echo '<span>';
                                if(in_array($recette['id'], $recettesFavorisIds)){
                                    echo '<i class="fi fi-sr-heart"></i>';
                                } else {
                                    echo '<i class="fi fi-rr-heart"></i>';
                                }
                        echo ' Favoris</span>';
                    }

                    echo '<span><i class="fi fi-sr-list-check"></i> ' . count($etiquettes) . ' étiquette(s)</span>
                            </div>
                            <h4>' . $recette['nom'] . '</h4>
                            <p>' . (strlen($recette['descriptif']) > 100 ? substr($recette['descriptif'], 0, 100) . '...' : $recette['descriptif']) . '</p>
                            <a href="'.RACINE_SITE.'views/recettes/recette.php?id=' . $recette['id'] . '">Voir la recette</a>
                        </div>';
                }
            }
            ?>
            <!-- Si aucune recette n'est trouvée -->
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
    <h2>nos fonctionnalités</h2>
    <p>Nous proposons plusieurs fonctionnalités pour vous aider à trouver la recette parfaite :</p>
    <div class="features-carousel">
        <div class="features-carousel-container">
            <div class="feature-slide">
                <i class="fi fi-sr-search"></i>
                <strong>Recherche par ingrédients</strong>
                <p>Saisissez les ingrédients que vous avez et nous vous proposerons des recettes adaptées.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-shopping-cart"></i>
                <strong>Liste d'ingrédients Personnelles</strong>
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
<!-- Fin du carousel des fonctionnalités -->
<?php
require_once('footer.php');
?>