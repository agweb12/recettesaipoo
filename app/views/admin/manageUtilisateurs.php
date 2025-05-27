<?php
require_once('../../inc/functions.php');
$titlePage = "Gestion des Utilisateurs";
$descriptionPage = "Gérer les utilisateurs de l'application Recette AI.";
$indexPage = "noindex";
$followPage = "nofollow";
$keywordsPage = "";
$info = "";

// Vérification des droits d'accès (page réservée aux administrateurs)
if (!isset($_SESSION['admin'])) {
    header('Location: ' . RACINE_SITE . 'index.php');
    exit();
}

// Vérification de l'action
$action = isset($_GET['action']) ? $_GET['action'] : '';
// Récupération de l'utilisateur à modifier
$utilisateurEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pdo = connexionBDD();
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $utilisateurEdit = $stmt->fetch();
}
// Traitement de la mise à jour de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $motDePasse = htmlspecialchars(trim($_POST['mot_de_passe']));

    // Vérification des champs
    if (empty($nom) || empty($prenom) || empty($email)) {
        $info = alert("Veuillez remplir tous les champs obligatoires.", "error");
    } else {
        // Mise à jour de l'utilisateur
        $pdo = connexionBDD();
        $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email";
        if (!empty($motDePasse)) {
            $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);
            $sql .= ", mot_de_passe = :motDePasse";
        }
        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        if (!empty($motDePasse)) {
            $stmt->bindParam(':motDePasse', $hashedPassword);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            header('Location: manageUtilisateurs.php?info=Utilisateur mis à jour avec succès.');
            exit();
        } else {
            $info = alert("Erreur lors de la mise à jour de l'utilisateur.", "error");
        }
    }
}
// Traitement de la suppression de l'utilisateur
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pdo = connexionBDD();
    $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        header('Location: manageUtilisateurs.php?info=Utilisateur supprimé avec succès.');
        exit();
    } else {
        $info = alert("Erreur lors de la suppression de l'utilisateur.", "error");
    }
}
// Récupération de la liste des utilisateurs
$pdo = connexionBDD();
$stmt = $pdo->prepare("SELECT * FROM utilisateur");
$stmt->execute();
$users = $stmt->fetchAll();
// Récupération de l'info si elle existe
if (isset($_GET['info'])) {
    $info = alert(htmlspecialchars($_GET['info']), "success");
}
// Récupération de l'info si elle existe
if (isset($_GET['error'])) {
    $info = alert(htmlspecialchars($_GET['error']), "error");
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

        <?php if ($utilisateurEdit): ?>
        <div class="edit-form-container">
            <h3>Modifier l'utilisateur</h3>
            <form method="POST" action="" class="edit-form">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $utilisateurEdit['id'] ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($utilisateurEdit['nom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($utilisateurEdit['prenom']) ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($utilisateurEdit['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="motDePasse" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" class="form-control" id="motDePasse" name="motDePasse">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Mettre à jour</button>
                    <a href="<?= RACINE_SITE ?>views/admin/manageUtilisateurs.php" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
        <div class="users-table-container">
            <!-- <h3>Liste des utilisateurs</h3> -->
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8">Aucun utilisateur trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['nom']) ?></td>
                                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?= $user['id'] ?>" class="btn-edit">Modifier</a>
                                        <a href="?action=delete&id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
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
<?php
require_once('../footerAdmin.php');
?>