<?php
require_once('../../inc/functions.php');
$titlePage = "Gestion des Administrateurs";
$descriptionPage = "Gérer les administrateurs de Recette AI";
$indexPage = "noindex";
$followPage = "nofollow";
$keywordsPage = "";
$info = "";

// Vérification des droits d'accès (page réservée aux superadmin et modérateurs)
if (!isset($_SESSION['admin']) || ($_SESSION['admin']['role'] !== 'superadmin')) {
    header('Location: ' . RACINE_SITE . 'views/admin/dashboard.php');
    exit();
}

// Vérification de l'action
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Récupération de l'administrateur à modifier
$adminEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pdo = connexionBDD();
    $sql = "SELECT * FROM administrateur WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $adminEdit = $stmt->fetch();
}

// Traitement de la mise à jour de l'administrateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $motDePasse = htmlspecialchars(trim($_POST['mot_de_passe']));
    $role = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : null;
    $verification = true;

    // Vérification spéciale pour le rôle
    if ($_SESSION['admin']['role'] === 'superadmin' && $id !== $_SESSION['admin']['id']) {
        $roleUpdate = ", role = :role";
    } else {
        $roleUpdate = "";
    }

    // Vérification des champs
    if (empty($nom) || empty($prenom) || empty($email)) {
        $info = alert("Veuillez remplir tous les champs obligatoires.", "error");
        $verification = false;
    } else {
        // Vérification si l'email est valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $info = alert("L'email n'est pas valide.", "error");
            $verification = false;
        } elseif (strlen($email) > 100) {
            $info = alert("L'email ne doit pas dépasser 100 caractères.", "error");
            $verification = false;
        } elseif (strlen($email) < 5) {
            $info = alert("L'email doit contenir au moins 5 caractères.", "error");
            $verification = false;
        }

        //Vérification du prénom
        // regex pour vérifier que le prénom ne contient que des lettres, des espaces et des tirets : internationalisation
        $regexPrenom = "/^\p{L}[\p{L}\s-]*$/u";
        if (!preg_match($regexPrenom, $prenom)) {
            $info = alert("Le prénom ne doit contenir que des lettres, des espaces et des tirets.", "error");
            $verification = false;
        } elseif (strlen($prenom) > 50) {
            $info = alert("Le prénom ne doit pas dépasser 50 caractères.", "error");
            $verification = false;
        } elseif (strlen($prenom) < 2) {
            $info = alert("Le prénom doit contenir au moins 2 caractères.", "error");
            $verification = false;
        }

        // Vérification du nom
        // regex pour vérifier que le nom ne contient que des lettres, des espaces et des tirets : internationalisation
        $regexNom = "/^\p{L}[\p{L}\s-]*$/u";
        if (!preg_match($regexNom, $nom)) {
            $info = alert("Le nom ne doit contenir que des lettres, des espaces et des tirets.", "error");
            $verification = false;
        } elseif (strlen($nom) > 50) {
            $info = alert("Le nom ne doit pas dépasser 50 caractères.", "error");
            $verification = false;
        } elseif (strlen($nom) < 2) {
            $info = alert("Le nom doit contenir au moins 2 caractères.", "error");
            $verification = false;
        }

        if (!empty($motDePasse)) {
            // regex pour vérifier que le mot de passe contient au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
            $regexPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/";
            if (!preg_match($regexPassword, $motDePasse)) {
                $info = alert("Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.", "error");
                $verification = false;
            } elseif (strlen($motDePasse) > 255) {
                $info = alert("Le mot de passe ne doit pas dépasser 255 caractères.", "error");
                $verification = false;
            } elseif (strlen($motDePasse) < 8) {
                $info = alert("Le mot de passe doit contenir au moins 8 caractères.", "error");
                $verification = false;
            }
        }

        if($verification){
            $nom = strtoupper($nom);
            $prenom = ucfirst(strtolower($prenom));
            // Mise à jour de l'administrateur
            $pdo = connexionBDD();
            $sql = "UPDATE administrateur SET nom = :nom, prenom = :prenom, email = :email";

            if(!empty($motDePasse)){
                // Hachage du mot de passe
                $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);
                $sql .= ", mot_de_passe = :motDePasse";
            }

            $sql .= $roleUpdate . " WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);

            if (!empty($motDePasse)) {
                $stmt->bindParam(':motDePasse', $hashedPassword);
            }

            if ($_SESSION['admin']['role'] === 'superadmin' && $id !== $_SESSION['admin']['id'] && $role !== null) {
                $stmt->bindParam(':role', $role);
            }

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
            // Enregistrer l'action dans le journal
                $stmtJournal = $pdo->prepare("INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)");
                $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                $table = 'administrateur';
                $actionJournal = 'modification';
                $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
                $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                $stmtJournal->execute();

                header('Location: manageAdministrateurs.php?info=Administrateur mis à jour avec succès.');
                $info = alert("Administrateur mis à jour avec succès", "success");
                exit();
            } else {
                $info = alert("Erreur lors de la mise à jour de l'administrateur.", "error");
            }

        }
    }
}

