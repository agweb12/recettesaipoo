document.addEventListener('DOMContentLoaded', function() {
    // Récupérer tous les éléments de recettes et les checkboxes de filtres
    const recipeBoxes = document.querySelectorAll('.recipeBox');
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    const resetButton = document.getElementById('reset-filters');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    
    // Objet pour stocker les filtres actifs
    const activeFilters = {
        difficulte: [],
        temps_preparation: [],
        temps_cuisson: [],
        categorie: [],
        etiquette: []
    };
    
    syncFiltersWithURL(); // Synchroniser les filtres avec l'URL au chargement de la page

    // Ajouter des écouteurs d'événements pour les changements de filtres
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            let type = this.dataset.type;
            let value = this.value;

            if(this.checked) {
                // Ajouter la valeur au filtre actif
                if(!activeFilters[type].includes(value)) {
                    activeFilters[type].push(value);
                }
            } else {
                // Retirer la valeur du filtre actif
                activeFilters[type] = activeFilters[type].filter(item => item !== value);
            }

            // Mettre à jour l'URL et Appliquer les filtres
            updateURL();
            applyFilters();
        });
    });

    // Réinitialiser les filtres
    if(resetButton){
        resetButton.addEventListener('click', function() {
            // Décocher toutes les cases
            filterCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Réinitialiser les filtres actifs
            for (const filterType in activeFilters) {
                activeFilters[filterType] = [];
            }

            // Vider le champ de recherche
            if(searchInput) {
                searchInput.value = '';
            }

            // Mettre à jour l'URL et appliquer les filtres
            // Passer true pour supprimer le paramètre de recherche
            updateURL(true);
            
            // Recharger la page pour effacer complètement les filtres
            window.location.reload();
        });
    }

    // écouteur d'événements pour le formulaire de recherche
    if(searchForm){
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Empêcher le rechargement de la page

            // Mettre à jour l'URL avec les paramètres de recherche et de filtres
            // Passer false pour inclure le paramètre de recherche
            updateURL(false);

            // Recharger la page pour effectuer la recherche côté serveur
            window.location.reload();
        });
    }

    // fonction pour synchroniser les filtres avec l'URL
    function syncFiltersWithURL() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Parcourir tous les types de filtres
        for (const filterType in activeFilters) {
            const paramName = `filtre_${filterType}`;
            if (urlParams.has(paramName)) {
                const filterValues = urlParams.get(paramName).split(',');
                activeFilters[filterType] = filterValues;
                
                // Cocher les cases correspondantes
                filterValues.forEach(value => {
                    const checkbox = document.querySelector(`.filter-checkbox[data-type="${filterType}"][value="${value}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        }
        
        // Appliquer les filtres
        applyFilters();
    }

    // Fonction pour mettre à jour l'URL avec les filtres actuels
    function updateURL(removeSearch = false) {
        let urlParams = new URLSearchParams(window.location.search);

        // Mettre à jour les paramètres de filtre
        for (let filterType in activeFilters) {
            let paramName = `filtre_${filterType}`;
            if (activeFilters[filterType].length > 0) {
                urlParams.set(paramName, activeFilters[filterType].join(','));
            } else {
                urlParams.delete(paramName);
            }
        }

        // Gérer le paramètre de recherche
        if (removeSearch) {
            // Supprimer le paramètre de recherche
            urlParams.delete('search');
        } else {
            // Inclure le paramètre de recherche s'il existe
            if (searchInput && searchInput.value.trim() !== '') {
                urlParams.set('search', searchInput.value.trim());
            }
        }

        // Mettre à jour l'URL sans recharger la page
        let newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.pushState({ path: newUrl }, '', newUrl);
    }

    // Fonction pour appliquer les filtres
    function applyFilters() {
        recipeBoxes.forEach(recipeBox => {
            let shouldShow = true;
            
            // Vérifier chaque type de filtre
            for (const filterType in activeFilters) {
                if (activeFilters[filterType].length > 0) {
                    const recipeValue = recipeBox.dataset[filterType];
                    
                    // Traitement spécial pour les étiquettes (qui sont stockées comme une liste d'IDs séparés par des virgules)
                    if (filterType === 'etiquette') {
                        const recipeEtiquettes = recipeValue ? recipeValue.split(',') : [];
                        // Vérifier si au moins une étiquette filtrée est présente dans la recette
                        const hasMatchingEtiquette = activeFilters[filterType].some(filterId => 
                            recipeEtiquettes.includes(filterId)
                        );
                        if (!hasMatchingEtiquette) {
                            shouldShow = false;
                            break;
                        }
                    }
                    // Traitement spécial pour les temps (qui sont comparés en "moins que")
                    else if (filterType === 'temps_preparation' || filterType === 'temps_cuisson') {
                        const recipeTime = parseInt(recipeValue, 10);
                        // Pour chaque valeur de filtre de temps cochée
                        let matchesTimeFilter = false;
                        activeFilters[filterType].forEach(filterTime => {
                            const filterTimeValue = parseInt(filterTime, 10);
                            
                            // Si le filtre est "moins de X minutes"
                            if (filterTimeValue === 15 && recipeTime <= 15) matchesTimeFilter = true;
                            else if (filterTimeValue === 30 && recipeTime <= 30) matchesTimeFilter = true;
                            else if (filterTimeValue === 60 && recipeTime <= 60) matchesTimeFilter = true;
                            else if (filterTimeValue === 120 && recipeTime > 60) matchesTimeFilter = true;
                        });
                        
                        if (!matchesTimeFilter) {
                            shouldShow = false;
                            break;
                        }
                    }
                    // Traitement standard pour les autres filtres
                    else if (!activeFilters[filterType].includes(recipeValue)) {
                        shouldShow = false;
                        break;
                    }
                }
            }
            
            // Afficher ou masquer la recette selon le résultat du filtrage
            recipeBox.style.display = shouldShow ? 'flex' : 'none';
        });
        
        // Afficher un message si aucune recette ne correspond aux filtres
        const recipesContainer = document.querySelector('.recipes');
        const visibleRecipes = document.querySelectorAll('.recipeBox[style="display: flex;"]');
        const noResultsMessage = document.getElementById('no-results');
        
        if (visibleRecipes.length === 0 && recipesContainer) {
            if (!noResultsMessage) {
                const message = document.createElement('p');
                message.id = 'no-results';
                message.className = 'no-results-message';
                message.textContent = 'Aucune recette ne correspond à vos critères de recherche.';
                recipesContainer.appendChild(message);
            }
        } else if (noResultsMessage) {
            noResultsMessage.remove();
        }
    }
});