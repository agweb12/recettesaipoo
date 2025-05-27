<?php
require_once('../../inc/functions.php');
$titlePage = "Gestion des Catégories";
$descriptionPage = "Gérer les catégories de recettes dans Recette AI";
$indexPage = "noindex";
$followPage = "nofollow";
$keywordsPage = "";
$info = "";

// Vérification des droits d'accès (page réservée aux superadmin et modérateurs)
if (!isset($_SESSION['admin']) || ($_SESSION['admin']['role'] !== 'superadmin' && $_SESSION['admin']['role'] !== 'moderateur')) {
    header('Location: ' . RACINE_SITE . 'index.php');
    exit();
}

// Vérification de l'action
$action = isset($_GET['action']) ? $_GET['action'] : '';
$categorieEdit = null;

// Chargement d'une catégorie existante pour édition
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Récupérer les détails de la catégorie
    $pdo = connexionBDD();
    $sql = "SELECT * FROM categorie WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $categorieEdit = $stmt->fetch();
}

// Traitement de la mise à jour d'une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $descriptif = htmlspecialchars(trim($_POST['descriptif']));
    $couleur = htmlspecialchars(trim($_POST['couleur']));
    $couleurTexte = htmlspecialchars(trim($_POST['couleurTexte']));
    
    // Gestion de l'image téléchargée
    $image_url = isset($_POST['image_url_actuelle']) ? $_POST['image_url_actuelle'] : null;
    
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image_url']['name'];
        $tmp = explode('.', $filename);
        $ext = strtolower(end($tmp));
        
        if (in_array($ext, $allowed)) {
            $newFilename = 'categorie_' . $id . '_' . time() . '.' . $ext;
            $uploadDir = '../../public/assets/img/categories/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $destination = $uploadDir . $newFilename;
            
            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $destination)) {
                $image_url = 'public/assets/img/categories/' . $newFilename;
            }
        }
    }
    
    // Vérification des champs
    if (empty($nom)) {
        $info = alert("Le nom de la catégorie est obligatoire.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom existe déjà (sauf pour la catégorie en cours)
        $sql = "SELECT COUNT(*) FROM categorie WHERE nom = :nom AND id != :id";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Une catégorie avec ce nom existe déjà.", "danger");
        } else {
            // Mise à jour de la catégorie
            $sql = "UPDATE categorie SET nom = :nom, descriptif = :descriptif, couleur = :couleur, couleurTexte = :couleurTexte";
            
            if ($image_url) {
                $sql .= ", image_url = :image_url";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':descriptif', $descriptif);
            $stmt->bindParam(':couleur', $couleur);
            $stmt->bindParam(':couleurTexte', $couleurTexte);
            
            if ($image_url) {
                $stmt->bindParam(':image_url', $image_url);
            }
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Enregistrer l'action dans le journal
                $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
                $stmtJournal = $pdo->prepare($sql);
                $table = 'categorie';
                $actionJournal = 'modification';
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();
                
                header('Location: manageCategories.php?info=Catégorie mise à jour avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de la mise à jour de la catégorie.", "danger");
            }
        }
    }
}

// Traitement de l'ajout d'une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $descriptif = htmlspecialchars(trim($_POST['descriptif']));
    $couleur = htmlspecialchars(trim($_POST['couleur']));
    $couleurTexte = htmlspecialchars(trim($_POST['couleurTexte']));
    
    // Gestion de l'image téléchargée
    $image_url = null;
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image_url']['name'];
        $tmp = explode('.', $filename);
        $ext = strtolower(end($tmp));
        
        if (in_array($ext, $allowed)) {
            $newFilename = 'categorie_' . time() . '.' . $ext;
            $uploadDir = '../../public/assets/img/categories/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $destination = $uploadDir . $newFilename;
            
            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $destination)) {
                $image_url = 'public/assets/img/categories/' . $newFilename;
            }
        }
    }
    
    // Vérification des champs
    if (empty($nom)) {
        $info = alert("Le nom de la catégorie est obligatoire.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom existe déjà
        $sql = "SELECT COUNT(*) FROM categorie WHERE nom = :nom";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Une catégorie avec ce nom existe déjà.", "danger");
        } else {
            // Insertion de la nouvelle catégorie
            $sql = "INSERT INTO categorie (nom, descriptif, couleur, image_url, id_admin, couleurTexte) VALUES (:nom, :descriptif, :couleur, :image_url, :id_admin, :couleurTexte)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':descriptif', $descriptif);
            $stmt->bindParam(':couleur', $couleur);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            $stmt->bindParam(':couleurTexte', $couleurTexte);
            
            if ($stmt->execute()) {
                $lastId = $pdo->lastInsertId();
                
                // Enregistrer l'action dans le journal
                $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
                $stmtJournal = $pdo->prepare($sql);
                $table = 'categorie';
                $actionJournal = 'ajout';
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $lastId, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();
                
                header('Location: manageCategories.php?info=Nouvelle catégorie ajoutée avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de l'ajout de la catégorie.", "danger");
            }
        }
    }
}

// Traitement de la suppression d'une catégorie
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérifier si la catégorie est utilisée dans des recettes
    $pdo = connexionBDD();
    $sql = "SELECT COUNT(*) FROM recette WHERE id_categorie = :id_categorie";
    $stmtCheck = $pdo->prepare($sql);
    $stmtCheck->bindParam(':id_categorie', $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->fetchColumn() > 0) {
        header('Location: manageCategories.php?info=Cette catégorie est utilisée dans une ou plusieurs recettes et ne peut pas être supprimée.');
        exit();
    } else {
        // Suppression de la catégorie
        $sql = "DELETE FROM categorie WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Enregistrer l'action dans le journal
            $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
            $stmtJournal = $pdo->prepare($sql);
            $table = 'categorie';
            $actionJournal = 'suppression';
            $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
            $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
            $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
            $stmtJournal->execute();
            
            header('Location: manageCategories.php?info=Catégorie supprimée avec succès.');
            exit();
        } else {
            $info = alert("Erreur lors de la suppression de la catégorie.", "danger");
        }
    }
}

