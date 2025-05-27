<?php
require_once('../../inc/functions.php');
$titlePage = "Gestion des Étiquettes";
$descriptionPage = "Gérer les étiquettes disponibles dans Recette AI";
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
$etiquetteEdit = null;

// Chargement d'une étiquette existante pour édition
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Récupérer les détails de l'étiquette
    $pdo = connexionBDD();
    $sql = "SELECT * FROM etiquette WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $etiquetteEdit = $stmt->fetch();
}

// Traitement de la mise à jour d'une étiquette
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $descriptif = htmlspecialchars(trim($_POST['descriptif']));
    
    // Vérification des champs
    if (empty($nom)) {
        $info = alert("Le nom de l'étiquette est obligatoire.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom existe déjà (sauf pour l'étiquette en cours)
        $sql = "SELECT COUNT(*) FROM etiquette WHERE nom = :nom AND id != :id";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Une étiquette avec ce nom existe déjà.", "danger");
        } else {
            // Mise à jour de l'étiquette
            $sql = "UPDATE etiquette SET nom = :nom, descriptif = :descriptif, id_admin = :id_admin WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':descriptif', $descriptif);
            $stmt->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Enregistrer l'action dans le journal
                $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
                $stmtJournal = $pdo->prepare($sql);
                $table = 'etiquette';
                $actionJournal = 'modification';
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();

                header('Location: manageEtiquettes.php?info=Étiquette mise à jour avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de la mise à jour de l'étiquette.", "danger");
            }
        }
    }
}

// Traitement de l'ajout d'une étiquette
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $descriptif = htmlspecialchars(trim($_POST['descriptif']));
    
    // Vérification des champs
    if (empty($nom)) {
        $info = alert("Le nom de l'étiquette est obligatoire.", "danger");
    } else {
        $pdo = connexionBDD();
        
        // Vérifier si le nom existe déjà
        $sql = "SELECT COUNT(*) FROM etiquette WHERE nom = :nom";
        $stmtCheck = $pdo->prepare($sql);
        $stmtCheck->bindParam(':nom', $nom);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            $info = alert("Une étiquette avec ce nom existe déjà.", "danger");
        } else {
            // Insertion de la nouvelle étiquette
            $sql = "INSERT INTO etiquette (nom, descriptif, id_admin) VALUES (:nom, :descriptif, :id_admin)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':descriptif', $descriptif);
            $stmt->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $lastId = $pdo->lastInsertId();
                
                // Enregistrer l'action dans le journal
                $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
                $stmtJournal = $pdo->prepare($sql);
                $table = 'etiquette';
                $actionJournal = 'ajout';
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $lastId, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();
                
                header('Location: manageEtiquettes.php?info=Nouvelle étiquette ajoutée avec succès.');
                exit();
            } else {
                $info = alert("Erreur lors de l'ajout de l'étiquette.", "danger");
            }
        }
    }
}

// Traitement de la suppression d'une étiquette
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérifier si l'étiquette est utilisée dans des recettes
    $pdo = connexionBDD();
    $sql = "SELECT COUNT(*) FROM recette_etiquette WHERE id_etiquette = :id_etiquette";
    $stmtCheck = $pdo->prepare($sql);
    $stmtCheck->bindParam(':id_etiquette', $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->fetchColumn() > 0) {
        header('Location: manageEtiquettes.php?info=Cette étiquette est utilisée dans une ou plusieurs recettes et ne peut pas être supprimée.');
        exit();
    } else {
        // Suppression de l'étiquette
        $stmt = $pdo->prepare("DELETE FROM etiquette WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Enregistrer l'action dans le journal
            $sql = "INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)";
            $stmtJournal = $pdo->prepare($sql);
            $table = 'etiquette';
            $actionJournal = 'suppression';
            $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
            $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
            $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
            $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
            $stmtJournal->execute();
            
            header('Location: manageEtiquettes.php?info=Étiquette supprimée avec succès.');
            exit();
        } else {
            $info = alert("Erreur lors de la suppression de l'étiquette.", "danger");
        }
    }
}


// Récupération de la liste des étiquettes
$pdo = connexionBDD();
$sql = "SELECT e.*, 
        (SELECT COUNT(*) FROM recette_etiquette WHERE id_etiquette = e.id) AS nb_recettes
    FROM etiquette e
    ORDER BY e.nom ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$etiquettes = $stmt->fetchAll();

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

        <?php if (!$etiquetteEdit): ?>
            <!-- Formulaire d'ajout d'une étiquette -->
            <div class="add-form-container">
                <h3>Ajouter une nouvelle étiquette</h3>
                <form method="POST" action="" class="add-form">
                    <input type="hidden" name="action" value="create">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'étiquette *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                                <small>Le nom doit être unique et descriptif (ex: "Sans gluten 🚫🌾")</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Descriptif</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3"></textarea>
                                <small>Une courte description de l'étiquette (ex: "Adapté aux intolérants")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter l'étiquette</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Formulaire de modification d'une étiquette -->
            <div class="edit-form-container">
                <h3>Modifier l'étiquette</h3>
                <form method="POST" action="" class="edit-form">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $etiquetteEdit['id'] ?>">

                    <div class="form-section">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de l'étiquette *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($etiquetteEdit['nom']) ?>" required>
                                <small>Le nom doit être unique et descriptif (ex: "Sans gluten 🚫🌾")</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="descriptif" class="form-label">Descriptif</label>
                                <textarea class="form-control" id="descriptif" name="descriptif" rows="3"><?= htmlspecialchars($etiquetteEdit['descriptif'] ?? '') ?></textarea>
                                <small>Une courte description de l'étiquette (ex: "Adapté aux intolérants")</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour l'étiquette</button>
                        <a href="<?= RACINE_SITE ?>views/admin/manageEtiquettes.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tableau des étiquettes -->
        <div class="etiquettes-table-container">
            <h3>Liste des étiquettes</h3>
            <div class="table-responsive">
                <table class="etiquettes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Descriptif</th>
                            <th>Date de création</th>
                            <th>Utilisée dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($etiquettes)): ?>
                            <tr>
                                <td colspan="6">Aucune étiquette trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($etiquettes as $etiquette): ?>
                                <tr>
                                    <td><?= $etiquette['id'] ?></td>
                                    <td><?= htmlspecialchars($etiquette['nom']) ?></td>
                                    <td><?= htmlspecialchars($etiquette['descriptif'] ?? '') ?></td>
                                    <td><?= date('d/m/Y', strtotime($etiquette['date_creation'])) ?></td>
                                    <td>
                                        <span class="usage-badge" title="Nombre de recettes utilisant cette étiquette">
                                            <?= $etiquette['nb_recettes'] ?> recette(s)
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?= $etiquette['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($etiquette['nb_recettes'] == 0): ?>
                                            <a href="?action=delete&id=<?= $etiquette['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette étiquette ?')">Supprimer</a>
                                        <?php else: ?>
                                            <button class="btn-delete disabled" title="Cette étiquette est utilisée et ne peut pas être supprimée" disabled>Supprimer</button>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminEtiquette.js"></script>

<?php
require_once('../footerAdmin.php');
?>