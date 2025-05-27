document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un champ de recherche au-dessus du tableau
    const tableContainer = document.querySelector('.etiquettes-table-container');
    const table = document.querySelector('.etiquettes-table');
    
    if (tableContainer && table) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'searchInput';
        searchInput.placeholder = 'Rechercher une étiquette...';
        
        tableContainer.insertBefore(searchInput, table.parentNode);
        
        // Fonction de recherche
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const description = row.cells[2].textContent.toLowerCase();
                if (name.includes(searchValue) || description.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});