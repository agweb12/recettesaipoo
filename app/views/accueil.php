<section class="hero">
    <div class="boxHero">
        <?php if(isset($isLoggedIn) && $isLoggedIn): ?>
        <p class="alert alert-warning">Bonjour <?= $user['prenom'] ?>, vous êtes connecté !</p>
        <form action="<?= RACINE_SITE ?>" method="post" id="formIngredients">
            <h4>Quels ingrédients avez-vous ?</h4>
            <label for="ingredients">Choisissez vos ingrédients :</label>
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
            <button type="submit" name="submit_ingredients">Créer ma liste et trouver mes recettes</button>
            <!-- Avertissement important pour l'utilisateur -->
            <div class="form-notice">
                <i class="fi fi-sr-info"></i>
                <p><strong>Important :</strong> En soumettant ce formulaire, vous créez une nouvelle liste personnelle d'ingrédients qui remplacera l'ancienne. Vos ingrédients précédents seront supprimés.</p>
            </div>
        </form>
        <?php else: ?>
            <h1>Recettes AI</h1>
            <p style="line-height:1.8">Avec <strong class="color-logo">Recettes Assistant Ingredient</strong> Trouvez votre recette de cuisine en fonction des ingrédients que vous avez chez vous.</p>
            <button type="button" id="btnModal">Commencez à trouver votre recette du jour</button>
        <?php endif; ?>
    </div>
</section>


<!-- Section Enjeu Sociétal -->
<section class="impact-section">
    <div class="impact-container">
        <h2>Un enjeu sociétal majeur</h2>
        <div class="impact-stats">
            <div class="stat-item highlight">
                <div class="stat-number">9,4M</div>
                <div class="stat-label">de tonnes de déchets alimentaires par an en France</div>
            </div>
            <div class="stat-connector">
                <i class="fi fi-sr-arrow-right"></i>
            </div>
            <div class="stat-item danger">
                <div class="stat-number">43%</div>
                <div class="stat-label">viennent des foyers français</div>
            </div>
        </div>
        <div class="impact-cause">
            <i class="fi fi-sr-exclamation"></i>
            <p><strong>Principal responsable :</strong> Le manque d'idées de cuisine avec les ingrédients disponibles</p>
        </div>
    </div>
</section>

<!-- Section Problème vs Solution -->
<section class="problem-solution">
    <div class="problem-solution-container">
        <div class="problem-box">
            <div class="problem-icon">
                <i class="fi fi-sr-confused"></i>
            </div>
            <h3>Le problème courant</h3>
            <blockquote>
                "J'ai des ingrédients dans mon frigo mais je ne sais pas quoi cuisiner avec..."
            </blockquote>
            <div class="problem-consequences">
                <span class="consequence">❌ Gaspillage alimentaire</span>
                <span class="consequence">❌ Perte de temps</span>
                <span class="consequence">❌ Courses supplémentaires</span>
            </div>
        </div>
        
        <div class="solution-arrow">
            <i class="fi fi-sr-arrow-right"></i>
        </div>
        
        <div class="solution-box">
            <div class="solution-icon">
                <i class="fi fi-sr-lightbulb-on"></i>
            </div>
            <h3>Notre solution</h3>
            <p>Recettes AI transforme vos ingrédients disponibles en délicieuses recettes personnalisées</p>
            <div class="solution-benefits">
                <span class="benefit">✅ Réduction du gaspillage</span>
                <span class="benefit">✅ Gain de temps</span>
                <span class="benefit">✅ Économies</span>
            </div>
        </div>
    </div>
</section>

<!-- Section Avantages -->
<section class="advantages-section">
    <div class="advantages-container">
        <h2>Pourquoi choisir Recettes AI ?</h2>
        <div class="advantages-grid">
            <div class="advantage-card">
                <div class="advantage-icon ecology">
                    <i class="fi fi-sr-leaf"></i>
                </div>
                <h4>Réduire le Gaspillage</h4>
                <p>Optimisez vos restes et réduisez votre impact environnemental grâce à nos recettes adaptées</p>
            </div>
            
            <div class="advantage-card">
                <div class="advantage-icon time">
                    <i class="fi fi-sr-clock"></i>
                </div>
                <h4>Gagner du Temps</h4>
                <p>Trouvez une recette parfaite en moins de <strong>2 minutes</strong> au lieu de chercher pendant des heures</p>
            </div>
            
            <div class="advantage-card">
                <div class="advantage-icon alternative">
                    <i class="fi fi-sr-star"></i>
                </div>
                <h4>Alternative Intelligente</h4>
                <p>Une expérience moderne et personnalisée, bien plus efficace que les moteurs de recherche classiques</p>
            </div>
            
            <div class="advantage-card">
                <div class="advantage-icon accessible">
                    <i class="fi fi-sr-users"></i>
                </div>
                <h4>Accessible à Tous</h4>
                <p>Conçu pour tous types d'utilisateurs : étudiants, familles, seniors, débutants ou experts</p>
            </div>
        </div>
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
                <a href="<?= RACINE_SITE ?>recettes/recette?id=<?= $recette['id'] ?>">Voir la recette</a>
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
                    <a href="<?= RACINE_SITE ?>recettes/recette?id=<?= $recette['id'] ?>">Voir la recette</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <a href="<?= RACINE_SITE ?>recettes" class="cta">Vous pouvez également rechercher des recettes</a>
</section>
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
                <strong>Liste personnelle d'ingrédient</strong>
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
            <button class="carousel-btn prev-btn" aria-label="précédent"><i class="fi fi-sr-angle-left"></i></button>
            <button class="carousel-btn next-btn" aria-label="suivant"><i class="fi fi-sr-angle-right"></i></button>
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