<?php
require_once('../../inc/functions.php');

$titlePage = "Compte - Recettes AI";
$descriptionPage = "Recettes AI est un site qui vous permet de trouver des recettes de cuisine en fonction des ingrédients que vous avez chez vous.";
$indexPage = "noindex";
$followPage = "nofollow";
$keywordsPage = "Recettes AI, recette, ai, intelligence artificielle, cuisine, ingrédients, recettes, trouver une recette";

$recettes = []; // Tableau pour stocker les recettes
$recettesFavorites = []; // Tableau pour stocker les recettes favorites
$ingredientsUtilisateur = []; // Tableau pour stocker les ingrédients de l'utilisateur
$message = ''; // Message pour l'utilisateur
$messageType = ''; // Type de message (success, error, etc.)

// Vérifie si l'utilisateur est connecté et si l'ID dans l'URL correspond
if(!isLoggedIn()){
    header('Location: '.RACINE_SITE.'views/connexion.php');
    exit();
}

if(!isset($_GET['id']) || $_GET['id'] != $_SESSION['user']['id']){
    header('Location: '.RACINE_SITE.'views/connexion.php');
    exit();
}

// Si l'utilisateur est connecté et que l'ID correspond
if(isLoggedIn() && isset($_GET['id']) && $_GET['id'] == $_SESSION['user']['id']){
    $pdo = connexionBDD();
    $userId = $_SESSION['user']['id'];

    // Traitement des actions
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            // 3. Suppression d'un ingrédient
            case 'supprimer_ingredient':
                if(isset($_POST['id_ingredient'])) {
                    $sql = "DELETE FROM liste_personnelle_ingredients WHERE id_utilisateur = :id_utilisateur AND id_ingredient = :id_ingredient";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':id_utilisateur', $userId);
                    $stmt->bindValue(':id_ingredient', $_POST['id_ingredient'], PDO::PARAM_INT);
                    if($stmt->execute()) {
                        $message = "L'ingrédient a été supprimé de votre liste.";
                        $messageType = "success";
                    } else {
                        $message = "Une erreur est survenue lors de la suppression de l'ingrédient.";
                        $messageType = "error";
                    }
                }
                break;

            // 4. Suppression d'une recette favorite
            case 'supprimer_favori':
                if(isset($_POST['id_recette'])) {
                    $sql = "DELETE FROM recette_favorite WHERE id_utilisateur = :id_utilisateur AND id_recette = :id_recette";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':id_utilisateur', $userId);
                    $stmt->bindValue(':id_recette', $_POST['id_recette'], PDO::PARAM_INT);
                    if($stmt->execute()) {
                        $message = "La recette a été retirée de vos favoris.";
                        $messageType = "success";
                    } else {
                        $message = "Une erreur est survenue lors de la suppression du favori.";
                        $messageType = "error";
                    }
                }
                break;

            // 5. Suppression de tous les ingrédients
            case 'supprimer_tous_ingredients':
                $sql = "DELETE FROM liste_personnelle_ingredients WHERE id_utilisateur = :id_utilisateur";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id_utilisateur', $userId);
                if($stmt->execute()) {
                    $message = "Tous vos ingrédients ont été supprimés.";
                    $messageType = "success";
                } else {
                    $message = "Une erreur est survenue lors de la suppression de vos ingrédients.";
                    $messageType = "error";
                }
                break;

            // 6. Suppression de tous les favoris
            case 'supprimer_tous_favoris':
                $sql = "DELETE FROM recette_favorite WHERE id_utilisateur = :id_utilisateur";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id_utilisateur', $userId);
                if($stmt->execute()) {
                    $message = "Toutes vos recettes favorites ont été supprimées.";
                    $messageType = "success";
                } else {
                    $message = "Une erreur est survenue lors de la suppression de vos favoris.";
                    $messageType = "error";
                }
                break;

            // 7. Suppression du compte
            case 'supprimer_compte':
                if(isset($_POST['confirm_suppression']) && $_POST['confirm_suppression'] === 'oui') {
                    // On supprime d'abord les données liées (favoris, ingrédients personnels)
                    $sql1 = "DELETE FROM recette_favorite WHERE id_utilisateur = :id_utilisateur";
                    $stmt1 = $pdo->prepare($sql1);
                    $stmt1->bindValue(':id_utilisateur', $userId);
                    $stmt1->execute();

                    $sql2 = "DELETE FROM liste_personnelle_ingredients WHERE id_utilisateur = :id_utilisateur";
                    $stmt2 = $pdo->prepare($sql2);
                    $stmt2->bindValue(':id_utilisateur', $userId);
                    $stmt2->execute();

                    // Puis on supprime le compte utilisateur
                    $sql3 = "DELETE FROM utilisateur WHERE id = :id_utilisateur";
                    $stmt3 = $pdo->prepare($sql3);
                    $stmt3->bindValue(':id_utilisateur', $userId);
                    if($stmt3->execute()) {
                        // Déconnexion
                        session_destroy();
                        header('Location: '.RACINE_SITE.'index.php?msg=compte_supprime');
                        exit();
                    } else {
                        $message = "Une erreur est survenue lors de la suppression de votre compte.";
                        $messageType = "error";
                    }
                }
                break;

            // 8. Changement de mot de passe
            case 'changer_mot_de_passe':
                if(isset($_POST['ancien_mot_de_passe']) && isset($_POST['nouveau_mot_de_passe']) && isset($_POST['confirmer_mot_de_passe'])) {
                    // Vérification de l'ancien mot de passe
                    $sql = "SELECT mot_de_passe FROM utilisateur WHERE id = :id_utilisateur";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':id_utilisateur', $userId);
                    $stmt->execute();
                    $user = $stmt->fetch();

                    if($user && password_verify($_POST['ancien_mot_de_passe'], $user['mot_de_passe'])) {
                        // Vérification que les nouveaux mots de passe correspondent
                        if($_POST['nouveau_mot_de_passe'] === $_POST['confirmer_mot_de_passe']) {
                            // Hashage du nouveau mot de passe
                            $hashedPassword = password_hash($_POST['nouveau_mot_de_passe'], PASSWORD_BCRYPT, ['cost' => 12]);
                            
                            // Mise à jour du mot de passe
                            $sql = "UPDATE utilisateur SET mot_de_passe = :mot_de_passe WHERE id = :id_utilisateur";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':mot_de_passe', $hashedPassword);
                            $stmt->bindValue(':id_utilisateur', $userId);
                            
                            if($stmt->execute()) {
                                $message = "Votre mot de passe a été modifié avec succès.";
                                $messageType = "success";
                            } else {
                                $message = "Une erreur est survenue lors de la modification de votre mot de passe.";
                                $messageType = "error";
                            }
                        } else {
                            $message = "Les nouveaux mots de passe ne correspondent pas.";
                            $messageType = "error";
                        }
                    } else {
                        $message = "Ancien mot de passe incorrect.";
                        $messageType = "error";
                    }
                }
                break;
        }
    }

    // 1. Récupération des ingrédients de l'utilisateur
    $sql = "SELECT i.id, i.nom FROM liste_personnelle_ingredients lpi
            JOIN ingredient i ON lpi.id_ingredient = i.id
            WHERE lpi.id_utilisateur = :id_utilisateur";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_utilisateur', $userId);
    $stmt->execute();
    $ingredientsUtilisateur = $stmt->fetchAll();

    // 2. Récupération des recettes favorites de l'utilisateur
    $sql = "SELECT r.*, c.nom as categorie_nom, c.couleur as categorie_couleur, c.couleurTexte as couleurTexte 
            FROM recette_favorite rf
            JOIN recette r ON rf.id_recette = r.id
            JOIN categorie c ON r.id_categorie = c.id
            WHERE rf.id_utilisateur = :id_utilisateur
            ORDER BY rf.date_enregistrement DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_utilisateur', $userId);
    $stmt->execute();
    $recettesFavorites = $stmt->fetchAll();
}

