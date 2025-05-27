<div id="connexionModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div class="modal-header">
            <h2>Connectez-vous</h2>
            <p>Pour trouver des recettes adaptées à vos ingrédients</p>
        </div>
        
        <div class="modal-body">
            <?php if(isset($errorGeneral)) { ?>
                <div class="alert alert-error">
                    <i class="fi fi-sr-exclamation-triangle"></i>
                    <p class="alert-error"><?php echo $errorGeneral; ?></p>
                </div>
            <?php } ?>
            <form method="post" id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre adresse email">
                    <?php if(isset($errorEmail)) { ?>
                        <div class="alert alert-warning">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-warning"><?php echo $errorEmail; ?></p>
                        </div>
                    <?php } ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Votre mot de passe">
                        <i class="fi fi-sr-eye password-toggle"></i>
                    </div>
                    <?php if(isset($errorMdp)) { ?>
                        <div class="alert alert-warning">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-warning"><?php echo $errorMdp; ?></p>
                        </div>
                    <?php } ?>
                </div>

                <button type="submit" class="cta">Connexion</button>
                
                <div class="form-footer">
                    <a href="views/reset_password.php" class="forgot-password">Mot de passe oublié ?</a>
                </div>
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