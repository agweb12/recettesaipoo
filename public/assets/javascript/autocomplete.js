document.addEventListener('DOMContentLoaded', function() {
    const ingredientsContainer = document.getElementById('ingredients-container');
    const addIngredientBtn = document.getElementById('addIngredient');
    const selectedIngredientsContainer = document.querySelector('.selected-ingredients');
// Définir RACINE_SITE pour JavaScript si ce n'est pas déjà fait
const RACINE_SITE = "<?= RACINE_SITE ?>";
    // Fonction pour initialiser l'autocomplétion sur un champ
    function initAutocomplete(inputElement) {
        let currentFocus;
        let selectedIngredients = [];
        
        // Événement lors de la saisie dans le champ
        inputElement.addEventListener('input', function(e) {
            const val = this.value;
            closeAllLists();
            
            if (!val) { return false; }
            currentFocus = -1;
            
            // Créer une liste de suggestions
            const autocompleteList = document.createElement('div');
            autocompleteList.setAttribute('class', 'autocomplete-items');
            this.parentNode.appendChild(autocompleteList);
            
            // Requête AJAX pour obtenir les suggestions d'ingrédients
            fetch(`inc/ingredients.php?search=${encodeURIComponent(val)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        const noResultItem = document.createElement('div');
                        noResultItem.innerHTML = `<strong>Aucun ingrédient trouvé</strong>`;
                        autocompleteList.appendChild(noResultItem);
                        return;
                    }
                    
                    data.forEach(ingredient => {
                        // Ne pas afficher les ingrédients déjà sélectionnés
                        if (selectedIngredients.includes(ingredient.id)) return;
                        
                        const item = document.createElement('div');
                        // Mettre en surbrillance les caractères correspondants
                        const matchIndex = ingredient.nom.toLowerCase().indexOf(val.toLowerCase());
                        if (matchIndex !== -1) {
                            item.innerHTML = ingredient.nom.substring(0, matchIndex) + 
                                             "<strong>" + ingredient.nom.substring(matchIndex, matchIndex + val.length) + "</strong>" + 
                                             ingredient.nom.substring(matchIndex + val.length);
                        } else {
                            item.innerHTML = ingredient.nom;
                        }
                        
                        // Stocker l'ID de l'ingrédient
                        item.setAttribute('data-id', ingredient.id);
                        
                        // Événement de clic sur un élément de la liste
                        item.addEventListener('click', function(e) {
                            const selectedId = this.getAttribute('data-id');
                            const selectedName = this.innerText;
                            
                            // Ajouter l'ingrédient à la liste des sélectionnés
                            addSelectedIngredient(selectedId, selectedName);
                            
                            // Vider le champ et fermer la liste
                            inputElement.value = '';
                            closeAllLists();
                        });
                        
                        autocompleteList.appendChild(item);
                    });
                })
                .catch(error => console.error('Erreur lors de la récupération des ingrédients:', error));
        });
        
        // Fonction pour ajouter un ingrédient sélectionné
        function addSelectedIngredient(id, name) {
            // Vérifier si l'ingrédient est déjà sélectionné
            if (selectedIngredients.includes(id)) return;
            
            selectedIngredients.push(id);
            
            // Créer une puce (badge) pour l'ingrédient sélectionné
            const badge = document.createElement('span');
            badge.className = 'ingredient-badge';
            badge.innerHTML = `
                ${name}
                <input type="hidden" name="ingredients[]" value="${id}">
                <button type="button" class="remove-ingredient"><i class="fi fi-sr-cross-small"></i></button>
            `;
            
            // Ajouter l'événement de suppression
            badge.querySelector('.remove-ingredient').addEventListener('click', function() {
                const index = selectedIngredients.indexOf(id);
                if (index > -1) {
                    selectedIngredients.splice(index, 1);
                }
                badge.remove();
            });
            
            // Ajouter le badge au conteneur
            selectedIngredientsContainer.appendChild(badge);
        }
        
        // Fermer toutes les listes d'autocomplétion, sauf celle spécifiée
        function closeAllLists(elmnt) {
            const autocompleteItems = document.getElementsByClassName('autocomplete-items');
            for (let i = 0; i < autocompleteItems.length; i++) {
                if (elmnt != autocompleteItems[i] && elmnt != inputElement) {
                    autocompleteItems[i].parentNode.removeChild(autocompleteItems[i]);
                }
            }
        }
        
        // Fermer les listes lors d'un clic à l'extérieur
        document.addEventListener('click', function(e) {
            closeAllLists(e.target);
        });
    }
    
    // Initialiser l'autocomplétion sur le premier champ
    initAutocomplete(document.querySelector('.ingredient-autocomplete'));
    
    // Ajouter un nouveau champ d'ingrédient
    addIngredientBtn.addEventListener('click', function() {
        const newInputGroup = document.createElement('div');
        newInputGroup.className = 'ingredient-input-group';
        newInputGroup.innerHTML = `
            <div class="inputBox">
                <i class="fi fi-sr-search-heart"></i>
                <input type="text" class="ingredient-autocomplete" placeholder="Ex: Tomate" autocomplete="off">
                <input type="hidden" name="ingredients[]" class="ingredient-id">
                <button type="button" class="remove-input"><i class="fi fi-sr-cross-small"></i></button>
            </div>
        `;
        
        // Ajouter l'événement de suppression
        newInputGroup.querySelector('.remove-input').addEventListener('click', function() {
            newInputGroup.remove();
        });
        
        // Ajouter au conteneur et initialiser l'autocomplétion
        ingredientsContainer.appendChild(newInputGroup);
        initAutocomplete(newInputGroup.querySelector('.ingredient-autocomplete'));
    });
});

