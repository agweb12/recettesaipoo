<section class="heroIngredients">
    <h1><?= $titlePage ?></h1>
    <div class="boxHeroIngredients">
        <?php if(!$this->isLoggedIn()): ?>
            <p>Inscrivez-vous ou connectez-vous pour trouver vos recettes en fonction de votre liste d'ingrédients</p>
            <button type="button" id="btnModal">Commencez à trouver votre recette du jour</button>
        <?php else: ?>
            <h5>Si vous souhaitez trouver votre recette du jour, clique juste en dessous</h5>
            <a href="<?= RACINE_SITE ?>" class="cta">Commencez à rentrer des ingrédients</a>
        <?php endif; ?>
    </div>
</section>
<section class="contact">
    <h2>Contactez-nous</h2>
    <h3>Nous sommes là pour vous aider</h3>
    <div class="contact-container">
        <div class="contact-box">
            <div class="contact-box-icon">
                <i class="fi fi-sr-headset"></i>
            </div>
            <div class="contact-box-text">
                <h4>Support Client</h4>
                <p>Notre équipe est disponible pour répondre à toutes vos questions.</p>
            </div>
        </div>
        <div class="contact-box">
            <div class="contact-box-icon">
                <i class="fi fi-sr-envelope"></i>
            </div>
            <div class="contact-box-text">
                <h4>Email</h4>
                <p>Vous pouvez nous contacter par email à l'adresse : <a href="mailto: contact@recettesai.fr">contact@recettesai.fr</a></p>
            </div>
        </div>
        <div class="contact-box">
            <div class="contact-box-icon">
                <i class="fi fi-sr-phone-flip"></i>
            </div>
            <div class="contact-box-text">
                <h4>Téléphone</h4>
                <p>Appelez-nous au : <br><span>+33 7 79 13 44 95</span></p>
            </div>
        </div>
        <div class="contact-box">
            <div class="contact-box-icon">
                <i class="fi fi-sr-map-marker"></i>
            </div>
            <div class="contact-box-text">
                <h4>Adresse</h4>
                <p>Nous sommes situés au : <br><span>123 Rue de la Cuisine, Paris, France</span></p>
            </div>
        </div>
    </div>
</section>