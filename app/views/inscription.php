<div id="register" class="register">
    <div class="register-content">
        <div class="register-header">
            <h2>Inscrivez-vous</h2>
            <p>Pour trouver des recettes adaptées à vos ingrédients</p>
        </div>
        <div class="register-body">
            <?php if(!empty($info)): ?>
                <div class="alert alert-success">
                    <p><i class="fi fi-sr-check"></i><?= $info ?></p>
                </div>
            <?php endif; ?>
            <?php if(isset($errors['general'])) { ?>
                <div class="alert alert-error">
                    <i class="fi fi-sr-exclamation-triangle"></i>
                    <p class="alert-error"><?= $errors['general'] ?></p>
                </div>
            <?php } ?>
            <form action="<?= RACINE_SITE ?>inscription" method="post" id="registerForm">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" >
                    <?php if(isset($errors['prenom'])) { ?>
                        <div class="alert alert-error">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-error"><?php echo $errors['prenom']; ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom" >
                    <?php if(isset($errors['nom'])) { ?>
                        <div class="alert alert-error">
                            <i class="fi fi-sr-exclamation-triangle"></i>
                            <p class="alert-error"><?php echo $errors['nom']; ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre adresse email" >
                </div>
                <?php if(isset($errors['email'])) { ?>
                    <div class="alert alert-error">
                        <i class="fi fi-sr-exclamation-triangle"></i>
                        <p class="alert-error"><?php echo $errors['email']; ?></p>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Votre mot de passe" >
                        <i class="fi fi-sr-eye password-toggle"></i>
                    </div>
                    <p>Le mot de passe doit contenir au moins 8 caractères, une minuscule, une majuscule, <br>un chiffre et un caractère spécial</p>
                </div>
                <?php if(isset($errors['password'])) { ?>
                    <div class="alert alert-error">
                        <i class="fi fi-sr-exclamation-triangle"></i>
                        <p class="alert-error"><?php echo $errors['password']; ?></p>
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