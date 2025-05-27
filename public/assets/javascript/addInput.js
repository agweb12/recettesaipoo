document.addEventListener('DOMContentLoaded', function() {
    const addIngredientBtn = document.getElementById('addIngredient');
    const ingredientContainer = document.querySelector('.inputBox').parentElement; // Sélectionne le parent de la première boîte d'entrée pour ajouter de nouveaux ingrédients
    
    if (addIngredientBtn) {
        addIngredientBtn.addEventListener('click', function() {
            const newInputBox = document.createElement('div');
            newInputBox.className = 'inputBox';
            newInputBox.innerHTML = `
                <i class="fi fi-sr-search-heart"></i>
                <input type="text" name="ingredients[]" class="inputIngredient" placeholder="Ex: Tomate" autocomplete="off">
                <button type="button" class="removeIngredient"><i class="fi fi-sr-cross"></i></button>
            `;
            ingredientContainer.insertBefore(newInputBox, addIngredientBtn); // Insère la nouvelle boîte d'entrée avant le bouton "Ajouter un ingrédient" et non à la fin de la liste

            // Ajouter l'écouteur d'événement pour le bouton de suppression
            const removeBtn = newInputBox.querySelector('.removeIngredient');
            removeBtn.addEventListener('click', function() {
                newInputBox.remove();
            });
        });
    } 
});