// Récupération de la liste des catégories
$pdo = connexionBDD();
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM recette WHERE id_categorie = c.id) AS nb_recettes
    FROM categorie c
    ORDER BY c.nom ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll();

// Récupération de l'info si elle existe
if (isset($_GET['info'])) {
    $info = alert(htmlspecialchars($_GET['info']), "info");
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

        <?php if (!$categorieEdit): ?>
            <!-- Formulaire d'ajout d'une catégorie -->
            <div class="add-form-container">
                <h3>Ajouter une nouvelle catégorie</h3>
                <form method="POST" action="" class="add-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de la catégorie *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="couleur" class="form-label">Couleur</label>
                                <div class="color-picker-container">
                                    <input type="color" class="form-control color-picker" id="couleur" name="couleur" value="#FFFFFF">
                                    <span class="color-preview" id="color-preview"></span>
                                </div>
                                <small>Cette couleur sera utilisée pour identifier visuellement la catégorie</small>
                            </div>
                            <select name="couleurTexte" id="couleurTexte">
                                <option value="#121212">Texte Noir</option>
                                <option value="#FFFFFF">Texte Blanc</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Description</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="image_url" class="form-label">Image (optionnelle)</label>
                                <input type="file" class="form-control" id="image_url" name="image_url">
                                <small>Formats acceptés : JPG, PNG, WEBP. Taille recommandée : 400x300 pixels</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter la catégorie</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'une catégorie -->
            <div class="edit-form-container">
                <h3>Modifier la catégorie</h3>
                <form method="POST" action="" class="edit-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $categorieEdit['id'] ?>">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de la catégorie *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($categorieEdit['nom']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="couleur" class="form-label">Couleur</label>
                                <div class="color-picker-container">
                                    <input type="color" class="form-control color-picker" id="couleur" name="couleur" value="<?= htmlspecialchars($categorieEdit['couleur']) ?>">
                                    <span class="color-preview" id="color-preview" style="background-color: <?= htmlspecialchars($categorieEdit['couleur']) ?>"></span>
                                </div>
                                <small>Cette couleur sera utilisée pour identifier visuellement la catégorie</small>
                            </div>
                            <select name="couleurTexte" id="couleurTexte">
                                <option value="#121212" <?= $categorieEdit['couleurTexte'] == '#121212' ? 'selected' : '' ?>>Texte Noir</option>
                                <option value="#FFFFFF" <?= $categorieEdit['couleurTexte'] == '#FFFFFF' ? 'selected' : '' ?>>Texte Blanc</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Description</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3"><?= htmlspecialchars($categorieEdit['descriptif']) ?></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="image_url" class="form-label">Image (optionnelle)</label>
                                <?php if ($categorieEdit['image_url']): ?>
                                    <div class="current-image">
                                        <img src="<?= RACINE_SITE . $categorieEdit['image_url'] ?>" alt="<?= htmlspecialchars($categorieEdit['nom']) ?>" style="max-width: 150px; max-height: 100px;">
                                        <input type="hidden" name="image_url_actuelle" value="<?= $categorieEdit['image_url'] ?>">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image_url" name="image_url">
                                <small>Laissez vide pour conserver l'image actuelle. Formats acceptés : JPG, PNG, WEBP</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour la catégorie</button>
                        <a href="<?= RACINE_SITE ?>views/admin/manageCategories.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tableau des catégories -->
        <div class="categories-table-container">
            <h3>Liste des catégories</h3>
            <div class="table-responsive">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Couleur</th>
                            <th>Couleur Texte</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Utilisée dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="7">Aucune catégorie trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $categorie): ?>
                                <tr>
                                    <td><?= $categorie['id'] ?></td>
                                    <td><?= htmlspecialchars($categorie['nom']) ?></td>
                                    <td>
                                        <div class="color-preview-cell" style="background-color: <?= htmlspecialchars($categorie['couleur']) ?>" title="<?= htmlspecialchars($categorie['couleur']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="color-preview-cell" style="background-color: <?= htmlspecialchars($categorie['couleurTexte']) ?>" title="<?= htmlspecialchars($categorie['couleurTexte']) ?>"></div>
                                    </td>
                                    <td class="description-cell">
                                        <?= !empty($categorie['descriptif']) ? htmlspecialchars(substr($categorie['descriptif'], 0, 50)) . (strlen($categorie['descriptif']) > 50 ? '...' : '') : '<em>Aucune description</em>' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($categorie['image_url'])): ?>
                                            <img src="<?= RACINE_SITE . $categorie['image_url'] ?>" alt="<?= htmlspecialchars($categorie['nom']) ?>" class="thumbnail">
                                        <?php else: ?>
                                            <em>Aucune image</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cette catégorie">
                                            <?= $categorie['nb_recettes'] ?> recette(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?= $categorie['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($categorie['nb_recettes'] == 0): ?>
                                            <a href="?action=delete&id=<?= $categorie['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">Supprimer</a>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cette catégorie est utilisée et ne peut pas être supprimée" disabled>Supprimer</button>
                                        <?php endif; ?>
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
<script src="<?= RACINE_SITE ?>public/assets/javascript/adminCategorie.js"></script>
<?php
require_once('../footerAdmin.php');
?>