// Traitement de la suppression de l'administrateur
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérifier que l'admin n'essaie pas de se supprimer lui-même
    if ($id === $_SESSION['admin']['id']) {
        header('Location: manageAdministrateurs.php?warning=Vous ne pouvez pas supprimer votre propre compte.');
        $info = alert("Vous ne pouvez pas supprimer votre propre compte.", "warning");
        exit();
    }
    
    // Vérifier que l'admin n'est pas un superadmin (seuls les superadmins peuvent supprimer d'autres superadmins)
    $pdo = connexionBDD();
    $stmt = $pdo->prepare("SELECT role FROM administrateur WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $adminToDelete = $stmt->fetch();
    
    if ($adminToDelete['role'] === 'superadmin' && $_SESSION['admin']['role'] !== 'superadmin') {
        header('Location: manageAdministrateurs.php?warning=Vous n\'avez pas les droits pour supprimer un superadmin.');
        $info = alert("Vous n'avez pas les droits de supprimer une super admin.", "warning");
        exit();
    }
    
    // Suppression de l'administrateur
    $sql = "DELETE FROM administrateur WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Enregistrer l'action dans le journal
        $stmtJournal = $pdo->prepare("INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)");
        $table = 'administrateur';
        $actionJournal = 'suppression';
        $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
        $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
        $stmtJournal->bindParam(':id_element', $id, PDO::PARAM_INT);
        $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
        $stmtJournal->execute();

        header('Location: manageAdministrateurs.php?info=Administrateur supprimé avec succès.');
        $info = alert("Administrateur supprimé avec succès", "success");
        exit();
    } else {
        $info = alert("Erreur lors de la suppression de l'administrateur.", "error");
    }
}

