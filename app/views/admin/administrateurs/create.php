<div class="add-form-container">
    <h3>Ajouter un nouvel administrateur</h3>
    <form method="POST" action="<?= RACINE_SITE ?>admin/administrateurs/store" class="add-form">
        <div class="form-row">
            <div class="form-group">
                <label for="nom" class="form-label">Nom *</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom" class="form-label">Prénom *</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email" class="form-label">Email *</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                <small>Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial</small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="role" class="form-label">Rôle *</label>
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
            <a href="<?= RACINE_SITE ?>admin/administrateurs" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>