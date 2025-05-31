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

        <?php if ($_SESSION['admin']['role'] === 'superadmin'): ?>
            <?php if (!isset($adminEdit) && !isset($isCreating) && !isset($viewActions)): ?>
                <div class="admin-actions">
                    <a href="<?= RACINE_SITE ?>admin/administrateurs/create" class="btn-primary">Ajouter un administrateur</a>
                    <a href="<?= RACINE_SITE ?>admin/administrateurs/view-actions" class="btn-secondary">Voir les actions des administrateurs</a>
                </div>
            <?php endif; ?>

            <?php if (isset($isCreating)): ?>
                <!-- Formulaire d'ajout d'un administrateur -->
                <?php require_once(__DIR__ . '/create.php'); ?>
            <?php elseif (isset($adminEdit)): ?>
                <!-- Formulaire de modification d'un administrateur -->
                <?php require_once(__DIR__ . '/edit.php'); ?>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isset($viewActions) && $viewActions): ?>
            <!-- Table des actions -->
            <?php require_once(__DIR__ . '/actions-table.php'); ?>
        <?php else: ?>
            <!-- Table des administrateurs -->
            <?php require_once(__DIR__ . '/admins-table.php'); ?>
        <?php endif; ?>
    </div>
</section>

<script src="<?= RACINE_SITE ?>public/assets/javascript/adminAdmin.js"></script>