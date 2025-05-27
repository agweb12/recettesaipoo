<?php
require_once('../../inc/functions.php');
/*
 * Ce fichier est le point d'entrée de l'application Recettes AI.
 * Il inclut les fichiers de configuration et d'en-tête,
 * Recoit toutes les requêtes
 * initialise le routeur
 * et dispatche la requête vers les bons contrôleurs. 
 */

// Requête pour récupérer la recette
// Vérifier si l'ID de la recette est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">ID de recette invalide</div>';
    exit;
}

$recipeId = intval($_GET['id']);
$pdo = connexionBDD();

// 1. Je prépare la requête pour récupérer la recette
    // Utiliser une requête préparée pour éviter les injections SQL
    $sqlRecipe = "SELECT r.id, r.nom, r.descriptif, r.instructions, r.temps_preparation, r.temps_cuisson, r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte
            FROM recette r
            JOIN categorie c ON r.id_categorie = c.id
            WHERE r.id = :id_recette";
    $stmtRecipe = $pdo->prepare($sqlRecipe);
    $stmtRecipe->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
    $stmtRecipe->execute();
    $recette = $stmtRecipe->fetch();

    // Vérifier si la recette existe
    if (!$recette) {
        echo '<div class="alert alert-error">Recette introuvable</div>';
        exit;
    }

// 2. Je récupère les étiquettes de cette recette
    $sqlEtiquettes = "SELECT e.id, e.nom, e.descriptif FROM etiquette e 
                        JOIN recette_etiquette re ON e.id = re.id_etiquette 
                        WHERE re.id_recette = :id_recette";
    $stmtEtiquettes = $pdo->prepare($sqlEtiquettes);
    $stmtEtiquettes->bindValue(':id_recette', $recette['id'], PDO::PARAM_INT);
    $stmtEtiquettes->execute();
    $etiquettes = $stmtEtiquettes->fetchAll();

    $etiquettesIds = [];
    foreach ($etiquettes as $etiquette) {
        $etiquettesIds[] = $etiquette['id'];
    }

// 3. Je vérifie si la recette est en favoris pour l'utilisateur connecté
    $isFavorite = false;
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['id'];
        $stmtFavorite = $pdo->prepare("SELECT COUNT(*) as count_favoris FROM recette_favorite 
                                    WHERE id_utilisateur = :id_utilisateur 
                                    AND id_recette = :id_recette");
        $stmtFavorite->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmtFavorite->bindValue(':id_recette', $recipeId, PDO::PARAM_INT);
        $stmtFavorite->execute();
        $favoriteResult = $stmtFavorite->fetch();
        $isFavorite = ($favoriteResult['count_favoris'] > 0);
    }

// 4. Je vérifie si l'utilisateur est connecté et a soumis une liste d'ingrédients
    $ingredientsUser = []; // Tableau pour stocker les ingrédients de l'utilisateur
    $possedeListIngredients = false;
    // Je vérifie si l'utilisateur est connecté
    if(isLoggedIn()){
        $pdo = connexionBDD();
        $userId = $_SESSION['user']['id'];

        // Je récupère les ingrédients de l'utilisateur
        $sqlUserIngredients = "SELECT id_ingredient FROM liste_personnelle_ingredients 
                            WHERE id_utilisateur = :id_utilisateur";
        $stmtUserIngredients = $pdo->prepare($sqlUserIngredients);
        $stmtUserIngredients->bindValue(':id_utilisateur', $userId, PDO::PARAM_INT);
        $stmtUserIngredients->execute();

        if($stmtUserIngredients->rowCount() > 0){
            $possedeListIngredients = true;

            while($row = $stmtUserIngredients->fetch()){
                $ingredientsUser[] = $row['id_ingredient'];
            }
        }
    }

