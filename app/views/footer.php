</main>
<footer>
    <div class="footer-content">
        <div class="footer-links">
            <a href="<?= RACINE_SITE ?>" title="accueil">Accueil</a>
            <a href="<?= RACINE_SITE ?>recettes" title="recettes">Recettes</a>
            <a href="<?= RACINE_SITE ?>contact" title="contact">Contact</a>
        </div>
        <div class="footer-links">
            <a href="<?= RACINE_SITE ?>politique-confidentialite" title="">Politique de Confidentialité</a>
            <a href="<?= RACINE_SITE ?>cgu" title="cgu">CGU</a>
            <a href="<?= RACINE_SITE ?>mentions-legales" title="mentions légales">Mentions Légales</a>
        </div>
        <div class="footer-social">
            <a href="#" title="facebook"><i class="fi fi-brands-facebook"></i></a>
            <a href="#" title="twitter"><i class="fi fi-brands-twitter"></i></a>
            <a href="#" title="instagram"><i class="fi fi-brands-instagram"></i></a>
        </div>
    </div>
    <p>&copy; <?= date('Y') ?> Recettes AI. Tous droits réservés.</p>
    <p>Développé par <a href="https://agwebcreation.fr" target="_blank" title="lien site web">Alexandre Graziani</a></p>
</footer>
<?php if($this->isLoggedIn()):?>
<script src="<?= RACINE_SITE ?>public/assets/javascript/favoris.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/autocomplete.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/tabOnglet.js"></script>
<?php endif; ?>
<script src="<?= RACINE_SITE ?>public/assets/javascript/mediaQueries.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/filter-recipes.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/carousel.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/themeMode.js"></script>
<?php if(!$this->isLoggedIn()): ?>
<script src="<?= RACINE_SITE ?>public/assets/javascript/password.js"></script>
<script src="<?= RACINE_SITE ?>public/assets/javascript/modal.js"></script>
<?php endif; ?>
</body>
</html>