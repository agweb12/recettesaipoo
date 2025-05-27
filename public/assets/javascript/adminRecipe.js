document.addEventListener('DOMContentLoaded', function() {
    // Gestion des ingrédients
    const addIngredientBtn = document.getElementById('add-ingredient');
    const ingredientsContainer = document.getElementById('ingredients-container');
    
    if (addIngredientBtn && ingredientsContainer) {
        addIngredientBtn.addEventListener('click', function() {
            const ingredientTemplate = document.querySelector('.ingredient-row').cloneNode(true);
            
            // Réinitialiser les valeurs des champs
            const inputs = ingredientTemplate.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
            
            const selects = ingredientTemplate.querySelectorAll('select');
            selects.forEach(select => select.selectedIndex = 0);
            
            // Ajouter des gestionnaires d'événements au bouton de suppression
            const removeBtn = ingredientTemplate.querySelector('.btn-remove-ingredient');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    this.closest('.ingredient-row').remove();
                });
            }
            
            ingredientsContainer.appendChild(ingredientTemplate);
        });
        
        // Initialiser les boutons de suppression existants
        const removeIngredientBtns = document.querySelectorAll('.btn-remove-ingredient');
        removeIngredientBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Ne pas supprimer si c'est le seul ingrédient restant
                const ingredientRows = document.querySelectorAll('.ingredient-row');
                if (ingredientRows.length > 1) {
                    this.closest('.ingredient-row').remove();
                } else {
                    alert('Vous devez avoir au moins un ingrédient.');
                }
            });
        });
    }
});