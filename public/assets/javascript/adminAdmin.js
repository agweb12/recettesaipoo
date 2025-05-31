document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un champ de recherche au-dessus du tableau des administrateurs
    const adminTableContainer = document.querySelector('.admins-table-container');
    if (adminTableContainer) {
        const adminTable = adminTableContainer.querySelector('.admins-table');
        
        // Créer un champ de recherche
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'searchAdmin';
        searchInput.className = 'search-input';
        searchInput.placeholder = 'Rechercher un administrateur...';
        
        // Ajouter le champ de recherche avant le tableau
        adminTableContainer.insertBefore(searchInput, adminTable.parentElement);
        
        // Fonction de recherche
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = adminTable.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const nom = row.cells[1].textContent.toLowerCase();
                const prenom = row.cells[2].textContent.toLowerCase();
                const email = row.cells[3].textContent.toLowerCase();
                const role = row.cells[4].textContent.toLowerCase();
                
                const match = nom.includes(searchTerm) || 
                              prenom.includes(searchTerm) || 
                              email.includes(searchTerm) || 
                              role.includes(searchTerm);
                
                row.style.display = match ? '' : 'none';
            });
        });
    }
    
    // Ajouter un champ de recherche au-dessus du tableau des actions
    const actionsContainer = document.querySelector('.actions-container');
    if (actionsContainer) {
        const actionsTable = actionsContainer.querySelector('.actions-table');
        
        // Créer un champ de recherche
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'searchActions';
        searchInput.className = 'search-input';
        searchInput.placeholder = 'Rechercher une action...';
        
        // Ajouter le champ de recherche après le bouton "Retour"
        const btnBack = actionsContainer.querySelector('.btn-back');
        actionsContainer.insertBefore(searchInput, btnBack.nextSibling);
        
        // Fonction de recherche
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = actionsTable.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const adminId = row.cells[1].textContent.toLowerCase();
                const tableModifiee = row.cells[2].textContent.toLowerCase();
                const action = row.cells[4].textContent.toLowerCase();
                const date = row.cells[5].textContent.toLowerCase();
                
                const match = adminId.includes(searchTerm) || 
                              tableModifiee.includes(searchTerm) || 
                              action.includes(searchTerm) || 
                              date.includes(searchTerm);
                
                row.style.display = match ? '' : 'none';
            });
        });
    }
});