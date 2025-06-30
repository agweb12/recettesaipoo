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
        <div class="security-stats-header">
            <h2><i class="fi fi-sr-shield-check"></i> Statistiques de Sécurité</h2>
            <p>Surveillance des tentatives de connexion des dernières 24 heures</p>
        </div>

        <?php if (!empty($securityStats)): ?>
            <div class="security-stats-grid">
                <div class="stats-card">
                    <h3>Total des IP surveillées</h3>
                    <span class="stat-number"><?= count($securityStats) ?></span>
                </div>
                
                <div class="stats-card danger">
                    <h3>Tentatives échouées</h3>
                    <span class="stat-number">
                        <?= array_sum(array_column($securityStats, 'failed_attempts')) ?>
                    </span>
                </div>
                
                <div class="stats-card warning">
                    <h3>Tentatives admin</h3>
                    <span class="stat-number">
                        <?= count(array_filter($securityStats, fn($stat) => $stat['user_type'] === 'admin')) ?>
                    </span>
                </div>
                
                <div class="stats-card success">
                    <h3>Tentatives utilisateur</h3>
                    <span class="stat-number">
                        <?= count(array_filter($securityStats, fn($stat) => $stat['user_type'] === 'user')) ?>
                    </span>
                </div>
            </div>

            <div class="security-table-container">
                <h3>Détail des tentatives par IP</h3>
                <div class="table-responsive">
                    <table class="security-table">
                        <thead>
                            <tr>
                                <th>Adresse IP</th>
                                <th>Type</th>
                                <th>Total tentatives</th>
                                <th>Échecs</th>
                                <th>Dernière tentative</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($securityStats as $stat): ?>
                                <tr class="<?= $stat['failed_attempts'] > 3 ? 'high-risk' : ($stat['failed_attempts'] > 1 ? 'medium-risk' : 'low-risk') ?>">
                                    <td>
                                        <code><?= htmlspecialchars($stat['ip_address']) ?></code>
                                    </td>
                                    <td>
                                        <span class="user-type-badge <?= $stat['user_type'] ?>">
                                            <?= $stat['user_type'] === 'admin' ? 'Admin' : 'Utilisateur' ?>
                                        </span>
                                    </td>
                                    <td><?= $stat['attempts'] ?></td>
                                    <td>
                                        <span class="failed-attempts"><?= $stat['failed_attempts'] ?></span>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i:s', strtotime($stat['last_attempt'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($stat['failed_attempts'] > 5): ?>
                                            <span class="status-badge critical">Critique</span>
                                        <?php elseif ($stat['failed_attempts'] > 3): ?>
                                            <span class="status-badge warning">Suspect</span>
                                        <?php else: ?>
                                            <span class="status-badge normal">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($stat['failed_attempts'] > 3): ?>
                                            <button class="btn-small btn-danger" onclick="blockIP('<?= $stat['ip_address'] ?>')">
                                                Bloquer
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn-small btn-info" onclick="viewIPDetails('<?= $stat['ip_address'] ?>')">
                                            Détails
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="no-stats">
                <i class="fi fi-sr-shield-check"></i>
                <h3>Aucune tentative de connexion détectée</h3>
                <p>Aucune activité suspecte dans les dernières 24 heures.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.security-stats-header {
    text-align: center;
    margin-bottom: 2rem;
}

.security-stats-header h2 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.security-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    border-left: 4px solid #3498db;
}

.stats-card.danger {
    border-left-color: #e74c3c;
}

.stats-card.warning {
    border-left-color: #f39c12;
}

.stats-card.success {
    border-left-color: #27ae60;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
    display: block;
    margin-top: 0.5rem;
}

.security-table-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.security-table {
    width: 100%;
    border-collapse: collapse;
}

.security-table th,
.security-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.security-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.high-risk {
    background-color: #ffeaea;
}

.medium-risk {
    background-color: #fff3cd;
}

.low-risk {
    background-color: #f8f9fa;
}

.user-type-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
}

.user-type-badge.admin {
    background-color: #dc3545;
    color: white;
}

.user-type-badge.user {
    background-color: #28a745;
    color: white;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-badge.critical {
    background-color: #dc3545;
    color: white;
}

.status-badge.warning {
    background-color: #ffc107;
    color: #212529;
}

.status-badge.normal {
    background-color: #28a745;
    color: white;
}

.btn-small {
    padding: 0.25rem 0.5rem;
    font-size: 0.85rem;
    margin-right: 0.5rem;
}

.no-stats {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.no-stats i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #28a745;
}
</style>

<script>
function blockIP(ip) {
    if (confirm(`Êtes-vous sûr de vouloir bloquer l'IP ${ip} ?`)) {
        // Implémentation du blocage d'IP
        console.log(`Blocage de l'IP: ${ip}`);
        alert('Fonctionnalité de blocage à implémenter');
    }
}

function viewIPDetails(ip) {
    // Afficher les détails d'une IP
    console.log(`Détails pour l'IP: ${ip}`);
    alert(`Détails pour l'IP: ${ip}\n(Fonctionnalité à implémenter)`);
}
</script>