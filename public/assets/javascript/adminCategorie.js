document.addEventListener('DOMContentLoaded', function() {
    // Gestion du color picker
    const colorInput = document.getElementById('couleur');
    const colorPreview = document.getElementById('color-preview');
    
    if (colorInput && colorPreview) {
        // Initialiser la couleur de prévisualisation
        colorPreview.style.backgroundColor = colorInput.value;
        
        // Mettre à jour la prévisualisation quand la couleur change
        colorInput.addEventListener('input', function() {
            colorPreview.style.backgroundColor = this.value;
        });
    }
    
    // Ajouter un champ de recherche au-dessus du tableau
    const tableContainer = document.querySelector('.categories-table-container');
    const table = document.querySelector('.categories-table');
    
    if (tableContainer && table) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.id = 'searchInput';
        searchInput.placeholder = 'Rechercher une catégorie...';
        
        tableContainer.insertBefore(searchInput, table.parentNode);
        
        // Fonction de recherche
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const description = row.cells[3].textContent.toLowerCase();
                if (name.includes(searchValue) || description.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});