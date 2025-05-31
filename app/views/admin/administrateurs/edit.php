<div class="edit-form-container">
    <h3>Modifier l'administrateur</h3>
    <form method="POST" action="<?= RACINE_SITE ?>admin/administrateurs/update/<?= $adminEdit['id'] ?>" class="edit-form">
        <div class="form-row">
            <div class="form-group">
                <label for="nom" class="form-label">Nom *</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($adminEdit['nom']) ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom" class="form-label">Prénom *</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($adminEdit['prenom']) ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email" class="form-label">Email *</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($adminEdit['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                <small>Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial</small>
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
            <a href="<?= RACINE_SITE ?>admin/administrateurs" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>