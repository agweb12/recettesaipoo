<?php
require_once('../../inc/functions.php');
$titlePage = "Gestion des Recettes";
$descriptionPage = "Gérer les recettes de Recette AI";
$indexPage = "noindex";
$followPage = "nofollow";
$keywordsPage = "";
$info = "";

// Vérification des droits d'accès
if (!isset($_SESSION['admin'])) {
    header('Location: ' . RACINE_SITE . 'index.php');
    exit();
}

// Vérification de l'action
$action = isset($_GET['action']) ? $_GET['action'] : '';
$recetteEdit = null;

// Récupération des catégories pour les formulaires
$pdo = connexionBDD();
$stmtCategories = $pdo->prepare("SELECT id, nom FROM categorie ORDER BY nom ASC");
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll();

// Récupération des étiquettes pour les formulaires
$stmtEtiquettes = $pdo->prepare("SELECT id, nom FROM etiquette ORDER BY nom ASC");
$stmtEtiquettes->execute();
$etiquettes = $stmtEtiquettes->fetchAll();

// Récupération des ingrédients pour les formulaires
$stmtIngredients = $pdo->prepare("SELECT id, nom FROM ingredient ORDER BY nom ASC");
$stmtIngredients->execute();
$ingredients = $stmtIngredients->fetchAll();

// Récupération des unités de mesure
$stmtUnites = $pdo->prepare("SELECT id, nom, abreviation FROM unite_mesure ORDER BY nom ASC");
$stmtUnites->execute();
$unites = $stmtUnites->fetchAll();

// Chargement d'une recette existante pour édition
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Récupérer les détails de la recette
    $stmt = $pdo->prepare("SELECT * FROM recette WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $recetteEdit = $stmt->fetch();
    
    if ($recetteEdit) {
        // Récupérer les étiquettes associées à cette recette
        $stmtRecetteEtiquettes = $pdo->prepare("SELECT id_etiquette FROM recette_etiquette WHERE id_recette = :id_recette");
        $stmtRecetteEtiquettes->bindParam(':id_recette', $id, PDO::PARAM_INT);
        $stmtRecetteEtiquettes->execute();
        $recetteEtiquettesData = $stmtRecetteEtiquettes->fetchAll(PDO::FETCH_COLUMN);
        
        // Récupérer les ingrédients associés à cette recette
        $stmtRecetteIngredients = $pdo->prepare("SELECT id_ingredient, quantite, id_unite FROM liste_recette_ingredients WHERE id_recette = :id_recette");
        $stmtRecetteIngredients->bindParam(':id_recette', $id, PDO::PARAM_INT);
        $stmtRecetteIngredients->execute();
        $recetteIngredientsData = $stmtRecetteIngredients->fetchAll();
    }
}