// Traitement de l'ajout d'un administrateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $motDePasse = htmlspecialchars(trim($_POST['mot_de_passe']));
    $role = htmlspecialchars(trim($_POST['role']));
    $verification = true;
    // Vérification des champs
    if (empty($nom) || empty($prenom) || empty($email) || empty($motDePasse)) {
        $info = alert("Veuillez remplir tous les champs obligatoires.", "error");
        $verification = false;
    } else {
        // Vérification du mot de passe
        // regex pour vérifier que le mot de passe contient au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
        $regexPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/";
        if (!preg_match($regexPassword, $motDePasse)) {
            $info .= alert("Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.", "error");
            $verification = false;
        } elseif (strlen($motDePasse) > 255) {
            $info .= alert("Le mot de passe ne doit pas dépasser 255 caractères.", "error");
            $verification = false;
        } elseif (strlen($motDePasse) < 8) {
            $info .= alert("Le mot de passe doit contenir au moins 8 caractères.", "error");
            $verification = false;
        }

        // Vérification si l'email est valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $info .= alert("L'email n'est pas valide.", "error");
            $verification = false;
        } elseif (strlen($email) > 100) {
            $info .= alert("L'email ne doit pas dépasser 100 caractères.", "error");
            $verification = false;
        } elseif (strlen($email) < 5) {
            $info .= alert("L'email doit contenir au moins 5 caractères.", "error");
            $verification = false;
        }

        //Vérification du prénom
        // regex pour vérifier que le prénom ne contient que des lettres, des espaces et des tirets : internationalisation
        $regexPrenom = "/^\p{L}[\p{L}\s-]*$/u";
        if (!preg_match($regexPrenom, $prenom)) {
            $info .= alert("Le prénom ne doit contenir que des lettres, des espaces et des tirets.", "error");
            $verification = false;
        } elseif (strlen($prenom) > 50) {
            $info .= alert("Le prénom ne doit pas dépasser 50 caractères.", "error");
            $verification = false;
        } elseif (strlen($prenom) < 2) {
            $info .= alert("Le prénom doit contenir au moins 2 caractères.", "error");
            $verification = false;
        }

        // Vérification du nom
        // regex pour vérifier que le nom ne contient que des lettres, des espaces et des tirets : internationalisation
        $regexNom = "/^\p{L}[\p{L}\s-]*$/u";
        if (!preg_match($regexNom, $nom)) {
            $info .= alert("Le nom ne doit contenir que des lettres, des espaces et des tirets.", "error");
            $verification = false;
        } elseif (strlen($nom) > 50) {
            $info .= alert("Le nom ne doit pas dépasser 50 caractères.", "error");
            $verification = false;
        } elseif (strlen($nom) < 2) {
            $info .= alert("Le nom doit contenir au moins 2 caractères.", "error");
            $verification = false;
        }

        if($verification){
            // Vérifier si l'email existe déjà
            $pdo = connexionBDD();
            $sql = "SELECT COUNT(*) FROM administrateur WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                $info = alert("Cette adresse email est déjà utilisée.", "error");
            } else {
                $nom = strtoupper($nom);
                $prenom = ucfirst(strtolower($prenom));

                $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);
                
                // Insertion du nouvel administrateur
                $sql = "INSERT INTO administrateur (nom, prenom, email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :mot_de_passe, :role)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mot_de_passe', $hashedPassword);
                $stmt->bindParam(':role', $role);
                
                if ($stmt->execute()) {
                    $lastId = $pdo->lastInsertId();
                    
                    // Enregistrer l'action dans le journal
                    $stmtJournal = $pdo->prepare("INSERT INTO administrateur_actions (id_admin, table_modifiee, id_element, action) VALUES (:id_admin, :table_modifiee, :id_element, :action)");
                    $stmtJournal->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
                    $table = 'administrateur';
                    $actionJournal = 'ajout';
                    $stmtJournal->bindParam(':table_modifiee', $table, PDO::PARAM_STR);
                    $stmtJournal->bindParam(':id_element', $lastId, PDO::PARAM_INT);
                    $stmtJournal->bindParam(':action', $actionJournal, PDO::PARAM_STR);
                    $stmtJournal->execute();
                    
                    header('Location: manageAdministrateurs.php?info=Nouvel administrateur ajouté avec succès.');
                    $info = alert("Nouvel Administrateur ajouté avec succès", "success");
                    // exit();
                } else {
                    $info = alert("Erreur lors de l'ajout de l'administrateur.", "error");
                }
            }
        }
    }
}

// récupération de la liste des administrateur_actions
if (isset($_GET['action']) && $_GET['action'] === 'viewActions') {
    $pdo = connexionBDD();
    $sql = "SELECT * FROM administrateur_actions WHERE id_admin = :id_admin ORDER BY date_action DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_admin', $_SESSION['admin']['id'], PDO::PARAM_INT);
    $stmt->execute();
    $actions = $stmt->fetchAll();
}

// Récupération de la liste des administrateurs
$pdo = connexionBDD();
$sql = "SELECT * FROM administrateur ORDER BY nom ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$admins = $stmt->fetchAll();

// Récupération de l'info si elle existe
if (isset($_GET['info'])) {
    $info = alert(htmlspecialchars($_GET['info']), "success");
}

