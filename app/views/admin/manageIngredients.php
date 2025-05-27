<?php
require_once('../../inc/functions.php');
$titlePage = "Gestion des Ingrédients";
$descriptionPage = "Gérer les ingrédients disponibles dans Recette AI";
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
$ingredientEdit = null;

// Chargement d'un ingrédient existant pour édition
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Récupérer les détails de l'ingrédient
    $pdo = connexionBDD();
    $sql = "SELECT * FROM ingredient WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $ingredientEdit = $stmt->fetch();
}

// Traitement de la mise à jour d'un ingrédient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    
    // Vérification des champs
    if (empty($nom)) {
        $info = alert("Le nom de l'ingrédient est obligatoire.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom existe déjà (sauf pour l'ingrédient en cours)
        $sql = "SELECT COUNT(*) FROM ingredient WHERE nom = :nom AND id != :id";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Un ingrédient avec ce nom existe déjà.", "danger");
        } else {
            // Mise à jour de l'ingrédient
            $sql = "UPDATE ingredient SET nom = :nom, id_admin = :id_admin WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Enregistrer l'action dans le journal
                $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
                $stmtJournal = $pdo->prepare($sql);
                $table = 'ingredient';
                $actionJournal = 'modification';
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();
                
                header('Location: manageIngredients.php?info=Ingrédient mis à jour avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de la mise à jour de l'ingrédient.", "danger");
            }
        }
    }
}

// Traitement de l'ajout d'un ingrédient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    
    // Vérification des champs
    if (empty($nom)) {
        $info = alert("Le nom de l'ingrédient est obligatoire.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom existe déjà
        $sql = "SELECT COUNT(*) FROM ingredient WHERE nom = :nom";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Un ingrédient avec ce nom existe déjà.", "danger");
        } else {
            // Insertion du nouvel ingrédient
            $sql = "INSERT INTO ingredient (nom, id_admin) VALUES (:nom, :id_admin)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $lastId = $pdo->lastInsertId();
                
                // Enregistrer l'action dans le journal
                $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
                $stmtJournal = $pdo->prepare($sql);
                $table = 'ingredient';
                $actionJournal = 'ajout';
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $lastId, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();
                
                header('Location: manageIngredients.php?info=Nouvel ingrédient ajouté avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de l'ajout de l'ingrédient.", "danger");
            }
        }
    }
}

// Traitement de la suppression d'un ingrédient
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérifier si l'ingrédient est utilisé dans des recettes
    $pdo = connexionBDD();
    $sql = "SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_ingredient = :id_ingredient";
    $stmtCheck = $pdo->prepare($sql);
    $stmtCheck->bindParam(':id_ingredient', $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->fetchColumn() > 0) {
        header('Location: manageIngredients.php?info=Cet ingrédient est utilisé dans une ou plusieurs recettes et ne peut pas être supprimé.');
        exit();
    } else {
        // Vérifier si l'ingrédient est dans des listes personnelles
        $sql = "SELECT COUNT(*) FROM liste_personnelle_ingredients WHERE id_ingredient = :id_ingredient";
        $stmtCheckPersonal = $pdo->prepare($sql);
        $stmtCheckPersonal->bindParam(':id_ingredient', $id, PDO::PARAM_INT);
        $stmtCheckPersonal->execute();
        
        if ($stmtCheckPersonal->fetchColumn() > 0) {
            header('Location: manageIngredients.php?info=Cet ingrédient est présent dans les listes personnelles des utilisateurs et ne peut pas être supprimé.');
            exit();
        } else {
            // Suppression de l'ingrédient
            $stmt = $pdo->prepare("DELETE FROM ingredient WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Enregistrer l'action dans le journal
                $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
                $stmtJournal = $pdo->prepare($sql);
                $table = 'ingredient';
                $actionJournal = 'suppression';
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();
                
                header('Location: manageIngredients.php?info=Ingrédient supprimé avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de la suppression de l'ingrédient.", "danger");
            }
        }
    }
}

// Récupération de la liste des ingrédients
$pdo = connexionBDD();
$sql = "SELECT i.*, 
        (SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_ingredient = i.id) AS nb_recettes,
        (SELECT COUNT(*) FROM liste_personnelle_ingredients WHERE id_ingredient = i.id) AS nb_listes_perso
    FROM ingredient i
    ORDER BY i.nom ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ingredients = $stmt->fetchAll();

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

        <?php if (!$ingredientEdit): ?>
            <!-- Formulaire d'ajout d'un ingrédient -->
            <div class="add-form-container">
                <h3>Ajouter un nouvel ingrédient</h3>
                <form method="POST" action="" class="add-form">
                    <input type="hidden" name="action" value="create">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'ingrédient *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                                <small>Le nom doit être unique et descriptif (ex: "farine de blé" plutôt que "farine")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter l'ingrédient</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'un ingrédient -->
            <div class="edit-form-container">
                <h3>Modifier l'ingrédient</h3>
                <form method="POST" action="" class="edit-form">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $ingredientEdit['id'] ?>">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'ingrédient *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($ingredientEdit['nom']) ?>" required>
                                <small>Le nom doit être unique et descriptif (ex: "farine de blé" plutôt que "farine")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour l'ingrédient</button>
                        <a href="<?= RACINE_SITE ?>views/admin/manageIngredients.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tableau des ingrédients -->
        <div class="ingredients-table-container">
            <h3>Liste des ingrédients</h3>
            <div class="table-responsive">
                <table class="ingredients-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Date de création</th>
                            <th>Utilisé dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ingredients)): ?>
                            <tr>
                                <td colspan="5">Aucun ingrédient trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ingredients as $ingredient): ?>
                                <tr>
                                    <td><?= $ingredient['id'] ?></td>
                                    <td><?= htmlspecialchars($ingredient['nom']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($ingredient['date_creation'])) ?></td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cet ingrédient">
                                            <?= $ingredient['nb_recettes'] ?> recette(s)
                                        </span>
                                        <span class="usage-badge" title="Nombre de listes personnelles contenant cet ingrédient">
                                            <?= $ingredient['nb_listes_perso'] ?> liste(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?= $ingredient['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($ingredient['nb_recettes'] == 0 && $ingredient['nb_listes_perso'] == 0): ?>
                                            <a href="?action=delete&id=<?= $ingredient['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet ingrédient ?')">Supprimer</a>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cet ingrédient est utilisé et ne peut pas être supprimé" disabled>Supprimer</button>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminIngredient.js"></script>

<?php
require_once('../footerAdmin.php');
?>