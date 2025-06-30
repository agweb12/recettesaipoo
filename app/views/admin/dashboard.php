<section class="sectionSiteLinks">
    <section class="breadcrumb">
        <div class="box-breadcrumb">
            <a class="crumb" href="<?= RACINE_SITE ?>admin/dashboard">Dashboard</a>
            <p>/</p>
            <p><?= $titlePage ?></p>
        </div>
    </section>
</section>
<section class="sectionDashboard">
    <div class="box-dashboard">
        <h2>Tableau de bord</h2>
        <p class="alert alert-warning"><b><?= $_SESSION['admin']['prenom']?></b>, tu es bien connecté en tant que <b><?= $_SESSION['admin']['role']?></b></p>
        <p class="alert alert-info">Tu peux gérer les différentes sections du site Recette AI.</p>
        <?php if($_SESSION['admin']['role'] === 'superadmin'): ?>
            <a href="<?= RACINE_SITE ?>admin/administrateurs" class="quick-link">
                    <i class="fi fi-sr-users"></i> Administrateurs
                </a>
                <a href="<?= RACINE_SITE ?>admin/security/stats" class="quick-link">
                    <i class="fi fi-sr-shield-check"></i> Sécurité
                </a>
                <a href="<?= RACINE_SITE ?>admin/analytics" class="quick-link">
                    <i class="fi fi-sr-chart-line"></i> Analyses détaillées
                </a>
        <?php endif; ?>
    </div>
</section>
<!-- Statistiques rapides -->
<section class="quick-stats">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="fi fi-sr-users"></i>
            </div>
            <div class="stat-content">
                <h3>Utilisateurs actifs</h3>
                <span class="stat-number"><?= $analytics['userStats']['users_actifs'] ?></span>
                <small>+<?= $analytics['userStats']['nouveaux_users'] ?> cette semaine</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon recipes">
                <i class="fi fi-sr-book"></i>
            </div>
            <div class="stat-content">
                <h3>Nouvelles recettes</h3>
                <span class="stat-number"><?= $analytics['recipeStats']['nouvelles_recettes'] ?></span>
                <small>Ce mois-ci</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon favorites">
                <i class="fi fi-sr-heart"></i>
            </div>
            <div class="stat-content">
                <h3>Avec favoris</h3>
                <span class="stat-number"><?= $analytics['userStats']['users_avec_favoris'] ?></span>
                <small>Utilisateurs engagés</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon ingredients">
                <i class="fi fi-sr-leaf"></i>
            </div>
            <div class="stat-content">
                <h3>Avec ingrédients</h3>
                <span class="stat-number"><?= $analytics['userStats']['users_avec_ingredients'] ?></span>
                <small>Listes personnelles</small>
            </div>
        </div>
    </div>
</section>

<!-- Graphiques et analyses -->
<section class="analytics-section">
    <div class="analytics-grid">
        <!-- Recettes populaires -->
        <div class="analytics-card">
            <h3><i class="fi fi-sr-crown"></i> Top Recettes</h3>
            <div class="top-recipes">
                <?php foreach(array_slice($analytics['topRecipes'], 0, 5) as $index => $recipe): ?>
                    <div class="recipe-item">
                        <span class="rank">#<?= $index + 1 ?></span>
                        <div class="recipe-info">
                            <strong><?= htmlspecialchars($recipe['nom']) ?></strong>
                            <span class="category" style="background-color: <?= $recipe['couleur_categorie'] ?>">
                                <?= htmlspecialchars($recipe['categorie']) ?>
                            </span>
                        </div>
                        <span class="favorites-count">
                            <i class="fi fi-sr-heart"></i> <?= $recipe['nb_favoris'] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Catégories populaires -->
        <div class="analytics-card">
            <h3><i class="fi fi-sr-chart-pie"></i> Catégories populaires</h3>
            <div class="categories-chart">
                <?php foreach($analytics['popularCategories'] as $category): ?>
                    <div class="category-bar">
                        <div class="category-info">
                            <span class="category-name"><?= htmlspecialchars($category['nom']) ?></span>
                            <span class="category-count"><?= $category['nb_favoris'] ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" style="background-color: <?= $category['couleur'] ?>; width: <?= $category['nb_favoris'] > 0 ? min(100, ($category['nb_favoris'] / max(array_column($analytics['popularCategories'], 'nb_favoris'))) * 100) : 0 ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Ingrédients populaires -->
        <div class="analytics-card">
            <h3><i class="fi fi-sr-carrot"></i> Ingrédients tendance</h3>
            <div class="ingredients-cloud">
                <?php foreach(array_slice($analytics['popularIngredients'], 0, 8) as $ingredient): ?>
                    <span class="ingredient-tag" style="font-size: <?= 0.8 + ($ingredient['nb_utilisations'] / 10) ?>rem">
                        <?= htmlspecialchars($ingredient['nom']) ?>
                        <small>(<?= $ingredient['nb_utilisations'] ?>)</small>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Utilisateurs les plus actifs -->
        <div class="analytics-card">
            <h3><i class="fi fi-sr-star"></i> Utilisateurs actifs</h3>
            <div class="active-users">
                <?php foreach(array_slice($analytics['mostActiveUsers'], 0, 5) as $user): ?>
                    <div class="user-item">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['prenom'], 0, 1)) ?>
                        </div>
                        <div class="user-info">
                            <strong><?= htmlspecialchars($user['prenom'] . ' ' . substr($user['nom'], 0, 1)) ?>.</strong>
                            <small><?= $user['nb_favoris'] ?> favoris • <?= $user['nb_ingredients'] ?> ingrédients</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Section de gestion traditionnelle -->
