<?php
require_once('../inc/functions.php');
$titlePage = "Inscription";
$descriptionPage = "S'inscrire sur Recette AI, pour trouver des recettes de cuisine en fonction des ingrédients que vous avez chez vous.";
$indexPage = "index";
$followPage = "follow";
$info = "";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $verification = true;

    // Vérification du nom
    // regex pour vérifier que le nom ne contient que des lettres en Majuscule, sans chiffres et sans caractères spéciaux
    $regexNom = "/^\p{L}[\p{L}\s-]*$/u";
    if(empty($nom) || !isset($nom)){
        $verification = false;
        $errorNom = "Le champs nom est vide";
    } elseif(!preg_match($regexNom, $nom)){
        $verification = false;
        $errorNom = "Le nom ne doit contenir que des lettres, tirets ou espaces";
    } elseif(strlen($nom) > 50){
        $verification = false;
        $errorNom = "Le nom ne doit pas dépasser 20 caractères";
    } elseif(strlen($nom) < 2){
        $verification = false;
        $errorNom = "Le nom doit contenir au moins 2 caractères";
    }

    // Vérification du prénom
    // regex pour vérifier que le prénom ne contient que des lettres en Majuscule ou minuscule, sans chiffres et sans caractères spéciaux
    $regexPrenom = "/^\p{L}[\p{L}\s-]*$/u";
    if(empty($prenom) || !isset($prenom)){
        $verification = false;
        $errorPrenom= "Le champs prénom est vide";
    } elseif(!preg_match($regexPrenom, $prenom)){
        $verification = false;
        $errorPrenom = "Le prénom ne doit contenir que des lettres, tirets ou espaces";
    } elseif(strlen($prenom) > 50){
        $verification = false;
        $errorPrenom = "Le prénom ne doit pas dépasser 20 caractères";
    } elseif(strlen($prenom) < 2){
        $verification = false;
        $errorPrenom = "Le prénom doit contenir au moins 2 caractères";
    }

    // Vérification de l'email
    // regex pour vérifier que l'email est valide
    $regexEmail = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    if(empty($email) || !isset($email)){
        $verification = false;
        $errorEmail = "Le champs email est vide";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $verification = false;
        $errorEmail = "L'email n'est pas valide";
    } elseif(!preg_match($regexEmail, $email)){
        $verification = false;
        $errorEmail = "L'email doit contenir un '@'et un '.'";
    } elseif(strlen($email) > 100){
        $verification = false;
        $errorEmail = "L'email ne doit pas dépasser 100 caractères";
    } elseif(strlen($email) < 5){
        $verification = false;
        $errorEmail = "L'email doit contenir au moins 5 caractères";
    }

    // Vérification du mot de passe 
    // regex pour vérifier que le mot de passe contient au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
    $regexPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/";
    if(empty($password) || !isset($password)){
        $verification = false;
        $errorPassword = "Le mot de passe est vide";
    } elseif(!preg_match($regexPassword, $password)){
        $verification = false;
        $errorPassword = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
    } elseif(strlen($password) > 100){
        $verification = false;
        $errorPassword = "Le mot de passe ne doit pas dépasser 100 caractères";
    } elseif(strlen($password) < 8){
        $verification = false;
        $errorPassword = "Le mot de passe doit contenir au moins 8 caractères";
    }

    //Vérification de validation des champs
    if($verification){
        // Vérification si l'email existe déjà
        $pdo = connexionBDD();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if($user){
            $verification = false;
            // L'email existe déjà
            $errorEmail = "L'email existe déjà";
        } else {
            $nom = strtoupper($nom);
            $prenom = ucfirst(strtolower($prenom));
            // Hachage du mot de passe
            $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

            // Insertion de l'utilisateur dans la base de données
            $pdo = connexionBDD();
            $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe) VALUES (:nom, :prenom, :email, :mdp)");
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':mdp', $passwordHashed, PDO::PARAM_STR);
            $newUser = $stmt->execute();

            if($newUser){
                // Redirection vers la page de connexion
                $info = alert("Vous êtes bien inscrit, vous pouvez vous connectez <a href='connexion.php'>ici</a>", "success");
            } else {
                $errorGeneral = "Une erreur est survenue lors de l'inscription";
            }
        }
    }
}

require_once('header.php');
?>

<div id="register" class="register">
    <div class="register-content">
        <div class="register-header">
            <h2>Inscrivez-vous</h2>
            <p>Pour trouver des recettes adaptées à vos ingrédients</p>
        </div>
        <div class="register-body">
            <?php echo $info; ?>
            <?php if(isset($errorGeneral)) { ?>
                <div class="alert alert-error">
                    <i class="fi fi-sr-exclamation-triangle"></i>
                    <p class="alert-error"><?php echo $errorGeneral; ?></p>
                </div>
            <?php } ?>
            <form action="" method="post" id="registerForm">
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
                <p>Déjà inscrit ? <a href="connexion.php" id="signinLink">Connectez-vous</a></p>
            </div>
        </div>
    </div>
</div>

<?php
require_once('footer.php');
?>