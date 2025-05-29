<section class="heroIngredients">
    <h1><?= htmlspecialchars($recette['nom']) ?></h1>
    <div class="boxHeroIngredients">
        <?php if(!$this->isLoggedIn()): ?>
            <p>Inscrivez-vous ou connectez-vous pour trouver vos recettes en fonction de votre liste d'ingrédients</p>
            <button type="button" id="btnModal">Commencez à trouver votre recette du jour</button>
        <?php else: ?>
                <h5>Ajoutez cette recette dans vos favoris si elle vous plaît</h5>
        <?php endif; ?>
    </div>
</section>
<!-- Récupération des données de la recette -->
<section class="oneRecipe">
    <div class="recipeBox <?= $isFavorite ? 'is-active' : '' ?>"
        data-id="<?= $recette['id'] ?>"
        data-categorie="<?= $recette['id_categorie'] ?>"
        data-etiquette="<?=implode(',', $etiquettesIds) ?>">
        <img src="<?= !empty($recette['image_url']) ? RACINE_SITE . 'public/assets/recettes/' . html_entity_decode($recette['image_url']) : RACINE_SITE . 'public/assets/img/femme-cuisine.jpg' ?>" 
            alt="<?= html_entity_decode($recette['nom']) ?>">
        <div class="recipe-meta">
            <span><i class="fi fi-sr-clock"></i> Préparation: <?= $recette['temps_preparation'] ?> min</span>
            <span><i class="fi fi-sr-flame"></i> Cuisson: <?= $recette['temps_cuisson'] ?> min</span>
            <span style="background-color:<?= html_entity_decode($recette['couleur_categorie']) ?>; color: <?= html_entity_decode($recette['couleurTexte']) ?>; border-radius:3rem; padding:.3rem;"><?= $recette['categorie'] ?></span>
            <span><i class="fi fi-sr-stats"></i> <?= ucfirst($recette['difficulte']) ?></span>
            <?php if($this->isLoggedIn()):?>
                <button class="favorite-btn <?= $isFavorite ? 'is-active' : '' ?>" 
                data-id="<?= $recette['id'] ?>" 
                data-favorite="<?= $isFavorite ? '1' : '0' ?>">
                    <i class="fi <?= $isFavorite ? 'fi-sr-heart' : 'fi-rr-heart' ?>" style="font-size:2rem"></i>
                </button>
            <?php endif; ?>
        </div>
        <div class="detailsBox">
            <h4><?= html_entity_decode($recette['nom']) ?></h4>
            <p><?= nl2br(html_entity_decode($recette['descriptif'])) ?></p>
            <div class="etiquettes">
                <?php foreach ($etiquettes as $etiquette): ?>
                    <span class="badge" title="<?= html_entity_decode($etiquette['descriptif']) ?>" style="background-color:<?= html_entity_decode($recette['couleur_categorie']) ?>; color:<?= html_entity_decode($recette['couleurTexte'])?>"><?= $etiquette['nom'] ?></span>
                <?php endforeach; ?>
            </div>
            <div class="instructions">
                <?php
                $instructions = explode("##", $recette['instructions']);
                foreach ($instructions as $instruction): ?>
                    <p style="text-align:justify"><?= html_entity_decode($instruction) ?></p>
                <?php endforeach; ?>
            </div>
            <a href="<?= RACINE_SITE?>recettes">Rechercher des recettes</a>
        </div>
    </div>
    <div class="ingredientsBox">
        <h4>Ingrédients</h4>
        <?php if(!empty($ingredientsList)): ?>
            <ul>
            <?php foreach ($ingredientsList as $ingredient): ?>
                <li class="<?php 
                    if($ingredient['possedeListIngredients']) {
                        echo $ingredient['disponible'] ? 'ingredient-disponible' : 'ingredient-manquant';
                    } else {
                        echo 'ingredient-non-disponible';
                    }
                ?>">
                    <div class="ingredient-info">
                        <?php if($ingredient['possedeListIngredients']): ?>
                            <i class="fi <?= $ingredient['disponible'] ? 'fi-sr-check' : 'fi-sr-cross' ?>"></i>
                        <?php endif; ?>
                        <span class="ingredientNom"><?= $ingredient['nom'] ?></span>
                    </div>
                    <span class="ingredientQuantite">
                        <?php if(!empty($ingredient['quantite'])): ?>
                            <?= $ingredient['quantite'] ?> <?= $ingredient['unite'] ?>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php if($this->isLoggedIn()): ?>
            <div class="legend">
                <p><i class="fi fi-sr-check"></i> Ingrédient disponible dans votre liste</p>
                <p><i class="fi fi-sr-cross"></i> Ingrédient manquant dans votre liste</p>
                <details>
                    <summary>Unités de Mesure</summary>
                    <div class="unite">
                    <?php if(!empty($ingredientsList)): ?>
                        <?php foreach ($uniteMesureList as $uniteMesure): ?>
                            <p><span><?= $uniteMesure['abreviation'] ?></span> : <?= $uniteMesure['nom'] ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </details>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <p>Aucun ingrédient trouvé pour cette recette.</p>
        <?php endif; ?>
        <?php if(!$this->isLoggedIn()): ?>
            <p class="info-ingredients">
                <a href="<?= RACINE_SITE ?>connexion">Connectez-vous</a> pour voir quels ingrédients vous manquent pour cette recette.
            </p>
        <?php endif; ?>
    </div>
</section>