<section class="totalSection">
    <div class="banner-dashboard">
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/categories">
            <h3>Gérer les Catégories</h3>
            <p class="numberItems"><?= $counters['categories'] ?> <?= ($counters['categories'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/utilisateurs">
            <h3>Gérer les Utilisateurs</h3>
            <p class="numberItems"><?= $counters['utilisateurs'] ?> <?= ($counters['utilisateurs'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <?php if($_SESSION['admin']['role'] === 'superadmin'): ?>
            <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/administrateurs">
                <h3>Gérer les Administrateurs</h3>
                <p class="numberItems"><?= $counters['administrateurs'] ?> <?= ($counters['administrateurs'] > 1) ? 'items' : 'item' ?></p>
            </a>
            <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/security/stats">
                <h3>Gérer la Sécurité</h3>
            </a>
            <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/analytics">
                <h3>Gérer les Analyses</h3>
            </a>
        <?php endif; ?>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/recettes">
            <h3>Gérer les Recettes</h3>
            <p class="numberItems"><?= $counters['recettes'] ?> <?= ($counters['recettes'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/etiquettes">
            <h3>Gérer les Etiquettes</h3>
            <p class="numberItems"><?= $counters['etiquettes'] ?> <?= ($counters['etiquettes'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/unites-mesure">
            <h3>Gérer les Unités de Mesure</h3>
            <p class="numberItems"><?= $counters['unites'] ?> <?= ($counters['unites'] > 1) ? 'items' : 'item' ?></p>
        </a>
        <a class="box-dashboard" href="<?= RACINE_SITE ?>admin/ingredients">
            <h3>Gérer les Ingrédients</h3>
            <p class="numberItems"><?= $counters['ingredients'] ?> <?= ($counters['ingredients'] > 1) ? 'items' : 'item' ?></p>
        </a>
    </div>
</section>

<style>
/* Styles pour les statistiques rapides */
.quick-stats {
    margin: 2rem 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon.users { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.recipes { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-icon.favorites { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-icon.ingredients { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.stat-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    color: #666;
    font-weight: 500;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
    display: block;
}

.stat-content small {
    color: #28a745;
    font-weight: 500;
}

/* Styles pour les analyses */
.analytics-section {
    margin: 3rem 0;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.analytics-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.analytics-card h3 {
    margin: 0 0 1.5rem 0;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Top recettes */
.recipe-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.recipe-item:last-child {
    border-bottom: none;
}

.rank {
    font-weight: bold;
    color: #f39c12;
    min-width: 30px;
}

.recipe-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.category {
    font-size: 0.75rem;
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    align-self: flex-start;
}

.favorites-count {
    color: #e74c3c;
    font-weight: 500;
}

/* Catégories populaires */
.category-bar {
    margin-bottom: 1rem;
}

.category-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.progress-bar {
    background: #f0f0f0;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
}

.progress {
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

/* Ingrédients populaires */
.ingredients-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.ingredient-tag {
    background: #f8f9fa;
    color: #495057;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.ingredient-tag:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

/* Utilisateurs actifs */
.user-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.user-item:last-child {
    border-bottom: none;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.user-info strong {
    display: block;
    margin-bottom: 0.25rem;
}

.user-info small {
    color: #666;
}

/* Liens rapides admin */
.admin-quick-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.quick-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    transition: transform 0.3s ease;
}

.quick-link:hover {
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid,
    .analytics-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-quick-links {
        flex-direction: column;
    }
}
</style>