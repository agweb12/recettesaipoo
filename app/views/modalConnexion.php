<div id="connexionModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div class="modal-header">
            <h2>Connectez-vous</h2>
            <p>Pour trouver des recettes adaptées à vos ingrédients</p>
        </div>

        <div class="modal-body">
            <div id="modal-errors"></div>

            <form action="<?= RACINE_SITE ?>connexion" method="POST" id="modalLoginForm">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre adresse email">
                    <?php if(isset($errors['email'])) { ?>
                        <div class="alert alert-warning">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-warning"><?php echo $errors['email']; ?></p>
                        </div>
                    <?php } ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Votre mot de passe">
                        <i class="fi fi-sr-eye password-toggle"></i>
                    </div>
                </div>

                <button type="submit" class="cta">Connexion</button>
            </form>

            <div class="separator">
                <span>ou</span>
            </div>
            <div class="signup-link">
                <p>Pas encore de compte ? <a href="<?= RACINE_SITE ?>inscription" id="signupLink">Inscrivez-vous</a></p>
            </div>
        </div>
    </div>
</div>