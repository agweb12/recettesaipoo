<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categories;

class AdminCategoriesController extends Controller {
    private $categorieModel;
    
    public function __construct() {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!isset($_SESSION['admin'])) {
            $this->redirect(RACINE_SITE . 'admin/login');
            exit();
        }
        
        // Vérifier les droits d'accès (page réservée aux superadmin et modérateurs)
        if ($_SESSION['admin']['role'] !== 'superadmin' && $_SESSION['admin']['role'] !== 'moderateur') {
            $this->redirect(RACINE_SITE . 'admin/dashboard');
            exit();
        }
        
        $this->categorieModel = new Categories();
    }
    
    /**
     * Affiche la liste des catégories
     * Cette méthode récupère toutes les catégories et les affiche dans une vue.
     * Elle inclut également un message de notification si nécessaire.
     * @return void
     */
    public function index() : void
    {
        // Récupération de la liste des catégories avec le nombre de recettes associées
        $categories = $this->categorieModel->getAllCategoriesWithUsage();
        
        // Message de notification (info/erreur)
        $info = isset($_GET['info']) ? htmlspecialchars($_GET['info']) : '';
        $infoType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
        
        $this->view('admin/categories/index', [
            'titlePage' => "Gestion des Catégories",
            'descriptionPage' => "Gérer les catégories de recettes dans Recette AI",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'categories' => $categories,
            'info' => $info,
            'infoType' => $infoType
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'une catégorie
     * Cette méthode affiche un formulaire pour ajouter une nouvelle catégorie.
     * @return void
     */
    public function create() : void
    {
        $this->view('admin/categories/create', [
            'titlePage' => "Ajouter une catégorie",
            'descriptionPage' => "Ajouter une nouvelle catégorie de recettes",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => ""
        ]);
    }
    
    /**
     * Traite le formulaire d'ajout d'une catégorie
     * Cette méthode vérifie les données du formulaire, gère le téléchargement de l'image,
     * et insère la nouvelle catégorie dans la base de données.
     * Si une erreur survient, elle redirige vers le formulaire avec un message d'erreur.
     * Si l'ajout est réussi, elle redirige vers la liste des catégories avec un message de succès.
     * @return void
     */
    public function store() : void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/categories');
            exit();
        }
        
        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }

        $nom = trim($_POST['nom']);
        $descriptif = trim($_POST['descriptif']);
        $couleur = trim($_POST['couleur']);
        $couleurTexte = trim($_POST['couleurTexte']);
        
        // Vérification des champs
        if (empty($nom)) {
            $this->redirect(RACINE_SITE . 'admin/categories/create?info=Le nom de la catégorie est obligatoire.&type=danger');
            exit();
        }
        
        // Gestion de l'image téléchargée
        $image_url = null;
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
            $image_url = $this->uploadImage($_FILES['image_url'], null);
            if ($image_url === false) {
                $this->redirect(RACINE_SITE . 'admin/categories/create?info=Format d\'image non supporté.&type=danger');
                exit();
            }
        }
        
        // Vérifier si le nom existe déjà
        if ($this->categorieModel->existsByName($nom)) {
            $this->redirect(RACINE_SITE . 'admin/categories/create?info=Une catégorie avec ce nom existe déjà.&type=danger');
            exit();
        }
        
        // Insertion de la nouvelle catégorie
        $categoryData = [
            'nom' => $nom,
            'descriptif' => $descriptif,
            'couleur' => $couleur,
            'couleurTexte' => $couleurTexte,
            'image_url' => $image_url,
            'id_admin' => $_SESSION['admin']['id']
        ];
        
        $lastId = $this->categorieModel->create($categoryData);
        
        if ($lastId) {
            // Enregistrement de l'action dans le journal
            $this->categorieModel->logAdminAction($_SESSION['admin']['id'], 'categorie', $lastId, 'ajout');
            
            $this->redirect(RACINE_SITE . 'admin/categories?info=Nouvelle catégorie ajoutée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/categories/create?info=Erreur lors de l\'ajout de la catégorie.&type=danger');
        }
    }
    
    /**
     * Affiche le formulaire d'édition d'une catégorie
     * Cette méthode récupère les détails d'une catégorie par son ID
     * et affiche un formulaire pour la modifier.
     * Si la catégorie n'existe pas, elle redirige vers la liste des catégories avec un message d'erreur.
     * @param int $id L'ID de la catégorie à modifier
     * @return void
     */
    public function edit(int $id) : void
    {
        $categorie = $this->categorieModel->findByIdCategories($id);
        
        if (!$categorie) {
            $this->redirect(RACINE_SITE . 'admin/categories?info=Catégorie introuvable.&type=danger');
            exit();
        }
        
        $this->view('admin/categories/edit', [
            'titlePage' => "Modifier une catégorie",
            'descriptionPage' => "Modifier une catégorie existante",
            'indexPage' => "noindex",
            'followPage' => "nofollow",
            'keywordsPage' => "",
            'categorie' => $categorie
        ]);
    }
    
    /**
     * Met à jour une catégorie
     * Cette méthode traite le formulaire de modification d'une catégorie.
     * Elle vérifie les données, gère le téléchargement de l'image,
     * et met à jour la catégorie dans la base de données.
     * Si une erreur survient, elle redirige vers le formulaire avec un message d'erreur.
     * Si la mise à jour est réussie, elle redirige vers la liste des catégories avec un message de succès.
     * @param int $id L'ID de la catégorie à mettre à jour
     * @return void
     */
    public function update(int $id) : void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(RACINE_SITE . 'admin/categories');
            exit();
        }
        
        // Validation CSRF
        if (!$this->validateCSRF()) {
            return;
        }

        $nom = trim($_POST['nom']);
        $descriptif = trim($_POST['descriptif']);
        $couleur = trim($_POST['couleur']);
        $couleurTexte = trim($_POST['couleurTexte']);
        $image_url = isset($_POST['image_url_actuelle']) ? $_POST['image_url_actuelle'] : null;
        
        // Vérification des champs
        if (empty($nom)) {
            $this->redirect(RACINE_SITE . 'admin/categories/edit/' . $id . '?info=Le nom de la catégorie est obligatoire.&type=danger');
            exit();
        }
        
        // Gestion de l'image téléchargée
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
            $new_image_url = $this->uploadImage($_FILES['image_url'], $id);
            if ($new_image_url === false) {
                $this->redirect(RACINE_SITE . 'admin/categories/edit/' . $id . '?info=Format d\'image non supporté.&type=danger');
                exit();
            }
            $image_url = $new_image_url;
        }
        
        // Vérifier si le nom existe déjà (sauf pour la catégorie en cours)
        if ($this->categorieModel->existsByNameExcept($nom, $id)) {
            $this->redirect(RACINE_SITE . 'admin/categories/edit/' . $id . '?info=Une catégorie avec ce nom existe déjà.&type=danger');
            exit();
        }
        
        // Mise à jour de la catégorie
        $categoryData = [
            'nom' => $nom,
            'descriptif' => $descriptif,
            'couleur' => $couleur,
            'couleurTexte' => $couleurTexte
        ];
        
        if ($image_url) {
            $categoryData['image_url'] = $image_url;
        }
        
        $result = $this->categorieModel->update($id, $categoryData);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->categorieModel->logAdminAction($_SESSION['admin']['id'], 'categorie', $id, 'modification');
            
            $this->redirect(RACINE_SITE . 'admin/categories?info=Catégorie mise à jour avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/categories/edit/' . $id . '?info=Erreur lors de la mise à jour de la catégorie.&type=danger');
        }
    }
    
    /**
     * Supprime une catégorie
     * Cette méthode supprime une catégorie par son ID.
     * Avant de supprimer, elle vérifie si la catégorie est utilisée dans des recettes.
     * Si elle est utilisée, elle redirige vers la liste des catégories avec un message d'erreur.
     * Si la suppression est réussie, elle redirige vers la liste des catégories avec un message de succès.
     * Si une erreur survient, elle redirige avec un message d'erreur.
     * @param int $id L'ID de la catégorie à supprimer
     * @return void
     */
    public function delete(int $id) : void
    {
        // Validation CSRF pour les suppressions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->validateCSRF()) {
            return;
        }
        
        // Vérifier si la catégorie est utilisée dans des recettes
        if ($this->categorieModel->isUsedInRecipes($id)) {
            $this->redirect(RACINE_SITE . 'admin/categories?info=Cette catégorie est utilisée dans une ou plusieurs recettes et ne peut pas être supprimée.&type=warning');
            exit();
        }
        
        $result = $this->categorieModel->delete($id);
        
        if ($result) {
            // Enregistrement de l'action dans le journal
            $this->categorieModel->logAdminAction($_SESSION['admin']['id'], 'categorie', $id, 'suppression');
            
            $this->redirect(RACINE_SITE . 'admin/categories?info=Catégorie supprimée avec succès.&type=success');
        } else {
            $this->redirect(RACINE_SITE . 'admin/categories?info=Erreur lors de la suppression de la catégorie.&type=danger');
        }
    }
    
    /**
     * Gestion du téléchargement des images
     * Cette méthode gère le téléchargement des images pour les catégories.
     * Elle vérifie le type de fichier, le redimensionne si nécessaire,
     * et enregistre l'image dans le répertoire approprié.
     * @param array $file Le fichier téléchargé
     * @param int|null $id L'ID de la catégorie (pour nommer le fichier de manière unique)
     * @return bool|string Le chemin de l'image téléchargée ou false en cas d'erreur
     */
    private function uploadImage($file, ?int $id) : bool|string
    {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $file['name'];
        $tmp = explode('.', $filename);
        $ext = strtolower(end($tmp));
        
        if (!in_array($ext, $allowed)) {
            return false;
        }
        
        $newFilename = 'categorie_' . ($id ? $id . '_' : '') . time() . '.' . $ext;
        $uploadDir = ROOT_DIR . '/public/assets/img/categories/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $destination = $uploadDir . $newFilename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'public/assets/img/categories/' . $newFilename;
        }
        
        return false;
    }
}