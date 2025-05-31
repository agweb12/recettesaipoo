<div class="actions-container">
    <h3>Actions des administrateurs</h3>
    <a href="<?= RACINE_SITE ?>admin/administrateurs" class="btn-secondary btn-back">Retour à la liste des administrateurs</a>

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
                            <td>
                                <span class="action-badge action-<?= htmlspecialchars($action['action']) ?>">
                                    <?= ucfirst(htmlspecialchars($action['action'])) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i:s', strtotime($action['date_action'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>