// Traitement de la mise à jour d'une recette
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $descriptif = htmlspecialchars(trim($_POST['descriptif']));
    $instructions = htmlspecialchars(trim($_POST['instructions']));
    $temps_preparation = intval($_POST['temps_preparation']);
    $temps_cuisson = intval($_POST['temps_cuisson']);
    $difficulte = htmlspecialchars(trim($_POST['difficulte']));
    $id_categorie = intval($_POST['id_categorie']);
    
    // Gestion de l'image téléchargée
    $image_url = isset($_POST['image_url_actuelle']) ? $_POST['image_url_actuelle'] : null;
    
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image_url']['name'];
        $tmp = explode('.', $filename);
        $ext = strtolower(end($tmp));
        
        if (in_array($ext, $allowed)) {
            $newFilename = 'recette_' . $id . '_' . time() . '.' . $ext;
            $uploadDir = '../../public/assets/recettes/images/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $destination = $uploadDir . $newFilename;
            
            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $destination)) {
                $image_url = 'images/' . $newFilename;
            }
        }
    }
    
    // Vérification des champs
    if (empty($nom) || empty($descriptif) || empty($instructions)) {
        $info = alert("Veuillez remplir tous les champs obligatoires.", "error");
    } else {
        // Mise à jour de la recette
        $pdo = connexionBDD();
        $sql = "UPDATE recette SET nom = :nom, descriptif = :descriptif, instructions = :instructions, 
                temps_preparation = :temps_preparation, temps_cuisson = :temps_cuisson, 
                difficulte = :difficulte, id_categorie = :id_categorie";
        
        if ($image_url) {
            $sql .= ", image_url = :image_url";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':descriptif', $descriptif);
        $stmt->bindParam(':instructions', $instructions);
        $stmt->bindParam(':temps_preparation', $temps_preparation, PDO::PARAM_INT);
        $stmt->bindParam(':temps_cuisson', $temps_cuisson, PDO::PARAM_INT);
        $stmt->bindParam(':difficulte', $difficulte);
        $stmt->bindParam(':id_categorie', $id_categorie, PDO::PARAM_INT);
        
        if ($image_url) {
            $stmt->bindParam(':image_url', $image_url);
        }
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Mise à jour des étiquettes
            // D'abord supprimer les associations existantes
            $stmtDeleteEtiquettes = $pdo->prepare("DELETE FROM recette_etiquette WHERE id_recette = :id_recette");
            $stmtDeleteEtiquettes->bindParam(':id_recette', $id, PDO::PARAM_INT);
            $stmtDeleteEtiquettes->execute();
            
            // Puis ajouter les nouvelles associations
            if (isset($_POST['etiquettes']) && is_array($_POST['etiquettes'])) {
                $stmtInsertEtiquette = $pdo->prepare("INSERT INTO recette_etiquette (id_recette, id_etiquette) VALUES (:id_recette, :id_etiquette)");
                
                foreach ($_POST['etiquettes'] as $etiquetteId) {
                    $stmtInsertEtiquette->bindParam(':id_recette', $id, PDO::PARAM_INT);
                    $stmtInsertEtiquette->bindParam(':id_etiquette', $etiquetteId, PDO::PARAM_INT);
                    $stmtInsertEtiquette->execute();
                }
            }
            
            // Mise à jour des ingrédients
            // D'abord supprimer les associations existantes
            $stmtDeleteIngredients = $pdo->prepare("DELETE FROM liste_recette_ingredients WHERE id_recette = :id_recette");
            $stmtDeleteIngredients->bindParam(':id_recette', $id, PDO::PARAM_INT);
            $stmtDeleteIngredients->execute();
            
            // Puis ajouter les nouvelles associations
            if (isset($_POST['ingredient_id']) && is_array($_POST['ingredient_id'])) {
                $stmtInsertIngredient = $pdo->prepare("INSERT INTO liste_recette_ingredients (id_recette, id_ingredient, quantite, id_unite) VALUES (:id_recette, :id_ingredient, :quantite, :id_unite)");
                
                for ($i = 0; $i < count($_POST['ingredient_id']); $i++) {
                    if (!empty($_POST['ingredient_id'][$i]) && !empty($_POST['quantite'][$i])) {
                        $ingredientId = intval($_POST['ingredient_id'][$i]);
                        $quantite = floatval($_POST['quantite'][$i]);
                        $uniteId = !empty($_POST['unite_id'][$i]) ? intval($_POST['unite_id'][$i]) : null;
                        
                        $stmtInsertIngredient->bindParam(':id_recette', $id, PDO::PARAM_INT);
                        $stmtInsertIngredient->bindParam(':id_ingredient', $ingredientId, PDO::PARAM_INT);
                        $stmtInsertIngredient->bindParam(':quantite', $quantite);
                        $stmtInsertIngredient->bindParam(':id_unite', $uniteId, PDO::PARAM_INT);
                        $stmtInsertIngredient->execute();
                    }
                }
            }
            
            // Enregistrer l'action dans le journal
            $stmtJournal = $pdo->prepare("INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)");
            $table = 'recette';
            $actionJournal = 'modification';
            $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
            $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
            $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
            $stmtJournal->execute();
            
            header('Location: manageRecettes.php?info=Recette mise à jour avec succès.');
            exit();
        } else {
            $info = alert("Erreur lors de la mise à jour de la recette.", "error");
        }
    }
}

