<div class="admins-table-container">
    <h3>Liste des administrateurs</h3>
    <div class="table-responsive">
        <table class="admins-table">
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
                                <a href="<?= RACINE_SITE ?>admin/administrateurs/edit/<?= $admin['id'] ?>" class="btn-edit">Modifier</a>
                                <?php if ($admin['id'] !== $_SESSION['admin']['id'] && 
                                         ($_SESSION['admin']['role'] === 'superadmin' || 
                                         ($_SESSION['admin']['role'] === 'moderateur' && $admin['role'] !== 'superadmin'))): ?>
                                    <form method="POST" action="<?= RACINE_SITE ?>admin/administrateurs/delete/<?= $admin['id'] ?>" class="delete-form" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')">Supprimer</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>