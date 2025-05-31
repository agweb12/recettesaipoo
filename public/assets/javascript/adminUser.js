document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un champ de recherche au-dessus du tableau
    const tableContainer = document.querySelector('.users-table-container');
    const table = document.querySelector('.users-table');
    
    if (tableContainer && table) {
        // Vérifier si le champ de recherche existe déjà
        let searchInput = document.getElementById('searchInput');
        
        if (!searchInput) {
            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.id = 'searchInput';
            searchInput.className = 'search-input';
            searchInput.placeholder = 'Rechercher un utilisateur...';
            
            // Insérer avant le tableau mais après le titre
            const tableTitle = tableContainer.querySelector('h3');
            if (tableTitle) {
                tableTitle.insertAdjacentElement('afterend', searchInput);
            } else {
                tableContainer.insertBefore(searchInput, table.parentNode);
            }
        }
        
        // Fonction de recherche
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const nom = row.cells[1].textContent.toLowerCase();
                const prenom = row.cells[2].textContent.toLowerCase();
                const email = row.cells[3].textContent.toLowerCase();
                
                if (nom.includes(searchValue) || prenom.includes(searchValue) || email.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});