// 5. Je récupère les ingrédients de cette recette pour inclure l'ID des ingrédients
    $sqlIngredients = "SELECT i.id, i.nom, lri.quantite, um.abreviation as unite, um.nom as nomUnite 
                        FROM ingredient i
                        JOIN liste_recette_ingredients lri ON i.id = lri.id_ingredient
                        JOIN unite_mesure um ON lri.id_unite = um.id
                        WHERE lri.id_recette = :id_recette";
    $stmtIngredients = $pdo->prepare($sqlIngredients);
    $stmtIngredients->bindValue(':id_recette', $recette['id'], PDO::PARAM_INT);
    $stmtIngredients->execute();
    $ingredients = $stmtIngredients->fetchAll();
    $ingredientsList = [];

    // Je boucle sur les ingrédients pour déterminer s'ils sont disponibles ou manquants
    // Je les mets dans un tableau pour les afficher et je les sécurise avec htmlspecialchars
    foreach ($ingredients as $ingredient) {
        // Je vérifie si l'ingrédient est disponible dans la liste de l'utilisateur
        // Je vérifie si l'id de l'ingrédient est dans le tableau des ingrédients de l'utilisateur
        $estDisponible = in_array($ingredient['id'], $ingredientsUser);

        $ingredientsList[] = [
            'id' => $ingredient['id'],
            'nom' => htmlspecialchars($ingredient['nom']),
            'quantite' => htmlspecialchars($ingredient['quantite']),
            'unite' => htmlspecialchars($ingredient['unite']),
            'disponible' => $estDisponible,
            'possedeListIngredients' => $possedeListIngredients
        ];
    }

// 6. Je récupère la liste de toutes les unités de mesure
    $sqlUniteMesure = "SELECT id, nom, abreviation FROM unite_mesure ORDER BY nom ASC";
    $stmtUniteMesure = $pdo->prepare($sqlUniteMesure);
    $stmtUniteMesure->execute();
    $uniteMesureList = $stmtUniteMesure->fetchAll();
    // Je boucle sur les unités de mesure pour les sécuriser avec htmlspecialchars
    foreach ($uniteMesureList as $uniteMesure) {
        $uniteMesureList[] = [
            'id' => htmlspecialchars($uniteMesure['id']),
            'nom' => htmlspecialchars($uniteMesure['nom']),
            'abreviation' => htmlspecialchars($uniteMesure['abreviation'])
        ];
    } 

// 7. Je prépare les metadonnées pour la page
$titlePage = htmlspecialchars($recette['nom']) . " - Recette AI";
$descriptionPage = "Découvrez comment préparer " . htmlspecialchars($recette['nom']) . ". " . substr(htmlspecialchars($recette['descriptif']), 0, 150) . "...";;
$indexPage = "index";
$followPage = "follow";
$keywordsPage = "Recettes AI, recette, ai, intelligence artificielle, cuisine, ingrédients, recettes, trouver une recette";

require_once('../../inc/modalConnexionController.php');

if(isLoggedIn()){
    $userId = $_SESSION['user']['id'];
}
require_once('../header.php');
?>
<?php if(!isLoggedIn()){
    include ('../../inc/modalConnexion.php'); 
}
?>

<section class="heroIngredients">
    <h1><?= $recette['nom'] ?></h1>
    <div class="boxHeroIngredients">
        <?php if(!isLoggedIn()): ?>
            <p>Inscrivez-vous ou connectez-vous pour trouver vos recettes en fonction de votre liste d'ingrédients</p>
            <button type="button" id="btnModal">Commencez à trouver votre recette du jour</button>
        <?php else: ?>
                <h5>Ajoutez cette recette dans vos favoris si elle vous plaît</h5>
        <?php endif; ?>
    </div>
