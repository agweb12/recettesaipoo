<?php
require_once('../inc/functions.php');

// Définition des variables pour la page
$titlePage = "Recettes AI";
$descriptionPage = "Recettes AI est un site qui vous permet de trouver des recettes de cuisine en fonction des ingrédients que vous avez chez vous.";
$indexPage = "index";
$followPage = "follow";
$keywordsPage = "Recettes AI, recette, ai, intelligence artificielle, cuisine, ingrédients, recettes, trouver une recette";

$recettes= []; // Tableau pour stocker les recettes
$ingredientsUtilisateur = []; // Tableau pour stocker les ingrédients de l'utilisateur
// Vérifie si l'utilisateur est connecté et si le formulaire d'ingrédients a été soumis

$whereConditions = []; // Tableau pour stocker les conditions WHERE de la requête SQL
$filterParams = []; // Tableau pour stocker les paramètres de filtre

// Si l'utilisateur est connecté et que le formulaire d'ingrédients a été soumis
if(isLoggedIn() && isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1){
    $pdo = connexionBDD();
    $userId = $_SESSION['user']['id'];

    // Je récupère tous les ingrédients de l'utilisateur
    $sql = "SELECT i.id, i.nom FROM liste_personnelle_ingredients lpi
            JOIN ingredient i ON lpi.id_ingredient = i.id
            WHERE lpi.id_utilisateur = :id_utilisateur";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_utilisateur', $userId);
    $stmt->execute();
    $ingredientsUtilisateur = $stmt->fetchAll();

    // Je cherche des recettes correspondantes
    // Cette requête trouve les recettes qui utilisent au moins un des ingrédients de l'utilisateur
    $sql = "SELECT DISTINCT r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte, COUNT(DISTINCT lri.id_ingredient) AS nombre_ingredients_correspondants
            FROM recette r
            JOIN liste_recette_ingredients lri ON r.id = lri.id_recette
            JOIN categorie c ON r.id_categorie = c.id
            WHERE lri.id_ingredient IN (
                SELECT id_ingredient
                FROM liste_personnelle_ingredients lpi
                WHERE lpi.id_utilisateur = :id_utilisateur) 
            GROUP BY r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, r.difficulte, r.image_url, 
                c.nom, c.id, c.couleur
        ORDER BY nombre_ingredients_correspondants DESC, r.nom ASC";
        // Ajout du COUNT(DISTINCT lri.id_ingredient) : Cette fonction compte le nombre d'ingrédients distincts qui correspondent entre la recette et la liste personnelle de l'utilisateur.
        // Ajout de GROUP BY : Nécessaire lorsqu'on utilise une fonction d'agrégation comme COUNT, pour regrouper les résultats par recette.
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_utilisateur', $userId);
    $stmt->execute();
    $recettes = $stmt->fetchAll();

    // Vérification si des recettes ont été ajoutées aux favoris de l'utilisateur
    $sql = "SELECT id_recette FROM recette_favorite WHERE id_utilisateur = :id_utilisateur";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_utilisateur', $userId);
    $stmt->execute();
    $recettesFavoris = $stmt->fetchAll();
    $recettesFavorisIds = [];
    foreach($recettesFavoris as $recetteFavoris){
        $recettesFavorisIds[] = $recetteFavoris['id_recette'];
    }

}

// Si l'utilisateur est connecté mais n'a pas soumis le formulaire d'ingrédients, on récupère ses recettes favorites
if(isLoggedIn()){
    $pdo = connexionBDD();
    $userId = $_SESSION['user']['id'];
    // Vérification si des recettes ont été ajoutées aux favoris de l'utilisateur
    $sql = "SELECT id_recette FROM recette_favorite WHERE id_utilisateur = :id_utilisateur";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_utilisateur', $userId);
    $stmt->execute();
    $recettesFavoris = $stmt->fetchAll();
    $recettesFavorisIds = [];
    foreach($recettesFavoris as $recetteFavoris){
        $recettesFavorisIds[] = $recetteFavoris['id_recette'];
    }
}

// Si l'utilisateur a soumis une recherche ou des filtres, on construit la requête SQL
if(isset($_GET['search']) && !empty($_GET['search'])){
    $searchTerm = $_GET['search'];
    $whereConditions[] = "(r.nom LIKE :search OR r.descriptif LIKE :search)";
    $filterParams[':search'] = "%{$searchTerm}%";
}

