document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Ajout des événements click aux boutons d'onglet
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // On retire la classe active de tous les boutons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // On ajoute la classe active au bouton cliqué
            this.classList.add('active');
            
            // On cache tous les contenus
            tabContents.forEach(content => content.classList.remove('active'));
            
            // On affiche le contenu correspondant
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
});