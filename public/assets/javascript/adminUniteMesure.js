document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un champ de recherche au-dessus du tableau
    const tableContainer = document.querySelector('.unites-table-container');
    const table = document.querySelector('.unites-table');
    
    if (tableContainer && table) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'searchInput';
        searchInput.placeholder = 'Rechercher une unité de mesure...';
        
        tableContainer.insertBefore(searchInput, table.parentNode);
        
        // Fonction de recherche
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const abbr = row.cells[2].textContent.toLowerCase();
                if (name.includes(searchValue) || abbr.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});