// Traitement de l'ajout d'une recette
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $descriptif = htmlspecialchars(trim($_POST['descriptif']));
    $instructions = htmlspecialchars(trim($_POST['instructions']));
    $temps_preparation = intval($_POST['temps_preparation']);
    $temps_cuisson = intval($_POST['temps_cuisson']);
    $difficulte = htmlspecialchars(trim($_POST['difficulte']));
    $id_categorie = intval($_POST['id_categorie']);

    // Gestion de l'image téléchargée
    $image_url = null;
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp']; // Extensions autorisées
        $filename = $_FILES['image_url']['name']; // Nom du fichier
        $tmp = explode('.', $filename); // explode divise le nom du fichier en un tableau
        $ext = strtolower(end($tmp)); // end est une fonction qui retourne le dernier élément d'un tableau

        // Vérification de l'extension
        if (in_array($ext, $allowed)) {
            $newFilename = 'recette_' . time() . '.' . $ext; // time() génère un timestamp
            $uploadDir = '../../public/assets/recettes/images/';

            if (!is_dir($uploadDir)) { // Vérifie si le répertoire existe
                mkdir($uploadDir, 0777, true); // mkdir crée le répertoire s'il n'existe pas
            }

            $destination = $uploadDir . $newFilename; // Chemin de destination

            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $destination)) {
                $image_url = 'images/' . $newFilename;
            }
        }
    }

    // Vérification des champs
    if (empty($nom) || empty($descriptif) || empty($instructions)) {
        $info = alert("Veuillez remplir tous les champs obligatoires.", "error");
    } else {
        $pdo = connexionBDD();

        // Insertion de la recette
        $stmt = $pdo->prepare("INSERT INTO recette (id_admin, nom, descriptif, instructions, image_url, 
                               temps_preparation, temps_cuisson, difficulte, id_categorie) 
                               VALUES (:id_admin, :nom, :descriptif, :instructions, :image_url, 
                               :temps_preparation, :temps_cuisson, :difficulte, :id_categorie)");

        $stmt->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':descriptif', $descriptif);
        $stmt->bindParam(':instructions', $instructions);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':temps_preparation', $temps_preparation, PDO::PARAM_INT);
        $stmt->bindParam(':temps_cuisson', $temps_cuisson, PDO::PARAM_INT);
        $stmt->bindParam(':difficulte', $difficulte);
        $stmt->bindParam(':id_categorie', $id_categorie, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $recetteId = $pdo->lastInsertId();

            // Ajout des étiquettes
            if (isset($_POST['etiquettes']) && is_array($_POST['etiquettes'])) {
                $stmtInsertEtiquette = $pdo->prepare("INSERT INTO recette_etiquette (id_recette, id_etiquette) VALUES (:id_recette, :id_etiquette)");

                foreach ($_POST['etiquettes'] as $etiquetteId) {
                    $stmtInsertEtiquette->bindParam(':id_recette', $recetteId, PDO::PARAM_INT);
                    $stmtInsertEtiquette->bindParam(':id_etiquette', $etiquetteId, PDO::PARAM_INT);
                    $stmtInsertEtiquette->execute();
                }
            }

            // Ajout des ingrédients
            if (isset($_POST['ingredient_id']) && is_array($_POST['ingredient_id'])) {
                $stmtInsertIngredient = $pdo->prepare("INSERT INTO liste_recette_ingredients (id_recette, id_ingredient, quantite, id_unite) VALUES (:id_recette, :id_ingredient, :quantite, :id_unite)");

                for ($i = 0; $i < count($_POST['ingredient_id']); $i++) {
                    if (!empty($_POST['ingredient_id'][$i]) && !empty($_POST['quantite'][$i])) {
                        $ingredientId = intval($_POST['ingredient_id'][$i]);
                        $quantite = floatval($_POST['quantite'][$i]);
                        $uniteId = !empty($_POST['unite_id'][$i]) ? intval($_POST['unite_id'][$i]) : null;

                        $stmtInsertIngredient->bindParam(':id_recette', $recetteId, PDO::PARAM_INT);
                        $stmtInsertIngredient->bindParam(':id_ingredient', $ingredientId, PDO::PARAM_INT);
                        $stmtInsertIngredient->bindParam(':quantite', $quantite);
                        $stmtInsertIngredient->bindParam(':id_unite', $uniteId, PDO::PARAM_INT);
                        $stmtInsertIngredient->execute();
                    }
                }
            }

            // Enregistrer l'action dans le journal
            $stmtJournal = $pdo->prepare("INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)");
            $table = 'recette';
            $actionJournal = 'ajout';
            $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
            $stmtJournal->bindParam(':id_element', $recetteId, PDO::PARAM_INT);
            $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
            $stmtJournal->execute();

            header('Location: manageRecettes.php?info=Nouvelle recette ajoutée avec succès.');
            exit();
        } else {
            $info = alert("Erreur lors de l'ajout de la recette.", "error");
        }
    }
}

