document.addEventListener('DOMContentLoaded', function() {
    const favoriteBtn = document.querySelector('.favorite-btn:not(.profile-favorite-btn)');
    // Vérifier que le bouton existe avant d'ajouter l'événement
    if (favoriteBtn) {
        // Ajouter un écouteur d'événement pour le clic sur le bouton
        favoriteBtn.addEventListener('click', function() {
            const recipeId = this.dataset.id; // Récupérer l'ID de la recette depuis l'attribut data-id
            const isFavorite = this.dataset.favorite === '1'; // Vérifier si la recette est déjà favorite
            const action = isFavorite ? 'remove' : 'add'; // Déterminer l'action à effectuer (ajouter ou supprimer des favoris)

            // Créer un objet FormData
            const formData = new FormData(); // Créer un nouvel objet FormData
            formData.append('id_recette', recipeId); // Ajouter l'ID de la recette
            formData.append('action', action); // Ajouter l'action (ajouter ou supprimer)

            // Récupérer la racine du site à partir du meta tag
            const RACINESITE = document.querySelector('meta[name="racine-site"]')?.getAttribute('content') || '/recettesaipoo/';

            // Envoyer la requête avec FormData
            fetch(RACINESITE + 'api/favoris', { // Utiliser la racine du site pour construire l'URL
                method: 'POST', // Utiliser la méthode POST
                body: formData // Envoyer les données du formulaire
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Réponse réseau non valide');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mettre à jour l'interface utilisateur
                    this.classList.toggle('is-active');
                    this.dataset.favorite = isFavorite ? '0' : '1';

                    // Changer l'icône
                    const icon = this.querySelector('i');
                    icon.className = isFavorite ? 'fi fi-rr-heart' : 'fi fi-sr-heart';

                    // Ajouter une animation
                    this.classList.add('animate');
                    setTimeout(() => {
                        this.classList.remove('animate');
                    }, 500);

                    // Mettre à jour la classe du conteneur de la recette
                    const recipeBox = document.querySelector('.recipeBox');
                    if(recipeBox){
                        recipeBox.classList.toggle('is-active');
                    }
                } else {
                    // Afficher une erreur
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Une erreur est survenue. Veuillez réessayer.');
            });
        });
    }
});