require_once('../header.php');
?>

<?php if(!empty($message)): ?>
<div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
<?php endif; ?>

<section class="toolbar">
    <div class="ctaButtons">
                <a href="#" class="tab-button active" data-tab="favoris-ingredients"><i class="fi fi-sr-heart"></i> Favoris & Ingrédients</a>
        <a href="#" class="tab-button" data-tab="parametres"><i class="fi fi-sr-settings"></i> Paramètres</a>
    </div>
</section>

<!-- Onglet Favoris et Ingrédients -->
    <section id="favoris-ingredients" class="allListUsersFavorisAndIngredients tab-content active">
        <!-- Section des ingrédients personnels -->
        <section class="listUserIngredientsPersonals">
            <h2>Mes ingrédients</h2>
            <a href="<?= RACINE_SITE ?>views/recettes.php?formIngredients=1" class="cta"><i class="fi fi-sr-cursor"></i> Voir les recettes correspondantes</a>
            <p><?= $_SESSION['user']['prenom'] ?>, vous pouvez supprimer vos ingrédients un par un :</p>
            <?php if(!empty($ingredientsUtilisateur)): ?>
                <div class="ingredients-list">
                    <div class="ingredient-tags">
                        <?php foreach($ingredientsUtilisateur as $ingredient): ?>
                            <div class="ingredient-tag">
                                <span><?= htmlspecialchars($ingredient['nom']) ?></span>
                                <form method="post" action="" class="inline-form">
                                    <input type="hidden" name="action" value="supprimer_ingredient">
                                    <input type="hidden" name="id_ingredient" value="<?= $ingredient['id'] ?>">
                                    <button type="submit" class="remove-btn" title="Retirer cet ingrédient">
                                        <i class="fi fi-sr-cross-small"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Formulaire pour supprimer tous les ingrédients -->
                <form method="post" action="" class="danger-form">
                    <input type="hidden" name="action" value="supprimer_tous_ingredients">
                    <button type="submit" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer tous vos ingrédients ?');">
                        <i class="fi fi-sr-trash"></i> Supprimer tous mes ingrédients
                    </button>
                </form>
            <?php else: ?>
                <p class="empty-state">Vous n'avez pas encore ajouté d'ingrédients à votre liste personnelle.</p>
                <a href="<?= RACINE_SITE ?>index.php" class="cta">Ajouter des ingrédients</a>
            <?php endif; ?>
        </section>
        <!-- Section des recettes favorites -->
        <section class="listUserRecipeFavoris">
            <h2>Mes recettes favorites</h2>
            <p><?= $_SESSION['user']['prenom'] ?>, vous pouvez supprimer vos recettes favorites une par une</p>
            <?php if(!empty($recettesFavorites)): ?>
                <div class="recipes">
                    <?php foreach($recettesFavorites as $recette): ?>
                        <div class="recipeBox">
                            <?php if(!empty($recette['image_url'])): ?>
                                <img src="<?= RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] ?>" alt="<?= htmlspecialchars($recette['nom']) ?>">
                            <?php else: ?>
                                <img src="<?= RACINE_SITE ?>public/assets/img/femme-cagette-legumes.jpg" alt="Image par défaut">
                            <?php endif; ?>
                            <div class="recipe-meta">
                                <h6><i class="fi fi-sr-clock"></i> <?= $recette['temps_preparation'] + $recette['temps_cuisson'] ?> min</h6>
                                <form method="post" action="" class="inline-form">
                                    <input type="hidden" name="action" value="supprimer_favori">
                                    <input type="hidden" name="id_recette" value="<?= $recette['id'] ?>">
                                    <button type="submit" class="favorite-btn is-active" title="Retirer des favoris">
                                        <i class="fi fi-sr-heart" id="heart"></i>
                                    </button>
                                </form>
                            </div>
                            <a href="<?= RACINE_SITE ?>views/recettes/recette.php?id=<?= $recette['id'] ?>" target="_blank">Voir <?= htmlspecialchars($recette['nom']) ?></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Formulaire pour supprimer tous les favoris -->
                <form method="post" action="" class="danger-form">
                    <input type="hidden" name="action" value="supprimer_tous_favoris">
                    <button type="submit" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer toutes vos recettes favorites ?');">
                        <i class="fi fi-sr-trash"></i> Supprimer tous mes favoris
                    </button>
                </form>
            <?php else: ?>
                <p class="empty-state">Vous n'avez pas encore de recettes favorites. Explorez notre catalogue de recettes pour en ajouter !</p>
                <a href="<?= RACINE_SITE ?>views/recettes.php" class="cta">Découvrir des recettes</a>
            <?php endif; ?>
        </section>
    </section>

    <!-- Onglet Paramètres du compte -->
    <section id="parametres" class="settingsUser tab-content">
        <h2>Paramètres de mon compte</h2>
        
        <!-- Informations du compte -->
        <div class="account-info">
            <h3>Mes informations</h3>
            <p><strong>Nom:</strong> <?= htmlspecialchars($_SESSION['user']['nom']) ?></p>
            <p><strong>Prénom:</strong> <?= htmlspecialchars($_SESSION['user']['prenom']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
        </div>
        
        <!-- Formulaire de changement de mot de passe -->
        <div class="change-password">
            <h3>Changer mon mot de passe</h3>
            <form method="post" action="" class="form-settings">
                <input type="hidden" name="action" value="changer_mot_de_passe">
                
                <div class="form-group">
                    <label for="ancien_mot_de_passe">Ancien mot de passe:</label>
                    <input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <label for="nouveau_mot_de_passe">Nouveau mot de passe:</label>
                    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required minlength="8">
                </div>
                
                <div class="form-group">
                    <label for="confirmer_mot_de_passe">Confirmer le nouveau mot de passe:</label>
                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required minlength="8">
                </div>
                
                <button type="submit" class="cta">Mettre à jour le mot de passe</button>
            </form>
        </div>
        
        <!-- Formulaire de suppression du compte -->
        <div class="delete-account">
            <h3>Supprimer mon compte</h3>
            <p class="warning-text">Attention : Cette action est irréversible et supprimera toutes vos données, y compris vos recettes favorites et votre liste d'ingrédients.</p>
            
            <form method="post" action="" class="form-settings">
                <input type="hidden" name="action" value="supprimer_compte">
                <input type="hidden" name="confirm_suppression" value="oui">
                
                <button type="submit" class="btn-danger" onclick="return confirm('ATTENTION : Êtes-vous vraiment sûr de vouloir supprimer définitivement votre compte ? Cette action est irréversible.');">
                    <i class="fi fi-sr-trash"></i> Supprimer définitivement mon compte
                </button>
            </form>
        </div>
    </section>
    <!-- JavaScript pour les onglets -->
<script src="<?= RACINE_SITE ?>public/assets/javascript/tabOnglet.js"></script>

<?php
require_once('../footer.php');
?>