// Récupération de l'erreur si elle existe
if (isset($_GET['warning'])) {
    $info = alert(htmlspecialchars($_GET['warning']), "warning");
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

        <?php if ($_SESSION['admin']['role'] === 'superadmin'): ?>
            <?php if (!$adminEdit): ?>
                <!-- Formulaire d'ajout d'un administrateur -->
                <div class="add-form-container">
                    <h3>Ajouter un nouvel administrateur</h3>
                    <form method="POST" action="" class="add-form">
                        <input type="hidden" name="action" value="create">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" >
                            </div>
                            <div class="form-group">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" >
                            </div>
                            <div class="form-group">
                                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" >
                                <small>Le mot de passe doit contenir au moins 8 caractères</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="role" class="form-label">Rôle</label>
                                <select class="form-control" id="role" name="role" required>
                                    <?php if ($_SESSION['admin']['role'] === 'superadmin'): ?>
                                        <option value="superadmin">Super Administrateur</option>
                                    <?php endif; ?>
                                    <option value="moderateur">Modérateur</option>
                                    <option value="editeur">Éditeur</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Formulaire de modification d'un administrateur -->
                <div class="edit-form-container">
                    <h3>Modifier l'administrateur</h3>
                    <form method="POST" action="" class="edit-form">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $adminEdit['id'] ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($adminEdit['nom']) ?>" >
                            </div>
                            <div class="form-group">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($adminEdit['prenom']) ?>" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($adminEdit['email']) ?>" >
                            </div>
                            <div class="form-group">
                                <label for="mot_de_passe" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                                <small>Le mot de passe doit contenir au moins 8 caractères</small>
                            </div>
                        </div>
                        <?php if ($_SESSION['admin']['role'] === 'superadmin' && $adminEdit['id'] !== $_SESSION['admin']['id']): ?>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="role" class="form-label">Rôle</label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="superadmin" <?= ($adminEdit['role'] === 'superadmin') ? 'selected' : '' ?>>Super Administrateur</option>
                                        <option value="moderateur" <?= ($adminEdit['role'] === 'moderateur') ? 'selected' : '' ?>>Modérateur</option>
                                        <option value="editeur" <?= ($adminEdit['role'] === 'editeur') ? 'selected' : '' ?>>Éditeur</option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Mettre à jour</button>
                            <a href="<?= RACINE_SITE ?>views/admin/manageAdministrateurs.php" class="btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="users-table-container">
            <h3>Liste des administrateurs</h3>
            <?php if ($_SESSION['admin']['role'] === 'superadmin'): ?>
            <div class="table-actions">
                <a href="?action=add" class="btn-primary">Ajouter un administrateur</a>
                <a href="?action=viewActions" class="btn-primary">Voir les actions des administrateurs</a>
            </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($admins)): ?>
                            <tr>
                                <td colspan="7">Aucun administrateur trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?= $admin['id'] ?></td>
                                    <td><?= htmlspecialchars($admin['nom']) ?></td>
                                    <td><?= htmlspecialchars($admin['prenom']) ?></td>
                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                    <td>
                                        <span class="role-badge role-<?= htmlspecialchars($admin['role']) ?>">
                                            <?= ucfirst(htmlspecialchars($admin['role'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($admin['date_inscription'])) ?></td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?= $admin['id'] ?>" class="btn-edit">Modifier</a>
                                        <?php if ($admin['id'] !== $_SESSION['admin']['id'] && 
                                                ($_SESSION['admin']['role'] === 'superadmin' || 
                                                ($_SESSION['admin']['role'] === 'moderateur' && $admin['role'] !== 'superadmin'))): ?>
                                            <a href="?action=delete&id=<?= $admin['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')">Supprimer</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(isset($_GET['action']) && $_GET['action'] === 'viewActions'): ?>
            <h3>Actions des administrateurs</h3>
            <div class="table-responsive">
                <table class="actions-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Administrateur</th>
                            <th>Table modifiée</th>
                            <th>ID de l'élément</th>
                            <th>Action</th>
                            <th>Date de l'action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($actions)): ?>
                            <tr>
                                <td colspan="6">Aucune action trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($actions as $action): ?>
                                <tr>
                                    <td><?= $action['id'] ?></td>
                                    <td><?= htmlspecialchars($action['id_admin']) ?></td>
                                    <td><?= htmlspecialchars($action['table_modifiee']) ?></td>
                                    <td><?= htmlspecialchars($action['id_element']) ?></td>
                                    <td><?= htmlspecialchars($action['action']) ?></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($action['date_action'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
require_once('../footerAdmin.php');
?>