// Traiter les filtres
$filterTypes = ['difficulte', 'temps_preparation', 'temps_cuisson', 'categorie', 'etiquette'];
foreach($filterTypes as $type) {
    $paramName = "filtre_{$type}";
    // Vérifier si le paramètre de filtre est défini et non vide
    if(isset($_GET[$paramName]) && !empty($_GET[$paramName])) {
        $values = explode(',', $_GET[$paramName]); // Séparer les valeurs par des virgules
        
        if($type === 'etiquette') {
            // Traitement spécial pour les étiquettes (utiliser une sous-requête)
            $whereConditions[] = "r.id IN (
                SELECT DISTINCT re.id_recette 
                FROM recette_etiquette re 
                WHERE re.id_etiquette IN (" . implode(',', array_map('intval', $values)) . ")
            )";
        }
        else if($type === 'temps_preparation' || $type === 'temps_cuisson') {
            // Traitement spécial pour les temps
            $timeConditions = [];
            foreach($values as $value) {
                $value = intval($value);
                if($value === 15) {
                    $timeConditions[] = "r.{$type} <= 15";
                } else if($value === 30) {
                    $timeConditions[] = "r.{$type} <= 30";
                } else if($value === 60) {
                    $timeConditions[] = "r.{$type} <= 60";
                } else if($value === 120) {
                    $timeConditions[] = "r.{$type} > 60";
                }
            }
            // Si au moins une condition de temps est définie, on les combine avec OR
            // pour permettre à une recette de correspondre si elle satisfait au moins une des conditions
            if(count($timeConditions) > 0) {
                $whereConditions[] = "(" . implode(' OR ', $timeConditions) . ")";
            }
        }
        // Pour les autres types de filtres (difficulté, catégorie), on utilise IN
        else {
            // Filtres standards (difficulté, catégorie)
            $placeholders = [];
            foreach($values as $i => $val) {
                $placeholder = ":{$type}_{$i}";
                $placeholders[] = $placeholder;
                $filterParams[$placeholder] = $val;
            }
            $whereConditions[] = "r.{$type} IN (" . implode(',', $placeholders) . ")";
        }
    }
}

// Construire la requête SQL complète
$sql = "SELECT r.id, r.nom, r.descriptif, r.temps_preparation, r.temps_cuisson, 
        r.difficulte, r.image_url, c.nom AS categorie, c.id AS id_categorie, 
        c.couleur AS couleur_categorie, c.couleurTexte AS couleurTexte
        FROM recette r
        JOIN categorie c ON r.id_categorie = c.id";

// Ajouter les conditions WHERE si elles existent
if(count($whereConditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $whereConditions);
}
// Ajouter la clause ORDER BY pour trier les résultats
$sql .= " ORDER BY r.date_creation DESC";

