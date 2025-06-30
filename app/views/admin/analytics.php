<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>admin/dashboard">Dashboard</a>
            <p>/</p>
            <p><?= $titlePage ?></p>
        </div>
    </section>
</section>

<section class="analytics-page">
    <div class="analytics-header">
        <h1><i class="fi fi-sr-chart-line"></i> Analyses & Statistiques</h1>
        <p>Données détaillées sur l'utilisation de Recettes AI</p>
    </div>

    <!-- Graphiques principaux -->
    <div class="charts-section">
        <div class="chart-container">
            <h3>Croissance des utilisateurs</h3>
            <canvas id="userGrowthChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3>Tendance des favoris</h3>
            <canvas id="favoritesChart"></canvas>
        </div>
    </div>

    <!-- Tableaux détaillés -->
    <div class="tables-section">
        <!-- Top 20 recettes -->
        <div class="analytics-table">
            <h3><i class="fi fi-sr-crown"></i> Top 20 Recettes</h3>
            <div class="table-responsive">
                <table class="top-recipes-table">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Recette</th>
                            <th>Catégorie</th>
                            <th>Favoris</th>
                            <th>Date d'ajout</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($analytics['topRecipes'] as $index => $recipe): ?>
                            <tr>
                                <td><span class="rank-badge">#<?= $index + 1 ?></span></td>
                                <td>
                                    <div class="recipe-cell">
                                        <strong><?= htmlspecialchars($recipe['nom']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge" style="background-color: <?= $recipe['couleur_categorie'] ?>">
                                        <?= htmlspecialchars($recipe['categorie']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="favorites-badge">
                                        <i class="fi fi-sr-heart"></i> <?= $recipe['nb_favoris'] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($recipe['date_creation'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistiques par difficulté -->
        <div class="analytics-card">
            <h3><i class="fi fi-sr-stats"></i> Répartition par difficulté</h3>
            <div class="difficulty-stats">
                <?php foreach($analytics['recipeStats']['par_difficulte'] as $stat): ?>
                    <div class="difficulty-item">
                        <span class="difficulty-badge difficulty-<?= $stat['difficulte'] ?>">
                            <?= ucfirst($stat['difficulte']) ?>
                        </span>
                        <div class="difficulty-bar">
                            <div class="difficulty-progress" style="width: <?= ($stat['nb_recettes'] / max(array_column($analytics['recipeStats']['par_difficulte'], 'nb_recettes'))) * 100 ?>%"></div>
                        </div>
                        <span class="difficulty-count"><?= $stat['nb_recettes'] ?> recettes</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Étiquettes populaires -->
        <div class="analytics-card">
            <h3><i class="fi fi-sr-tags"></i> Étiquettes les plus utilisées</h3>
            <div class="tags-grid">
                <?php foreach($analytics['popularTags'] as $tag): ?>
                    <div class="tag-item">
                        <span class="tag-name"><?= htmlspecialchars($tag['nom']) ?></span>
                        <span class="tag-count"><?= $tag['nb_utilisations'] ?> fois</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<style>
.analytics-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.analytics-header {
    text-align: center;
    margin-bottom: 3rem;
}

.analytics-header h1 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.charts-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.chart-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.chart-container h3 {
    margin: 0 0 1rem 0;
    color: #2c3e50;
}

.tables-section {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 2rem;
}

.analytics-table,
.analytics-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.analytics-table h3,
.analytics-card h3 {
    margin: 0 0 1.5rem 0;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.top-recipes-table {
    width: 100%;
    border-collapse: collapse;
}

.top-recipes-table th,
.top-recipes-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.top-recipes-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.rank-badge {
    background: #f39c12;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.85rem;
}

.category-badge {
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
}

.favorites-badge {
    color: #e74c3c;
    font-weight: 500;
}

/* Styles pour les difficultés */
.difficulty-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.difficulty-item {
    display: grid;
    grid-template-columns: 100px 1fr 80px;
    align-items: center;
    gap: 1rem;
}

.difficulty-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
    text-align: center;
}

.difficulty-badge.difficulty-facile {
    background-color: #28a745;
    color: white;
}

.difficulty-badge.difficulty-moyenne {
    background-color: #ffc107;
    color: #212529;
}

.difficulty-badge.difficulty-difficile {
    background-color: #dc3545;
    color: white;
}

.difficulty-bar {
    background: #f0f0f0;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
}

.difficulty-progress {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.difficulty-count {
    font-size: 0.9rem;
    color: #666;
    text-align: right;
}

/* Styles pour les étiquettes */
.tags-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.tag-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.tag-name {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.tag-count {
    color: #666;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .tables-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .difficulty-item {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 0.5rem;
    }
    
    .tags-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique de croissance des utilisateurs
const growthData = <?= json_encode($analytics['growthData']) ?>;
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: growthData.map(d => d.mois),
        datasets: [{
            label: 'Nouveaux utilisateurs',
            data: growthData.map(d => d.nouveaux_users),
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Graphique des tendances favoris
const favoritesData = <?= json_encode($analytics['favoritesTrends']) ?>;
const favoritesCtx = document.getElementById('favoritesChart').getContext('2d');
new Chart(favoritesCtx, {
    type: 'bar',
    data: {
        labels: favoritesData.map(d => 'S' + d.semaine.split('-')[1]),
        datasets: [{
            label: 'Favoris ajoutés',
            data: favoritesData.map(d => d.nb_favoris),
            backgroundColor: 'rgba(231, 76, 60, 0.8)',
            borderColor: '#e74c3c',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>