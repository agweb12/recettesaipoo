<?php 
require_once('../../../inc/functions.php'); // Inclure le fichier de fonctions
header('Content-Type: application/javascript');
?>
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

            /* console.log('Envoi de la requête avec:', {
                recipeId: recipeId,
                action: action,
                url: '<?= RACINE_SITE ?>inc/favoris.php'
            }); */

            // Envoyer la requête avec FormData
            fetch('<?= RACINE_SITE ?>inc/favoris.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // console.log("Status de la réponse:", response.status);
                if (!response.ok) {
                    throw new Error('Réponse réseau non valide');
                }
                return response.json();
            })
            .then(data => {
                // console.log("Données reçues:", data);
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
                //console.error('Erreur:', error);
                alert('Une erreur est survenue. Veuillez réessayer.');
            });
        });
    } else {
        console.log('Bouton favoris non trouvé');
    }
});