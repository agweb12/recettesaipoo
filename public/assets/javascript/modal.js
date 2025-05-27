document.addEventListener('DOMContentLoaded', function() {
    // Éléments du modal
    const modal = document.getElementById('connexionModal');
    const btnModal = document.getElementById('btnModal');
    const closeBtn = document.querySelector('.close');
    const signupLink = document.getElementById('signupLink');

    // Ouvre le modal quand on clique sur le bouton
    btnModal.addEventListener('click', function() {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Empêcher le scroll en arrière-plan
    });

    // Ferme le modal quand on clique sur le X
    closeBtn.addEventListener('click', function() {
        closeModal();
    });

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
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Réactiver le scroll
    }

    // Soumettre le formulaire - À personnaliser selon vos besoins
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Vous pouvez ajouter du code de validation ici si nécessaire
            // Par exemple, vérifier la longueur du mot de passe, etc.
            
            // Pour la démonstration, nous allons juste empêcher la soumission par défaut
            // Décommentez cette ligne pour le développement
            // e.preventDefault(); 
            
            // Vous pouvez également ajouter un spinner pendant la soumission
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner"></span> ' + originalText;
        });
    }
});