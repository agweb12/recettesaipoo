<?php
// require_once('../inc/functions.php');
// $titlePage = "Se Connecter";
// $descriptionPage = "Se Connecter pour accéder à votre compte Recette AI.";
// $indexPage = "index";
// $followPage = "follow";

// Vérification si l'utilisateur est déjà connecté
if (isset($_SESSION['user'])) {
    header("Location: " . RACINE_SITE . "index.php");
    exit();
}

// Connexion de l'utilisateur
// if(!empty($_POST)){
    // $errorGeneral = "";
    // $verification = true;
    // $email = htmlspecialchars($_POST['email']);
    // $mdp = htmlspecialchars($_POST['password']);

    //verification des valeurs si elles sont vides

    // foreach ($_POST as $key => $value) {
    //     if (empty(trim($value))) {
    //         $verification = false;
    //     }
    // }

    // if($verification == false){
    //     $errorGeneral = alert("Veuillez remplir tous les champs", "danger");
    // } else {

        // Vérification de l'email
        if(!isset($email)){
            $verification = false;
            $errorEmail = "Le champs email est vide";
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $verification = false;
            $errorEmail = "L'email n'est pas valide";
        }

        // Vérification du mot de passe
        if(!isset($mdp)){
            $verification = false;
            $errorMdp = "Le champs mot de passe est vide";
        } elseif(strlen($mdp) < 8){
            $verification = false;
            $errorMdp = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre";
        }

        // Si toutes les vérifications sont passées
        $pdo = connexionBDD();
        $sql = "SELECT * FROM utilisateur WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $checkUserExist = $stmt->fetch();

        if($checkUserExist){
            // Vérification du mot de passe
            if(password_verify($mdp, $checkUserExist['mot_de_passe'])){
                // Si le mot de passe est correct, on démarre la session
                $pdo = connexionBDD();
                $sql = "SELECT id, id_admin, nom, prenom, email FROM utilisateur WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch();

                $_SESSION['user'] = $user;
                // debug($_SESSION['user']);
                
                header("Location: " . RACINE_SITE . "index.php");
                // exit();
            } else {
                $errorGeneral = alert("Veuillez vérifier votre mot de passe", "danger");
                $errorMdp = alert("Mot de passe incorrect", "warning");
            }
        } else {
            $errorGeneral = alert("Aucun compte trouvé avec cet email", "danger");
        }
    }
}

require_once('header.php');
?>
<div id="login" class="login">
    <div class="login-content">        
        <div class="login-header">
            <h2>Connectez-vous</h2>
            <p>Pour trouver des recettes adaptées à vos ingrédients</p>
        </div>
        <div class="login-body">
            <?php if(isset($errorGeneral)) { ?>
                <div class="alert alert-error">
                    <i class="fi fi-sr-exclamation-triangle"></i>
                    <p class="alert-error"><?php echo $errorGeneral; ?></p>
                </div>
            <?php } ?>
            <form action="" method="post" id="loginForm">
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

                <button type="submit" class="btn-primary">Connexion</button>
                <div class="form-footer">
                    <a href="reset_password.php" class="forgot-password">Mot de passe oublié ?</a>
                </div>
            </form>
            <div class="separator">
                <span>ou</span>
            </div>
            <div class="signup-link">
                <p>Pas encore de compte ? <a href="inscription.php" id="signupLink">Inscrivez-vous</a></p>
            </div>
        </div>
    </div>
</div>
<?php
require_once('footer.php');
?>