<div id="register" class="register">
    <div class="register-content">
        <div class="register-header">
            <h2>Inscrivez-vous</h2>
            <p>Pour trouver des recettes adaptées à vos ingrédients</p>
        </div>
        <div class="register-body">
            <?php if(!empty($info)): ?>
                <div class="alert alert-success">
                    <i class="fi fi-sr-check"></i>
                    <p><?= $info ?></p>
                </div>
            <?php endif; ?>
            <?php if(isset($errorGeneral)) { ?>
                <div class="alert alert-error">
                    <i class="fi fi-sr-exclamation-triangle"></i>
                    <p class="alert-error"><?= $errorGeneral ?></p>
                </div>
            <?php } ?>
            <form action="<?= RACINE_SITE ?>inscription" method="post" id="registerForm">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" >
                    <?php if(isset($errorPrenom)) { ?>
                        <div class="alert alert-error">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-error"><?php echo $errorPrenom; ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom" >
                    <?php if(isset($errorNom)) { ?>
                        <div class="alert alert-error">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-error"><?php echo $errorNom; ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre adresse email" >
                </div>
                <?php if(isset($errorEmail)) { ?>
                    <div class="alert alert-error">
                        <i class="fi fi-sr-exclamation-triangle"></i>
                        <p class="alert-error"><?php echo $errorEmail; ?></p>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Votre mot de passe" >
                        <i class="fi fi-sr-eye password-toggle"></i>
                    </div>
                </div>
                <?php if(isset($errorPassword)) { ?>
                    <div class="alert alert-error">
                        <i class="fi fi-sr-exclamation-triangle"></i>
                        <p class="alert-error"><?php echo $errorPassword; ?></p>
                    </div>
                <?php } ?>
                <button type="submit" class="btn-primary">S'inscrire</button>
            </form>

            <div class="separator">
                <span>ou</span>
            </div>
            <div class="signin-link">
                <p>Déjà inscrit ? <a href="<?= RACINE_SITE ?>connexion" id="signinLink">Connectez-vous</a></p>
            </div>
        </div>
    </div>
</div>

<?php
require_once('footer.php');
?>