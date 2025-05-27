document.addEventListener('DOMContentLoaded', function() {
    const passwordToggle = document.querySelector('.password-toggle');
    const passwordInput = document.getElementById('password');
    // Fonction pour afficher/masquer le mot de passe
    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fi-sr-eye');
                passwordToggle.classList.add('fi-sr-eye-crossed');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fi-sr-eye-crossed');
                passwordToggle.classList.add('fi-sr-eye');
            }
        });
    }
});