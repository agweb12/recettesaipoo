<section class="hero">
    <div class="boxHero">
        <?php if(isset($isLoggedIn) && $isLoggedIn): ?>
        <p class="alert alert-warning">Bonjour <?= $user['prenom'] ?>, vous êtes connecté !</p>
        <form action="<?= RACINE_SITE ?>" method="post" id="formIngredients">
            <h4>Rentrez vos ingrédients pour trouver votre recette</h4>
            <label for="ingredients">Quels ingrédients avez-vous ?</label>
            <div id="ingredients-container">
                <div class="ingredient-input-group">
                    <div class="inputBox">
                        <i class="fi fi-sr-search-heart"></i>
                        <input type="text" class="ingredient-autocomplete" placeholder="Ex: Tomate" autocomplete="off">
                        <input type="hidden" name="ingredients[]" class="ingredient-id">
                    </div>
                </div>
            </div>
            <div class="selected-ingredients"></div>
            <button type="submit" name="submit_ingredients">Trouver une recette</button>
        </form>
        <?php else: ?>
            <h1>Bienvenue sur Recettes AI</h1>
            <p>Avec Recettes Assistant Ingrédient, trouvez votre recette de cuisine en fonction des ingrédients que vous avez chez vous.</p>
            <button type="button" id="btnModal">Commencez à trouver votre recette du jour</button>
        <?php endif; ?>
    </div>
</section>
<section class="typesRecipes">
    <h2>Nos recettes</h2>
    <h3>les plus populaires</h3>
    <section class="recipes">
        <?php if(!empty($popularRecipes)): ?>
            <?php foreach($popularRecipes as $recette): ?>
            <div class="recipeBox">
                <?php if(!empty($recette['image_url'])): ?>
                    <img src="<?= RACINE_SITE ?>public/assets/recettes/<?= $recette['image_url'] ?>" alt="<?= $recette['nom'] ?>">
                <?php else: ?>
                    <img src="<?= RACINE_SITE ?>public/assets/img/femme-avocat.jpg" alt="Recette par défaut">
                <?php endif; ?>
                <h4><?= $recette['nom'] ?></h4>
                <p><?= $recette['descriptif'] ?></p>
                <a href="<?= RACINE_SITE ?>recette?id=<?= $recette['id'] ?>">Voir la recette</a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <h3>les plus récentes</h3>
    <section class="recipes">
        <?php if(!empty($recentRecipes)): ?>
            <?php foreach($recentRecipes as $recette): ?>
                <div class="recipeBox">
                    <?php if(!empty($recette['image_url'])): ?>
                        <img src="<?= RACINE_SITE ?>public/assets/recettes/<?= $recette['image_url'] ?>" alt="<?= $recette['nom'] ?>">
                    <?php else: ?>
                        <img src="<?= RACINE_SITE ?>public/assets/img/femme-avocat.jpg" alt="Recette par défaut">
                    <?php endif; ?>
                    <h4><?= $recette['nom'] ?></h4>
                    <p><?= $recette['descriptif'] ?></p>
                    <a href="<?= RACINE_SITE ?>recette?id=<?= $recette['id'] ?>">Voir la recette</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <a href="<?= RACINE_SITE ?>recettes" class="cta">Vous pouvez également rechercher des recettes</a>
</section>
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
<section class="aboutUs">
    <h2>À propos de nous</h2>
    <p>Recettes AI est une équipe de passionnés de cuisine et de technologie. Nous avons créé ce site pour aider les gens à trouver des recettes facilement et rapidement.</p>
    <p>Nous croyons que la cuisine doit être accessible à tous, c'est pourquoi nous avons développé cet outil pour vous aider à cuisiner avec ce que vous avez chez vous.</p>
    <p>Nous espérons que vous apprécierez notre site et que vous trouverez des recettes délicieuses à essayer !</p>
    <p>Merci de votre visite et à bientôt sur Recettes AI !</p>
</section>