</section>
<section class="oneRecipe">
    <div class="recipeBox <?= $isFavorite ? 'is-active' : '' ?>"
        data-id="<?= $recette['id'] ?>"
        data-categorie="<?= $recette['id_categorie'] ?>"
        data-etiquette="<?=implode(',', $etiquettesIds) ?>">
        <img src="<?php 
            if(!empty($recette['image_url'])){
                echo RACINE_SITE . 'public/assets/recettes/' . html_entity_decode($recette['image_url']);
            } else{
                echo RACINE_SITE . 'public/assets/img/femme-cuisine.jpg';
            }?>" alt="<?= html_entity_decode($recette['nom']) ?>">
        <div class="recipe-meta">
            <span><i class="fi fi-sr-clock"></i> Préparation: <?= $recette['temps_preparation'] ?> min</span>
            <span><i class="fi fi-sr-flame"></i> Cuisson: <?= $recette['temps_cuisson'] ?> min</span>
            <span style="background-color:<?= html_entity_decode($recette['couleur_categorie']) ?>; color: <?= html_entity_decode($recette['couleurTexte']) ?>; border-radius:3rem; padding:.3rem;"><?= $recette['categorie'] ?></span>
            <span><i class="fi fi-sr-stats"></i> <?= ucfirst($recette['difficulte']) ?></span>
            <?php if(isLoggedIn()):?>
                <button class="favorite-btn <?= $isFavorite ? 'is-active' : '' ?>" 
                data-id="<?= $recette['id'] ?>" 
                data-favorite="<?= $isFavorite ? '1' : '0' ?>">
                    <i class="fi <?= $isFavorite ? 'fi-sr-heart' : 'fi-rr-heart' ?>" style="font-size:2rem"></i>
                </button>
            <?php endif; ?>
        </div>
        <div class="detailsBox">
            <h4><?= html_entity_decode($recette['nom']) ?></h4>
            <p><?= nl2br(html_entity_decode($recette['descriptif'])) ?></p>
            <div class="etiquettes">
                <?php foreach ($etiquettes as $etiquette): ?>
                    <span class="badge" title="<?= html_entity_decode($etiquette['descriptif']) ?>" style="background-color:<?= html_entity_decode($recette['couleur_categorie']) ?>; color:<?= html_entity_decode($recette['couleurTexte'])?>"><?= html_entity_decode($etiquette['nom']) ?></span>
                <?php endforeach; ?>
                </div>
            <div class="instructions">
                <?php
                    $instructions = explode("##", $recette['instructions']);
                    foreach ($instructions as $instruction) {
                        echo "<p style='text-align:justify'>" . html_entity_decode($instruction) . "</p>";
                    }
                ?>
            </div>
            <a href="<?= RACINE_SITE?>views/recettes.php">Rechercher des recettes</a>
        </div>
    </div>
    <div class="ingredientsBox">
        <h4>Ingrédients</h4>
        <?php if(!empty($ingredientsList)): ?>
            <ul>
            <?php foreach ($ingredientsList as $ingredient): ?>
                <li class="<?php 
                    if($ingredient['possedeListIngredients']) {
                        echo $ingredient['disponible'] ? 'ingredient-disponible' : 'ingredient-manquant';
                    } else {
                        echo 'ingredient-non-disponible';
                    }
                ?>">
                    <div class="ingredient-info">
                        <?php if($ingredient['possedeListIngredients']): ?>
                            <i class="fi <?= $ingredient['disponible'] ? 'fi-sr-check' : 'fi-sr-cross' ?>"></i>
                        <?php endif; ?>
                        <span class="ingredientNom"><?= $ingredient['nom'] ?></span>
                    </div>
                    <span class="ingredientQuantite">
                        <?php if(!empty($ingredient['quantite'])): ?>
                            <?= $ingredient['quantite'] ?> <?= $ingredient['unite'] ?>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php if(isLoggedIn()): ?>
            <div class="legend">
                <p><i class="fi fi-sr-check"></i> Ingrédient disponible dans votre liste</p>
                <p><i class="fi fi-sr-cross"></i> Ingrédient manquant dans votre liste</p>
                <details>
                    <summary>Unités de Mesure</summary>
                    <div class="unite">
                    <?php if(!empty($ingredientsList)): ?>
                        <?php foreach ($uniteMesureList as $uniteMesure): ?>
                            <p><span><?= $uniteMesure['abreviation'] ?></span> : <?= $uniteMesure['nom'] ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </details>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <p>Aucun ingrédient trouvé pour cette recette.</p>
        <?php endif; ?>
        <?php if(!isLoggedIn()): ?>
            <p class="info-ingredients">
                <a href="<?= RACINE_SITE ?>views/connexion.php">Connectez-vous</a> pour voir quels ingrédients vous manquent pour cette recette.
            </p>
        <?php endif; ?>
    </div>
</section>
<?php
require_once('../footer.php');
?>