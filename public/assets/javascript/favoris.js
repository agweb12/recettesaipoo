document.addEventListener('DOMContentLoaded', function() {
    const favoriteBtn = document.querySelector('.favorite-btn');
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const recipeId = this.dataset.id;
            const isFavorite = this.dataset.favorite === '1';
            const action = isFavorite ? 'remove' : 'add';

            // Créer un objet FormData
            const formData = new FormData();
            formData.append('id_recette', recipeId);
            formData.append('action', action);

            // Récupérer la racine du site à partir du meta tag
            const RACINESITE = document.querySelector('meta[name="racine-site"]')?.getAttribute('content') || '/recettesaipoo/';

            // Envoyer la requête avec FormData
            fetch(RACINESITE + 'api/favoris', {
                method: 'POST',
                body: formData
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