<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>admin/dashboard">Dashboard</a>
            <p>/</p>
            <p><?= $titlePage ?></p>
        </div>
    </section>
</section>

<section class="admin-section">
    <div class="admin-container">
        <?php if (!empty($info)): ?>
            <?= alert($info, $infoType) ?>
        <?php endif; ?>

        <?php if ($utilisateurEdit): ?>
            <!-- Formulaire de modification d'un utilisateur -->
            <div class="edit-form-container">
                <h3>Modifier l'utilisateur</h3>
                <form method="POST" action="<?= RACINE_SITE ?>admin/utilisateurs/update/<?= $utilisateurEdit['id'] ?>" class="edit-form">
                    <?= csrf_field() ?>
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
                            <label for="mot_de_passe" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mettre à jour</button>
                        <a href="<?= RACINE_SITE ?>admin/utilisateurs" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Tableau des utilisateurs -->
        <div class="users-table-container">
            <h3>Liste des utilisateurs</h3>
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
                                <td colspan="6">Aucun utilisateur trouvé.</td>
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
                                        <a href="<?= RACINE_SITE ?>admin/utilisateurs/edit/<?= $user['id'] ?>" class="btn-edit">Modifier</a>
                                        <form method="POST" action="<?= RACINE_SITE ?>admin/utilisateurs/delete/<?= $user['id'] ?>" class="delete-form" style="display: inline;">
                                            <?= csrf_field()?>
                                            <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                                        </form>
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

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminUsers.js"></script>