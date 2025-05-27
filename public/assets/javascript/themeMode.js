document.addEventListener('DOMContentLoaded', function() {
    const RACINE_SITE = 'http://localhost/recettesaipoo/';
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const imgMenu = document.querySelector('.imgMenu img');
    // Vérifier si un thème est stocké dans localStorage
    const currentTheme = localStorage.getItem('theme') || 'auto'; // Le localstorage va permettre de garder le thème même après le rechargement de la page
    // Mais si je quitte le navigateur et que je reviens, le thème sera celui du système
    // Si le thème est 'auto', on applique le thème du système
    // Si le thème est 'dark', on applique le thème sombre
    // Si le thème est 'light', on applique le thème clair
    
    // Appliquer le thème sauvegardé ou le thème du système par défaut
    applyTheme(currentTheme);
    
    // Fonction pour basculer le thème
    function toggleTheme() {
        if (body.classList.contains('dark')) {
            body.classList.remove('dark');
            body.classList.add('light');
            themeToggle.innerHTML = '<i class="fi fi-sr-sun"></i>';
            // Changer l'image du menu
            imgMenu.src = RACINE_SITE + 'public/assets/img/logo-black.webp';
            localStorage.setItem('theme', 'light');
        } else {
            body.classList.remove('light');
            body.classList.add('dark');
            // Changer l'image du menu
            themeToggle.innerHTML = '<i class="fi fi-sr-moon"></i>';
            imgMenu.src = RACINE_SITE + 'public/assets/img/logo-white.webp';
            localStorage.setItem('theme', 'dark');
        }
    }
    
    // Appliquer le thème initial
    function applyTheme(theme) {
        if (theme === 'dark') {
            body.classList.add('dark');
            themeToggle.innerHTML = '<i class="fi fi-sr-moon"></i>';
            imgMenu.src = RACINE_SITE + 'public/assets/img/logo-white.webp';

        } else if (theme === 'light') {
            body.classList.add('light');
            themeToggle.innerHTML = '<i class="fi fi-sr-sun"></i>';
            imgMenu.src = RACINE_SITE + 'public/assets/img/logo-black.webp';

        } else {
            // Auto - utiliser la préférence du système
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                body.classList.add('dark');
                themeToggle.innerHTML = '<i class="fi fi-sr-moon"></i>';
                imgMenu.src = RACINE_SITE + 'public/assets/img/logo-white.webp';

            } else {
                body.classList.add('light');
                themeToggle.innerHTML = '<i class="fi fi-sr-sun"></i>';
                imgMenu.src = RACINE_SITE + 'public/assets/img/logo-black.webp';
            }
        }
    }
    // Ajouter l'événement de clic au bouton
    themeToggle.addEventListener('click', toggleTheme);
});