document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un champ de recherche au-dessus du tableau
    const tableContainer = document.querySelector('.ingredients-table-container');
    const table = document.querySelector('.ingredients-table');
    
    if (tableContainer && table) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'searchInput';
        searchInput.placeholder = 'Rechercher un ingrédient...';
        
        tableContainer.insertBefore(searchInput, table.parentNode);
        
        // Fonction de recherche
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                if (name.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});