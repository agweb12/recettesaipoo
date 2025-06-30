<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recettes;
use App\Models\Ingredients;
use App\Models\Utilisateurs;

class ProfileController extends Controller {
    private $userModel;
    private $recetteModel;
    private $ingredientModel;

    public function __construct() {
        $this->userModel = new Utilisateurs();
        $this->recetteModel = new Recettes();
        $this->ingredientModel = new Ingredients();
    }

    /**
     * Afficher et gérer la page de compte utilisateur
     */
    public function account(){
        // Vérifier si l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            $this->redirect(RACINE_SITE . 'connexion');
        }

        // Vérifier si l'ID est présent dans l'URL et correspond à l'utilisateur connecté
        $id = $_GET['id'] ?? null;
        if (!$id || $id != $_SESSION['user']['id']) {
            $this->redirect(RACINE_SITE . 'connexion');
        }

        $userId = $_SESSION['user']['id'];
        $message = ''; // Message pour l'utilisateur
        $messageType = ''; // Type de message (success, error, etc.)

        // Traitement des actions basées sur POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

            // Validation CSRF
            if (!$this->validateCSRF()) {
                return;
            }
            
            $message = $this->handlePostActions($_POST, $userId);
            if (isset($message['redirect'])) {
                $this->redirect($message['redirect']);
            }
            $messageType = $message['type'] ?? '';
            $message = $message['message'] ?? '';
        }

        // Récupération des ingrédients de l'utilisateur
        $ingredientsUtilisateur = $this->ingredientModel->getUserIngredients($userId);

        // Récupération des recettes favorites de l'utilisateur
        $recettesFavorites = $this->recetteModel->getUserFavorites($userId);

        // Charger la vue
        $this->view('profil/monCompte', [
            'titlePage' => "Compte - Recettes AI",
            'descriptionPage' => "Gérez votre compte et vos préférences sur Recettes AI.",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "Recettes AI, compte, profil, favoris, ingrédients",
            'ingredientsUtilisateur' => $ingredientsUtilisateur,
            'recettesFavorites' => $recettesFavorites,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Gère les actions POST du formulaire
     * 
     * @param array $postData Les données POST
     * @param int $userId L'ID de l'utilisateur
     * @return array Message de résultat
     */
    private function handlePostActions($postData, $userId) {
        switch ($postData['action']) {
            // Suppression d'un ingrédient
            case 'supprimer_ingredient':
                if (isset($postData['id_ingredient'])) {
                    $success = $this->ingredientModel->deleteUserIngredient($userId, $postData['id_ingredient']);
                    return [
                        'message' => $success 
                            ? "L'ingrédient a été supprimé de votre liste." 
                            : "Une erreur est survenue lors de la suppression de l'ingrédient.",
                        'type' => $success ? 'success' : 'error'
                    ];
                }
                break;

            // Suppression d'une recette favorite
            case 'supprimer_favori':
                if (isset($postData['id_recette'])) {
                    $recipeId = intval($postData['id_recette']);
                    $success = $this->recetteModel->removeFromFavorites($userId, $recipeId);
                    return [
                        'message' => $success 
                            ? "La recette a été retirée de vos favoris." 
                            : "Une erreur est survenue lors de la suppression du favori.",
                        'type' => $success ? 'success' : 'error'
                    ];
                }
                break;

            // Suppression de tous les ingrédients
            case 'supprimer_tous_ingredients':
                $success = $this->ingredientModel->deleteUserIngredients($userId);
                return [
                    'message' => $success 
                        ? "Tous vos ingrédients ont été supprimés." 
                        : "Une erreur est survenue lors de la suppression de vos ingrédients.",
                    'type' => $success ? 'success' : 'error'
                ];

            // Suppression de tous les favoris
            case 'supprimer_tous_favoris':
                $success = $this->recetteModel->removeAllFavorites($userId);
                return [
                    'message' => $success 
                        ? "Toutes vos recettes favorites ont été supprimées." 
                        : "Une erreur est survenue lors de la suppression de vos favoris.",
                    'type' => $success ? 'success' : 'error'
                ];

            // Suppression du compte
            case 'supprimer_compte':
                if (isset($postData['confirm_suppression']) && $postData['confirm_suppression'] === 'oui') {
                    $success = $this->userModel->deleteUser($userId);
                    if ($success) {
                        // Destruction de la session
                        session_destroy();
                        return [
                            'redirect' => RACINE_SITE . '?msg=compte_supprime'
                        ];
                    } else {
                        return [
                            'message' => "Une erreur est survenue lors de la suppression de votre compte.",
                            'type' => 'error'
                        ];
                    }
                }
                break;

            // Changement de mot de passe
            case 'changer_mot_de_passe':
                if (isset($postData['ancien_mot_de_passe'], $postData['nouveau_mot_de_passe'], $postData['confirmer_mot_de_passe'])) {
                    // Vérifier l'ancien mot de passe
                    $user = $this->userModel->findById($userId);
                    if ($user && password_verify($postData['ancien_mot_de_passe'], $user['mot_de_passe'])) {
                        // Vérifier que les nouveaux mots de passe correspondent
                        if ($postData['nouveau_mot_de_passe'] === $postData['confirmer_mot_de_passe']) {
                            // Hashage et mise à jour du mot de passe
                            $hashedPassword = password_hash($postData['nouveau_mot_de_passe'], PASSWORD_BCRYPT, ['cost' => 12]);
                            $success = $this->userModel->updatePassword($userId, $hashedPassword);
                            return [
                                'message' => $success 
                                    ? "Votre mot de passe a été modifié avec succès." 
                                    : "Une erreur est survenue lors de la modification de votre mot de passe.",
                                'type' => $success ? 'success' : 'error'
                            ];
                        } else {
                            return [
                                'message' => "Les nouveaux mots de passe ne correspondent pas.",
                                'type' => 'error'
                            ];
                        }
                    } else {
                        return [
                            'message' => "Ancien mot de passe incorrect.",
                            'type' => 'error'
                        ];
                    }
                }
                break;
        }
        return ['message' => '', 'type' => ''];
    }
}