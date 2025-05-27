</main>
<footer>
    <div class="footer-content">
        <div class="footer-links">
            <a href="<?= RACINE_SITE ?>">Accueil</a>
            <a href="<?= RACINE_SITE ?>recettes">Recettes</a>
            <a href="<?= RACINE_SITE ?>contact">Contact</a>
        </div>
        <div class="footer-links">
            <a href="<?= RACINE_SITE ?>politique-confidentialite">Politique de Confidentialité</a>
            <a href="<?= RACINE_SITE ?>cgu">CGU</a>
            <a href="<?= RACINE_SITE ?>mentions-legales">Mentions Légales</a>
        </div>
        <div class="footer-social">
            <a href="#"><i class="fi fi-brands-facebook"></i></a>
            <a href="#"><i class="fi fi-brands-twitter"></i></a>
            <a href="#"><i class="fi fi-brands-instagram"></i></a>
        </div>
    </div>
    <p>&copy; <?= date('Y') ?> Recette AI. Tous droits réservés.</p>
    <p>Développé par <a href="https://agwebcreation.fr" target="_blank">Alexandre Graziani</a></p>
</footer>
<?php if($this->isLoggedIn()):?>
<script src="<?= RACINE_SITE ?>public/assets/javascript/favoris-loader.php"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/autocomplete.js"></script>
<?php endif; ?>
<script src="<?= RACINE_SITE ?>public/assets/javascript/mediaQueries.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/filter-recipes.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/carousel.js"></script>
<?php if(!$this->isLoggedIn()): ?>
<script src="<?= RACINE_SITE ?>public/assets/javascript/password.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/modal.js"></script>
<?php endif; ?>
</body>
</html>