// Traitement de la suppression d'une recette
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $pdo = connexionBDD();
    $stmt = $pdo->prepare("DELETE FROM recette WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Enregistrer l'action dans le journal
        $stmtJournal = $pdo->prepare("INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)");
        $table = 'recette';
        $actionJournal = 'suppression';
        $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
        $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
        $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
        $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
        $stmtJournal->execute();
        
        header('Location: manageRecettes.php?info=Recette supprimée avec succès.');
        exit();
    } else {
        $info = alert("Erreur lors de la suppression de la recette.", "error");
    }
}

// Récupération de la liste des recettes avec leur catégorie
$pdo = connexionBDD();
$stmt = $pdo->prepare("SELECT r.id, r.nom, r.temps_preparation, r.temps_cuisson, r.difficulte, r.date_creation, 
                       c.nom AS categorie, c.couleur AS couleur_categorie
                       FROM recette r
                       JOIN categorie c ON r.id_categorie = c.id
                       ORDER BY r.date_creation DESC");
$stmt->execute();
$recettes = $stmt->fetchAll();

// Récupération de l'info si elle existe
if (isset($_GET['info'])) {
    $info = alert(htmlspecialchars($_GET['info']), "success");
}

require_once('../headerAdmin.php');
?>
<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>views/admin/dashboard.php">Dashboard</a>
            <p>/</p>
            <p><?= $titlePage ?></p>
        </div>
    </section>
</section>

<section class="admin-section">
    <div class="admin-container">
        <?php if (!empty($info)): ?>
            <?= $info ?>
        <?php endif; ?>

        <?php if (!$recetteEdit): ?>
            <!-- Formulaire d'ajout d'une recette -->
            <div class="add-form-container">
                <h3>Ajouter une nouvelle recette</h3>
                <form method="POST" action="" class="add-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    
                    <!-- Informations générales -->
                    <div class="form-section">
                        <h4>Informations générales</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de la recette *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="id_categorie" class="form-label">Catégorie *</label>
                                <select class="form-control" id="id_categorie" name="id_categorie" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?= $categorie['id'] ?>"><?= htmlspecialchars($categorie['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="temps_preparation" class="form-label">Temps de préparation (minutes) *</label>
                                <input type="number" class="form-control" id="temps_preparation" name="temps_preparation" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="temps_cuisson" class="form-label">Temps de cuisson (minutes) *</label>
                                <input type="number" class="form-control" id="temps_cuisson" name="temps_cuisson" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="difficulte" class="form-label">Difficulté *</label>
                                <select class="form-control" id="difficulte" name="difficulte" required>
                                    <option value="facile">Facile</option>
                                    <option value="moyenne">Moyenne</option>
                                    <option value="difficile">Difficile</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="image_url" class="form-label">Image</label>
                                <input type="file" class="form-control" id="image_url" name="image_url">
                                <small>Formats acceptés : JPG, PNG, WEBP</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description et instructions -->
                    <div class="form-section">
                        <h4>Description et instructions</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Description *</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="instructions" class="form-label">Instructions *</label>
                                <textarea class="form-control" id="instructions" name="instructions" rows="6" required></textarea>
                                <small>Utilisez ## pour séparer les étapes (ex: ##Étape 1. ##Étape 2.)</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Étiquettes -->
                    <div class="form-section">
                        <h4>Étiquettes</h4>
                        <div class="form-row tags-container">
                            <?php foreach ($etiquettes as $etiquette): ?>
                                <div class="tag-item">
                                    <input type="checkbox" id="etiquette_<?= $etiquette['id'] ?>" name="etiquettes[]" value="<?= $etiquette['id'] ?>">
                                    <label for="etiquette_<?= $etiquette['id'] ?>"><?= htmlspecialchars($etiquette['nom']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Ingrédients -->
                    <div class="form-section">
                        <h4>Ingrédients</h4>
                        <div id="ingredients-container">
                            <div class="ingredient-row">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Ingrédient</label>
                                        <select class="form-control" name="ingredient_id[]">
                                            <option value="">Sélectionner un ingrédient</option>
                                            <?php foreach ($ingredients as $ingredient): ?>
                                                <option value="<?= $ingredient['id'] ?>"><?= htmlspecialchars($ingredient['nom']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Quantité</label>
                                        <input type="number" class="form-control" name="quantite[]" step="0.01" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Unité</label>
                                        <select class="form-control" name="unite_id[]">
                                            <option value="">Sélectionner une unité</option>
                                            <?php foreach ($unites as $unite): ?>
                                                <option value="<?= $unite['id'] ?>"><?= htmlspecialchars($unite['nom']) ?> (<?= htmlspecialchars($unite['abreviation']) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group ingredient-actions">
                                        <button type="button" class="btn-remove-ingredient">Supprimer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-ingredient" class="btn-secondary">Ajouter un ingrédient</button>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter la recette</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'une recette -->
            <div class="edit-form-container">
                <h3>Modifier la recette</h3>
                <form method="POST" action="" class="edit-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $recetteEdit['id'] ?>">
                    
                    <!-- Informations générales -->
                    <div class="form-section">
                        <h4>Informations générales</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de la recette *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($recetteEdit['nom']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="id_categorie" class="form-label">Catégorie *</label>
                                <select class="form-control" id="id_categorie" name="id_categorie" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?= $categorie['id'] ?>" <?= ($categorie['id'] == $recetteEdit['id_categorie']) ? 'selected' : '' ?>><?= htmlspecialchars($categorie['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="temps_preparation" class="form-label">Temps de préparation (minutes) *</label>
                                <input type="number" class="form-control" id="temps_preparation" name="temps_preparation" min="1" value="<?= $recetteEdit['temps_preparation'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="temps_cuisson" class="form-label">Temps de cuisson (minutes) *</label>
                                <input type="number" class="form-control" id="temps_cuisson" name="temps_cuisson" min="0" value="<?= $recetteEdit['temps_cuisson'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="difficulte" class="form-label">Difficulté *</label>
                                <select class="form-control" id="difficulte" name="difficulte" required>
                                    <option value="facile" <?= ($recetteEdit['difficulte'] == 'facile') ? 'selected' : '' ?>>Facile</option>
                                    <option value="moyenne" <?= ($recetteEdit['difficulte'] == 'moyenne') ? 'selected' : '' ?>>Moyenne</option>
                                    <option value="difficile" <?= ($recetteEdit['difficulte'] == 'difficile') ? 'selected' : '' ?>>Difficile</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="image_url" class="form-label">Image</label>
                                <?php if ($recetteEdit['image_url']): ?>
                                    <div class="current-image">
                                        <img src="<?= RACINE_SITE . $recetteEdit['image_url'] ?>" alt="<?= htmlspecialchars($recetteEdit['nom']) ?>" style="max-width: 150px; max-height: 100px;">
                                        <input type="hidden" name="image_url_actuelle" value="<?= $recetteEdit['image_url'] ?>">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image_url" name="image_url">
                                <small>Laissez vide pour conserver l'image actuelle. Formats acceptés : JPG, PNG, WEBP</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description et instructions -->
                    <div class="form-section">
                        <h4>Description et instructions</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Description *</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3" required><?= htmlspecialchars($recetteEdit['descriptif']) ?></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="instructions" class="form-label">Instructions *</label>
                                <textarea class="form-control" id="instructions" name="instructions" rows="6" required><?= htmlspecialchars($recetteEdit['instructions']) ?></textarea>
                                <small>Utilisez ## pour séparer les étapes (ex: ##Étape 1. ##Étape 2.)</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Étiquettes -->
                    <div class="form-section">
                        <h4>Étiquettes</h4>
                        <div class="form-row tags-container">
                            <?php foreach ($etiquettes as $etiquette): ?>
                                <div class="tag-item">
                                    <input type="checkbox" id="etiquette_<?= $etiquette['id'] ?>" name="etiquettes[]" value="<?= $etiquette['id'] ?>" 
                                        <?= in_array($etiquette['id'], $recetteEtiquettesData ?? []) ? 'checked' : '' ?>>
                                    <label for="etiquette_<?= $etiquette['id'] ?>"><?= htmlspecialchars($etiquette['nom']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Ingrédients -->
                    <div class="form-section">
                        <h4>Ingrédients</h4>
                        <div id="ingredients-container">
                            <?php if (isset($recetteIngredientsData) && !empty($recetteIngredientsData)): ?>
                                <?php foreach ($recetteIngredientsData as $ingredient): ?>
                                    <div class="ingredient-row">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="form-label">Ingrédient</label>
                                                <select class="form-control" name="ingredient_id[]">
                                                    <option value="">Sélectionner un ingrédient</option>
                                                    <?php foreach ($ingredients as $ing): ?>
                                                        <option value="<?= $ing['id'] ?>" <?= ($ing['id'] == $ingredient['id_ingredient']) ? 'selected' : '' ?>><?= htmlspecialchars($ing['nom']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Quantité</label>
                                                <input type="number" class="form-control" name="quantite[]" step="0.01" min="0" value="<?= $ingredient['quantite'] ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Unité</label>
                                                <select class="form-control" name="unite_id[]">
                                                    <option value="">Sélectionner une unité</option>
                                                    <?php foreach ($unites as $unite): ?>
                                                        <option value="<?= $unite['id'] ?>" <?= ($unite['id'] == $ingredient['id_unite']) ? 'selected' : '' ?>><?= htmlspecialchars($unite['nom']) ?> (<?= htmlspecialchars($unite['abreviation']) ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group ingredient-actions">
                                                <button type="button" class="btn-remove-ingredient">Supprimer</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="ingredient-row">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Ingrédient</label>
                                            <select class="form-control" name="ingredient_id[]">
                                                <option value="">Sélectionner un ingrédient</option>
                                                <?php foreach ($ingredients as $ingredient): ?>
                                                    <option value="<?= $ingredient['id'] ?>"><?= htmlspecialchars($ingredient['nom']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Quantité</label>
                                            <input type="number" class="form-control" name="quantite[]" step="0.01" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Unité</label>
                                            <select class="form-control" name="unite_id[]">
                                                <option value="">Sélectionner une unité</option>
                                                <?php foreach ($unites as $unite): ?>
                                                    <option value="<?= $unite['id'] ?>"><?= htmlspecialchars($unite['nom']) ?> (<?= htmlspecialchars($unite['abreviation']) ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group ingredient-actions">
                                            <button type="button" class="btn-remove-ingredient">Supprimer</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-ingredient" class="btn-secondary">Ajouter un ingrédient</button>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour la recette</button>
                        <a href="<?= RACINE_SITE ?>views/admin/manageRecettes.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Tableau des recettes -->
        <div class="recipes-table-container">
            <h3>Liste des recettes</h3>
            <div class="table-responsive">
                <table class="recipes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Durée</th>
                            <th>Difficulté</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recettes)): ?>
                            <tr>
                                <td colspan="7">Aucune recette trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recettes as $recette): ?>
                                <tr>
                                    <td><?= $recette['id'] ?></td>
                                    <td><?= htmlspecialchars($recette['nom']) ?></td>
                                    <td>
                                        <span class="category-badge" style="background-color: <?= htmlspecialchars($recette['couleur_categorie']) ?>">
                                            <?= htmlspecialchars($recette['categorie']) ?>
                                        </span>
                                    </td>
                                    <td><?= $recette['temps_preparation'] + $recette['temps_cuisson'] ?> min</td>
                                    <td>
                                        <span class="difficulty-badge difficulty-<?= $recette['difficulte'] ?>">
                                            <?= ucfirst($recette['difficulte']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($recette['date_creation'])) ?></td>
                                    <td class="actions">
                                        <a href="<?= RACINE_SITE ?>views/recettes/recette.php?id=<?= $recette['id'] ?>" class="btn-view" target="_blank">Voir</a>
                                        <a href="?action=edit&id=<?= $recette['id'] ?>" class="btn-edit">Modifier</a>
                                        <a href="?action=delete&id=<?= $recette['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script src="<?= RACINE_SITE ?>public/assets/javascript/adminRecipe.js"></script>

<?php
require_once('../footerAdmin.php');
?>