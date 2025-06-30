<div id="login" class="login">
    <div class="login-content">
        <div class="login-header">
            <h2>Connectez-vous</h2>
            <p>Pour trouver des recettes adaptées à vos ingrédients</p>
        </div>
        <div class="login-body">
            <?php if(isset($errors['general'])) : ?>
                <div class="alert alert-danger">
                    <i class="fi fi-sr-exclamation-triangle"></i>
                    <p><?php echo $errors['general']; ?></p>
                    
                    <?php if(strpos($errors['general'], 'Trop de tentatives') !== false): ?>
                        <small style="display: block; margin-top: 10px; opacity: 0.8;">
                            Pour votre sécurité, nous limitons le nombre de tentatives de connexion.
                        </small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <form action="<?= RACINE_SITE ?>connexion" method="post" id="loginForm">
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
                    <?php if(isset($errors['password'])) { ?>
                        <div class="alert alert-warning">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-warning"><?php echo $errors['password']; ?></p>
                        </div>
                    <?php } ?>
                </div>

                <button type="submit" class="btn-primary">Connexion</button>
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