<?php
// Connexion de l'utilisateur
if(!empty($_POST)){
    $errorGeneral = "";
    $verification = true;

    //verification des valeurs si elles sont vides
    if(empty(trim($_POST['email'])) || empty(trim($_POST['password']))){
        $verification = false;
        $errorGeneral = alert("Veuillez remplir tous les champs", "danger");
    } else {
        $email = htmlspecialchars($_POST['email']);
        $mdp = htmlspecialchars($_POST['password']);

        // Vérification de l'email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $verification = false;
            $errorEmail = "L'email n'est pas valide";
        }

        // Vérification du mot de passe
        if(strlen($mdp) < 8){
            $verification = false;
            $errorMdp = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre";
        }

        if($verification){
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
}
?>