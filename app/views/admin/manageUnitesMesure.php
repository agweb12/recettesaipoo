<?php
require_once('../../inc/functions.php');
$titlePage = "Gestion des Unités de Mesure";
$descriptionPage = "Gérer les unités de mesure disponibles dans Recette AI";
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
$uniteEdit = null;

// Chargement d'une unité de mesure existante pour édition
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Récupérer les détails de l'unité de mesure
    $pdo = connexionBDD();
    $sql = "SELECT * FROM unite_mesure WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $uniteEdit = $stmt->fetch();
}

// Traitement de la mise à jour d'une unité de mesure
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $abreviation = htmlspecialchars(trim($_POST['abreviation']));
    
    // Vérification des champs
    if (empty($nom) || empty($abreviation)) {
        $info = alert("Tous les champs sont obligatoires.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom ou l'abréviation existe déjà (sauf pour l'unité en cours)
        $sql = "SELECT COUNT(*) FROM unite_mesure WHERE (nom = :nom OR abreviation = :abreviation) AND id != :id";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->bindParam(':abreviation', $abreviation);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Une unité de mesure avec ce nom ou cette abréviation existe déjà.", "danger");
        } else {
            // Mise à jour de l'unité de mesure
            $sql = "UPDATE unite_mesure SET nom = :nom, abreviation = :abreviation WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':abreviation', $abreviation);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                header('Location: manageUnitesMesure.php?info=Unité de mesure mise à jour avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de la mise à jour de l'unité de mesure.", "danger");
            }
        }
    }
}

// Traitement de l'ajout d'une unité de mesure
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $abreviation = htmlspecialchars(trim($_POST['abreviation']));
    
    // Vérification des champs
    if (empty($nom) || empty($abreviation)) {
        $info = alert("Tous les champs sont obligatoires.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom ou l'abréviation existe déjà
        $sql = "SELECT COUNT(*) FROM unite_mesure WHERE nom = :nom OR abreviation = :abreviation";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->bindParam(':abreviation', $abreviation);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Une unité de mesure avec ce nom ou cette abréviation existe déjà.", "danger");
        } else {
            // Insertion de la nouvelle unité de mesure
            $sql = "INSERT INTO unite_mesure (nom, abreviation) VALUES (:nom, :abreviation)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':abreviation', $abreviation);
            
            if ($stmt->execute()) {
                $lastId = $pdo->lastInsertId();

                header('Location: manageUnitesMesure.php?info=Nouvelle unité de mesure ajoutée avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de l'ajout de l'unité de mesure.", "danger");
            }
        }
    }
}

// Traitement de la suppression d'une unité de mesure
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérifier si l'unité est utilisée dans des recettes
    $pdo = connexionBDD();
    $sql = "SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_unite = :id_unite";
    $stmtCheck = $pdo->prepare($sql);
    $stmtCheck->bindParam(':id_unite', $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->fetchColumn() > 0) {
        header('Location: manageUnitesMesure.php?info=Cette unité de mesure est utilisée dans des recettes et ne peut pas être supprimée.');
        exit();
    } else {
        // Suppression de l'unité de mesure
        $sql = "DELETE FROM unite_mesure WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {            
            header('Location: manageUnitesMesure.php?info=Unité de mesure supprimée avec succès.');
            exit();
        } else {
            $info = alert("Erreur lors de la suppression de l'unité de mesure.", "danger");
        }
    }
}

// Récupération de la liste des unités de mesure avec le nombre de recettes qui les utilisent
$pdo = connexionBDD();
$sql = "SELECT um.*, 
        (SELECT COUNT(*) FROM liste_recette_ingredients WHERE id_unite = um.id) AS nb_recettes
        FROM unite_mesure um
        ORDER BY um.nom ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$unites = $stmt->fetchAll();

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

        <?php if (!$uniteEdit): ?>
            <!-- Formulaire d'ajout d'une unité de mesure -->
            <div class="add-form-container">
                <h3>Ajouter une nouvelle unité de mesure</h3>
                <form method="POST" action="" class="add-form">
                    <input type="hidden" name="action" value="create">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'unité *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                                <small>Ex: gramme, litre, cuillère à soupe, etc.</small>
                            </div>
                            <div class="form-group">
                                <label for="abreviation" class="form-label">Abréviation *</label>
                                <input type="text" class="form-control" id="abreviation" name="abreviation" maxlength="10" required>
                                <small>Ex: g, L, cas, etc.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter l'unité de mesure</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'une unité de mesure -->
            <div class="edit-form-container">
                <h3>Modifier l'unité de mesure</h3>
                <form method="POST" action="" class="edit-form">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $uniteEdit['id'] ?>">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'unité *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($uniteEdit['nom']) ?>" required>
                                <small>Ex: gramme, litre, cuillère à soupe, etc.</small>
                            </div>
                            <div class="form-group">
                                <label for="abreviation" class="form-label">Abréviation *</label>
                                <input type="text" class="form-control" id="abreviation" name="abreviation" maxlength="10" value="<?= htmlspecialchars($uniteEdit['abreviation']) ?>" required>
                                <small>Ex: g, L, cas, etc.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour l'unité de mesure</button>
                        <a href="<?= RACINE_SITE ?>views/admin/manageUnitesMesure.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tableau des unités de mesure -->
        <div class="unites-table-container">
            <h3>Liste des unités de mesure</h3>
            <div class="table-responsive">
                <table class="unites-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Abréviation</th>
                            <th>Utilisée dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($unites)): ?>
                            <tr>
                                <td colspan="5">Aucune unité de mesure trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($unites as $unite): ?>
                                <tr>
                                    <td><?= $unite['id'] ?></td>
                                    <td><?= htmlspecialchars($unite['nom']) ?></td>
                                    <td><?= htmlspecialchars($unite['abreviation']) ?></td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cette unité">
                                            <?= $unite['nb_recettes'] ?> recette(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?= $unite['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($unite['nb_recettes'] == 0): ?>
                                            <a href="?action=delete&id=<?= $unite['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette unité de mesure ?')">Supprimer</a>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cette unité est utilisée dans des recettes et ne peut pas être supprimée" disabled>Supprimer</button>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminUniteMesure.js"></script>

<?php
require_once('../footerAdmin.php');
?>