// Si on utilise la recherche ou les filtres, exécuter la requête
if(isset($_GET['search']) || isset($_GET['filtre_difficulte']) || isset($_GET['filtre_temps_preparation']) || 
   isset($_GET['filtre_temps_cuisson']) || isset($_GET['filtre_categorie']) || isset($_GET['filtre_etiquette'])) {
    
    $pdo = connexionBDD();
    $stmt = $pdo->prepare($sql);
    
    // Lier tous les paramètres de filtre
    foreach($filterParams as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    
    $stmt->execute();
    $recettesRecherche = $stmt->fetchAll();
}

require_once('../inc/modalConnexionController.php');

if(isLoggedIn()){
    $user = $_SESSION['user'];
}
require_once('header.php');
?>

<?php if(!isLoggedIn()){
    // Inclure le modal de connexion
    require_once('../inc/modalConnexion.php');
}
?>

<!-- Section des recettes -->
<?php if(isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1): ?>
    <?php if(isLoggedIn()): ?>
    <section class="listeRecipesIngredients">
        <!-- Afficher les ingrédients de l'utilisateur -->
        <?php if(!empty($ingredientsUtilisateur)): ?>
        <section class="ingredients-list">
            <h4>Vos ingrédients</h4>
            <div class="ingredient-tags">
                <?php foreach($ingredientsUtilisateur as $ingredient): ?>
                    <span class="ingredient-tag"><?= $ingredient['nom'] ?></span>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        <!-- Afficher les recettes correspondantes -->
        <?php if(!empty($recettes)): ?>
        <section class="columnRecipe">
            <h4>Vos recettes correspondantes</h4>
            <div class="recipes">
            <?php foreach($recettes as $recette): ?>
                <div class="recipeBox">
                    <img src="<?= (!empty($recette['image_url'])) ? RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] : RACINE_SITE . 'public/assets/img/femme-cuisine.jpg' ?>" alt="<?= $recette['nom'] ?>">
                    <div class="recipe-meta">
                        <span><i class="fi fi-sr-clock"></i> Préparation: <?= $recette['temps_preparation'] ?> min</span>
                        <span><i class="fi fi-sr-flame"></i> Cuisson: <?= $recette['temps_cuisson'] ?> min</span>
                        <span style="background-color:<?= $recette['couleur_categorie'] ?>; color:<?= $recette['couleurTexte'] ?>;  border-radius:3rem; padding:.3rem;"><?= $recette['categorie'] ?></span>
                        <span><i class="fi fi-sr-stats"></i><?= ucfirst($recette['difficulte']) ?></span>
                        <?php if(isLoggedIn()): ?>
                            <span>
                                <?php if(in_array($recette['id'], $recettesFavorisIds)): ?>
                                    <i class="fi fi-sr-heart"></i>
                                <?php else: ?>
                                    <i class="fi fi-rr-heart"></i>
                                <?php endif; ?>
                                Favoris
                            </span>
                        <?php endif; ?>
                        <span><i class="fi fi-sr-list-check"></i>
                        <?php 
                            // déterminer le nombre d'ingrédients total de chaque recette
                            $sql = "SELECT COUNT(*) AS nombre_ingredients_total 
                                    FROM liste_recette_ingredients 
                                    WHERE id_recette = :id_recette";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':id_recette', $recette['id']);
                            $stmt->execute();
                            $nombreIngredientsTotal = $stmt->fetchColumn();
                            $recette['nombre_ingredients_total'] = $nombreIngredientsTotal;
                        ?>
                        <?php if($recette['nombre_ingredients_correspondants'] == 1) : ?>
                            <?= $recette['nombre_ingredients_correspondants'] ?> ingrédient / <?= $nombreIngredientsTotal ?? '...' ?>
                        <?php elseif($recette['nombre_ingredients_correspondants'] > 1) : ?>
                            <?= $recette['nombre_ingredients_correspondants'] ?> ingrédients / <?= $nombreIngredientsTotal ?? '...' ?>
                        <?php else : ?>
                            <?= $recette['nombre_ingredients_correspondants'] ?> ingrédient / <?= $nombreIngredientsTotal ?? '...' ?> 
                        <?php endif; ?>
                        </span>
                    </div>
                    <p><?= $recette['nom'] ?></p>
                    <p><?= substr($recette['descriptif'], 0, 100) ?><?= strlen($recette['descriptif']) > 100 ? '...' : '' ?></p>
                    <a href="<?= RACINE_SITE ?>views/recettes/recette.php?id=<?= $recette['id'] ?>" target="_blank">Voir la recette</a>
                </div>
            <?php endforeach;?>
            </div>
        </section>
        <?php elseif(isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1): ?>
        <section class="no-recipes">
            <h4>Aucune recette trouvée</h4>
            <p>Nous n'avons pas trouvé de recettes correspondant à vos ingrédients. Essayez avec d'autres ingrédients ou consultez notre liste complète de recettes.</p>
        </section>
        <?php endif; ?>
    </section>
    <?php endif; ?>

<?php else: ?>
    <!-- Hero Section -->
    <section class="heroIngredients">
        <h1>Toutes les recettes</h1>
        <div class="boxHeroIngredients">
            <?php if(!isLoggedIn()): ?>
                <p>Inscrivez-vous ou connectez-vous pour trouver vos recettes en fonction de votre liste d'ingrédients</p>
                <button type="button" id="btnModal">Commencez à trouver votre recette du jour</button>
            <?php else: ?>
                <h5>Trouvez votre recette à l'aide du système de filtrage en fonction de vos envies</h5>
            <?php endif; ?>
        </div>
    </section>
    <!-- Hero Section End -->
    <section class="filterAllRecipes">
        <!-- Filtre des recettes -->
        <aside class="filterRecipes">
            <div class="filterTitle">
                <h3>Filtrer les recettes</h3>
                <button id="reset-filters" class="btn-reset">Réinitialiser les filtres</button>
            </div>
            <details>
                <summary>Clique pour voir les filtres</summary>
                <div class="groupCol">
                    <div class="filter-groupRow">
                        <div class="filter-group">
                            <h4>Difficulté</h4>
                            <div class="filter-options">
                                <label><input type="checkbox" class="filter-checkbox" data-type="difficulte" value="facile"> Facile</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="difficulte" value="moyenne"> Moyenne</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="difficulte" value="difficile"> Difficile</label>
                            </div>
                        </div>
                        <div class="filter-group">
                            <h4>Temps de préparation</h4>
                            <div class="filter-options">
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="15"> Moins de 15 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="30"> Moins de 30 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="60"> Moins de 1h</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_preparation" value="120"> Plus de 1h</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-groupRow">
                        <div class="filter-group">
                            <h4>Temps de cuisson</h4>
                            <div class="filter-options">
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="15"> Moins de 15 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="30"> Moins de 30 min</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="60"> Moins de 1h</label>
                                <label><input type="checkbox" class="filter-checkbox" data-type="temps_cuisson" value="120"> Plus de 1h</label>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <h4>Catégorie</h4>
                            <details class="filter-options">
                                <summary>Choisir une catégorie</summary>
                                <?php
                                $pdo = connexionBDD();
                                // Requête pour récupérer toutes les catégories
                                $sql = "SELECT id, nom, couleur, couleurTexte FROM categorie ORDER BY nom";
                                $stmt = $pdo->query($sql);
                                while ($categorie = $stmt->fetch()) {
                                    echo '<label><i style="display:block;background-color:'.$categorie['couleur'].';color:'.$categorie['couleurTexte'].'; border-radius:3rem; width:14px; height:14px;"></i><input type="checkbox" class="filter-checkbox" data-type="categorie" value="' . $categorie['id'] . '"> ' . $categorie['nom'] . '</label>';
                                }
                                ?>
                            </details>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Étiquettes</h4>
                        <details class="filter-options">
                            <summary>Choisir une étiquette</summary>
                            <?php
                            $pdo = connexionBDD();
                            // Requête pour récupérer toutes les étiquettes
                            $sql = "SELECT id, nom FROM etiquette ORDER BY nom";
                            $stmt = $pdo->query($sql);
                            while ($etiquette = $stmt->fetch()) {
                                echo '<label><input type="checkbox" class="filter-checkbox" data-type="etiquette" value="' . $etiquette['id'] . '"> ' . $etiquette['nom'] . '</label>';
                            }
                            ?>
                        </details>
                    </div>
                </div>
            </details>
        </aside>
        <!-- Fin du filtre des recettes -->
        <!-- Affichage des recettes -->
        <section class="allRecipes">
            <!-- Barre de recherche -->
            <div class="search-container">
                <form id="search-form" method="get" action="">
                    <div class="inputBox">
                        <i class="fi fi-sr-search"></i>
                        <input type="text" name="search" id="search-input" placeholder="Rechercher une recette..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                    <button type="submit" class="search-btn">Rechercher</button>
                </form>
            </div>
            <!-- Fin de la barre de recherche -->
            <!-- Affichage des recettes filtrées -->
            <?php if(isset($_GET['search']) || isset($_GET['filtre_difficulte']) || isset($_GET['filtre_temps_preparation']) || isset($_GET['filtre_temps_cuisson']) || isset($_GET['filtre_categorie']) || isset($_GET['filtre_etiquette'])) : ?>
            <section class="recipes">
            <?php
            if(!empty($recettesRecherche)) {
                foreach($recettesRecherche as $recette) {
                    // Récupérer les étiquettes de cette recette
                    $sqlEtiquettes = "SELECT e.id, e.nom FROM etiquette e 
                                    JOIN recette_etiquette re ON e.id = re.id_etiquette 
                                    WHERE re.id_recette = :id_recette";
                    $stmtEtiquettes = $pdo->prepare($sqlEtiquettes);
                    $stmtEtiquettes->execute(['id_recette' => $recette['id']]);
                    $etiquettes = $stmtEtiquettes->fetchAll();
                    
                    $etiquettesIds = [];
                    foreach ($etiquettes as $etiquette) {
                        $etiquettesIds[] = $etiquette['id'];
                    }

                    // Créer un élément de recette avec des attributs data pour le filtrage
                    echo '<div class="recipeBox"
                            data-difficulte="' . $recette['difficulte'] . '" 
                            data-temps_preparation="' . $recette['temps_preparation'] . '" 
                            data-temps_cuisson="' . $recette['temps_cuisson'] . '" 
                            data-categorie="' . $recette['id_categorie'] . '" 
                            data-etiquette="' . implode(',', $etiquettesIds) . '">
                            <img src="' . (!empty($recette['image_url']) ? RACINE_SITE . 'public/assets/recettes/' . $recette['image_url'] : RACINE_SITE . 'public/assets/img/femme-cuisine.jpg') . '" alt="' . $recette['nom'] . '">
                            <div class="recipe-meta">
                                <span><i class="fi fi-sr-clock"></i> Préparation: ' . $recette['temps_preparation'] . ' min</span>
                                <span><i class="fi fi-sr-flame"></i> Cuisson: ' . $recette['temps_cuisson'] . ' min</span>
                                <span style="background-color:'.$recette['couleur_categorie'].'; color:'.$recette['couleurTexte'].'; border-radius:3rem; padding:.3rem;">' . $recette['categorie'] . '</span>
                                <span><i class="fi fi-sr-stats"></i> ' . ucfirst($recette['difficulte']) . '</span>';
                    if(isLoggedIn()){
                        echo '<span>';
                                if(in_array($recette['id'], $recettesFavorisIds)){
                                    echo '<i class="fi fi-sr-heart"></i>';
                                } else {
                                    echo '<i class="fi fi-rr-heart"></i>';
                                }
                        echo ' Favoris</span>';
                    }

                    echo '<span><i class="fi fi-sr-list-check"></i> ' . count($etiquettes) . ' étiquette(s)</span>
                            </div>
                            <h4>' . $recette['nom'] . '</h4>
                            <p>' . (strlen($recette['descriptif']) > 100 ? substr($recette['descriptif'], 0, 100) . '...' : $recette['descriptif']) . '</p>
                            <a href="'.RACINE_SITE.'views/recettes/recette.php?id=' . $recette['id'] . '">Voir la recette</a>
                        </div>';
                }
            }
            ?>
            <!-- Si aucune recette n'est trouvée -->
        </section>
        <!-- Fin de l'affichage des recettes filtrées -->
        <?php else: ?>
        <!-- Si aucune recherche ou filtre n'est appliqué, afficher un message d'état vide -->
        <div class="empty-state">
            <i class="fi fi-sr-search"></i>
            <h3>Trouvez votre recette parfaite</h3>
            <p>Utilisez la barre de recherche et les filtres pour découvrir des recettes qui correspondent à vos envies !</p>
            <p>Vous pouvez rechercher par nom de recette ou par description.</p>
        </div>
        <?php endif; ?>
        </section>
        <!-- Fin de l'affichage des recettes -->
    </section>
<?php endif; ?>
<!-- Fin de la section des recettes -->
<!-- Carousel des fonctionnalités -->
<section class="features">
    <h2>nos fonctionnalités</h2>
    <p>Nous proposons plusieurs fonctionnalités pour vous aider à trouver la recette parfaite :</p>
    <div class="features-carousel">
        <div class="features-carousel-container">
            <div class="feature-slide">
                <i class="fi fi-sr-search"></i>
                <strong>Recherche par ingrédients</strong>
                <p>Saisissez les ingrédients que vous avez et nous vous proposerons des recettes adaptées.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-shopping-cart"></i>
                <strong>Liste d'ingrédients Personnelles</strong>
                <p>Créez votre liste d'ingrédients à partir des ingrédients existants, et retrouver les recettes correspondantes à votre liste.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-filter"></i>
                <strong>Filtres de recherche</strong>
                <p>Affinez votre recherche en fonction de vos préférences alimentaires (végétarien, sans gluten, etc.).</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-crown"></i>
                <strong>Recettes populaires</strong>
                <p>Découvrez les recettes les plus populaires de notre communauté.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-heart"></i>
                <strong>Favoris</strong>
                <p>Enregistrez vos recettes préférées pour y accéder facilement.</p>
            </div>
            <div class="feature-slide">
                <i class="fi fi-sr-share"></i>
                <strong>Partage</strong>
                <p>Partagez vos recettes préférées avec vos amis et votre famille.</p>
            </div>
        </div>

        <div class="carousel-nav">
            <button class="carousel-btn prev-btn"><i class="fi fi-sr-angle-left"></i></button>
            <button class="carousel-btn next-btn"><i class="fi fi-sr-angle-right"></i></button>
        </div>

        <div class="carousel-indicators">
            <!-- Créez un indicateur pour chaque slide -->
            <span class="indicator active"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
        </div>
    </div>
</section>
<!-- Fin du carousel des fonctionnalités -->
<?php
require_once('footer.php');
?>