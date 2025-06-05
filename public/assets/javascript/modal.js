document.addEventListener('DOMContentLoaded', function() {
    // Éléments du modal
    const modal = document.getElementById('connexionModal');
    const btnModal = document.getElementById('btnModal');
    const closeBtn = document.querySelector('.close');
    const signupLink = document.getElementById('signupLink');

    // Vérifier que les éléments existent avant d'ajouter des événements
    if (btnModal) {
        btnModal.addEventListener('click', function() {
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        });
    }

    // Ferme le modal quand on clique sur le X
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closeModal();
        });
    }

    // Ferme le modal quand on clique à l'extérieur
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Ferme le modal et redirige vers la page d'inscription
    if (signupLink) {
        signupLink.addEventListener('click', function(e) {
            e.preventDefault();
            closeModal();
            window.location.href = this.href;
        });
    }

    // Fonction pour fermer le modal
    function closeModal() {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Soumettre le formulaire - À personnaliser selon vos besoins
    const loginForm = document.getElementById('modalLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner"></span> ' + originalText;
        });
    }
});