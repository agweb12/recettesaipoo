<?php
namespace App\Controllers;

use PDO;
use App\Core\Database;
use App\Core\Controller;
use App\Models\Recettes;
use App\Models\Categories;
use App\Models\Etiquettes;
use App\Models\Ingredients;
use App\Models\UnitesMesure;
use App\Helpers\StructuredData;

class RecettesController extends Controller {
    private $recetteModel;
    private $ingredientModel;
    private $etiquetteModel;
    private $categorieModel;
    private $uniteMesureModel;

    public function __construct() {
        $this->recetteModel = new Recettes();
        $this->ingredientModel = new Ingredients();
        $this->etiquetteModel = new Etiquettes();
        $this->categorieModel = new Categories();
        $this->uniteMesureModel = new UnitesMesure();
    }

    /**
     * Affiche toutes les recettes avec options de filtrage et de recherche
     * @return void
     */
    public function index() : void
    {
        $whereConditions = []; // Tableau pour stocker les conditions WHERE de la requête SQL
        $filterParams = []; // Tableau pour stocker les paramètres de filtre
        // Logique pour afficher toutes les recettes
        $recettes= []; // Tableau pour stocker les recettes
        $ingredientsUtilisateur = []; // Tableau pour stocker les ingrédients de l'utilisateur
        $recettesRecherche = []; // Tableau pour stocker les recettes de recherche
        $recettesFavorisIds = []; // Tableau pour stocker les IDs des recettes favorites de l'utilisateur
        $formIngredients = isset($_GET['formIngredients']) && $_GET['formIngredients'] == 1; // Vérifie si le formulaire d'ingrédients a été soumis
        $hasFilters = $this->hasFilters($_GET); // Vérifie si des filtres sont appliqués
        $recettesGroupees = [
            'complete' => [],
            'partielle' => []
        ];
        // Si l'utilisateur est connecté
        if($this->isLoggedIn()){
            $userId = $_SESSION['user']['id'];

            // Récupérer les favoris de l'utilisateur
            $recettesFavorisIds = $this->recetteModel->getUserFavoriteIds($userId);

            // Si le formulaire d'ingrédients a été soumis
            if($formIngredients === true){
                // Récupérer les ingrédients de l'utilisateur
                $ingredientsUtilisateur = $this->ingredientModel->getUserIngredients($userId);
                
                // Récupérer les recettes correspondant aux ingrédients de l'utilisateur
                $recettesIngredients = $this->recetteModel->getRecipesByUserIngredients($userId);

                // Ajouter le nombre total d'ingrédients pour chaque recette
                // array_map permet de parcourir chaque recette et d'ajouter le nombre d'ingrédients
                $recettes = array_map(function($recette){
                    $recette['nombre_ingredients_total'] = $this->recetteModel->countRecipeIngredients($recette['id']);
                    return $recette;
                }, $recettesIngredients);

                // Préparer les recettes avec le pourcentage et les séparer en deux groupes
                $recettesGroupees = $this->prepareRecettesWithPercentage($recettes);
            }
        }
        
        // Traiter les recherches et les filtres
        if(isset($_GET['search']) || $hasFilters) {
            // Construire les conditions de recherche
            if(isset($_GET['search']) && !empty($_GET['search'])) {
                $searchTerm = $_GET['search'];
                $whereConditions[] = "(r.nom LIKE :search OR r.descriptif LIKE :search)";
                $filterParams[':search'] = "%{$searchTerm}%";
            }
            
            // Traiter les filtres
            $this->processFilters($_GET, $whereConditions, $filterParams);
            
            // Récupérer les recettes filtrées
            $recettesIngredients = $this->recetteModel->getFilteredRecipes($whereConditions, $filterParams);

            // Pour chaque recette, récupérer les étiquettes associées et préparer le tableau d'IDs des recettes
            // On utilise array_map pour parcourir chaque recette et ajouter les étiquettes
            $recettesRecherche = array_map(function($recette) {
                $etiquettes = $this->etiquetteModel->getRecipeEtiquettes($recette['id']);
                
                $etiquettesIds = [];
                foreach($etiquettes as $etiquette) {
                    $etiquettesIds[] = $etiquette['id'];
                }
                
                $recette['etiquettes_ids'] = implode(',', $etiquettesIds);
                $recette['nb_etiquettes'] = count($etiquettes);
                
                return $recette;
            }, $recettesIngredients);
        }

        // Récupérer toutes les catégories et étiquettes pour les filtres
        $categories = $this->categorieModel->getAllCategories();
        $etiquettes = $this->etiquetteModel->getAllEtiquettes();
        

        $breadcrumbs = [
            [
                'name' => 'Accueil',
                'url' => RACINE_SITE . 'accueil'
            ],
            [
                'name' => 'Recettes',
                'url' => RACINE_SITE . 'recettes'
            ]
        ];
        
        // 🚀 AJOUT : Générer les données structurées pour la liste
        $structuredData = [
            'website' => StructuredData::getWebSiteData(),
            'organization' => StructuredData::getOrganizationData(),
            'breadcrumb' => StructuredData::getBreadcrumbData($breadcrumbs)
        ];
        
        // Si on a des recettes à afficher, ajouter les données de liste
        if (!empty($recettesRecherche)) {
            $structuredData['recipeList'] = StructuredData::getRecipeListData($recettesRecherche);
        } elseif (!empty($recettesGroupees['complete']) || !empty($recettesGroupees['partielle'])) {
            $allRecipes = array_merge($recettesGroupees['complete'], $recettesGroupees['partielle']);
            if (!empty($allRecipes)) {
                $structuredData['recipeList'] = StructuredData::getRecipeListData($allRecipes);
            }
        }


        // Charger la vue avec les données
        $this->view('recettes', [
            'titlePage' => $formIngredients && $this->isLoggedIn() ? 
                "Vos recettes personnalisées - Recettes AI" : 
                "Toutes les recettes de cuisine - Recettes AI",
            'descriptionPage' => $formIngredients && $this->isLoggedIn() ? 
                "Découvrez les recettes parfaites avec vos ingrédients disponibles" : 
                "Découvrez notre collection complète de recettes de cuisine. Filtrez par difficulté, temps de préparation et catégories.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "recettes cuisine, filtres recettes, temps préparation, catégories culinaires, ingrédients disponibles",
            'isLoggedIn' => $this->isLoggedIn(),
            'recettes' => $recettes,
            'recettesRecherche' => $recettesRecherche,
            'ingredientsUtilisateur' => $ingredientsUtilisateur,
            'recettesFavorisIds' => $recettesFavorisIds,
            'etiquettes' => $etiquettes,
            'categories' => $categories,
            'recettesComplete' => $recettesGroupees['complete'],
            'recettesPartielle' => $recettesGroupees['partielle'],
            'formIngredients' => $formIngredients,
            'hasFilters' => $hasFilters,
            'structuredData' => $structuredData,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    /**
     * Vérifie si la requête contient des filtres
     * @param array $request La requête HTTP
     * @return bool True si des filtres sont présents, sinon false
     */
    private function hasFilters($request) : bool
    {
        $filterTypes = ['difficulte', 'temps_preparation', 'temps_cuisson', 'categorie', 'etiquette'];
        foreach($filterTypes as $type) {
            $paramName = "filtre_{$type}";
            if(isset($request[$paramName]) && !empty($request[$paramName])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Traite les filtres de la requête et construit les conditions WHERE
     * @param array $request La requête HTTP
     * @param array $whereConditions Tableau pour stocker les conditions WHERE
     * @param array $filterParams Tableau pour stocker les paramètres de filtre
     * @return void
     */
    private function processFilters($request, &$whereConditions, &$filterParams) : void
    {
        $filterTypes = ['difficulte', 'temps_preparation', 'temps_cuisson', 'categorie', 'etiquette'];
        foreach($filterTypes as $type) {
            $paramName = "filtre_{$type}";
            if(isset($request[$paramName]) && !empty($request[$paramName])) {
                $values = explode(',', $request[$paramName]);
                
                if($type === 'etiquette') {
                    $whereConditions[] = "r.id IN (
                        SELECT DISTINCT re.id_recette 
                        FROM recette_etiquette re 
                        WHERE re.id_etiquette IN (" . implode(',', array_map('intval', $values)) . ")
                    )";
                }
                else if($type === 'temps_preparation' || $type === 'temps_cuisson') {
                    $timeConditions = [];
                    foreach($values as $value) {
                        $value = intval($value);
                        if($value === 15) {
                            $timeConditions[] = "r.{$type} <= 15";
                        } else if($value === 30) {
                            $timeConditions[] = "r.{$type} <= 30";
                        } else if($value === 60) {
                            $timeConditions[] = "r.{$type} <= 60";
                        } else if($value === 120) {
                            $timeConditions[] = "r.{$type} > 60";
                        }
                    }
                    if(count($timeConditions) > 0) {
                        $whereConditions[] = "(" . implode(' OR ', $timeConditions) . ")";
                    }
                }
                else {
                    $placeholders = [];
                    foreach($values as $i => $val) {
                        $placeholder = ":{$type}_{$i}";
                        $placeholders[] = $placeholder;
                        $filterParams[$placeholder] = $val;
                    }
                    $whereConditions[] = "r.{$type} IN (" . implode(',', $placeholders) . ")";
                }
            }
        }
    }

    /**
     * Prépare les recettes avec le pourcentage de disponibilité des ingrédients
     * @param array $recettes Les recettes à préparer
     * @return array Les recettes avec pourcentage de disponibilité
     */
    private function prepareRecettesWithPercentage(array $recettes): array
    {
        $recettesPreparees = [];
        $recettesComplete = []; // Recettes avec 100% des ingrédients
        $recettesPartielle = []; // Recettes avec moins de 100% des ingrédients
        
        foreach ($recettes as $recette) {
            // Calculer le pourcentage de disponibilité
            $pourcentage = ($recette['nombre_ingredients_correspondants'] / $recette['nombre_ingredients_total']) * 100;
            $recette['pourcentage_disponibilite'] = round($pourcentage, 0);
            
            // Trier selon que tous les ingrédients sont disponibles ou non
            if ($recette['pourcentage_disponibilite'] == 100) {
                $recettesComplete[] = $recette;
            } else {
                $recettesPartielle[] = $recette;
            }
        }
        
        // Trier les recettes partielles par pourcentage de disponibilité décroissant
        usort($recettesPartielle, function($a, $b) {
            return $b['pourcentage_disponibilite'] <=> $a['pourcentage_disponibilite'];
        });
        
        $recettesPreparees['complete'] = $recettesComplete;
        $recettesPreparees['partielle'] = $recettesPartielle;
        
        return $recettesPreparees;
    }

    /**
     * Affiche les détails d'une recette
     */
    public function recette() : void
    {
        $recipeId = (int) ($_GET['id'] ?? 0);
        
        if ($recipeId <= 0) {
            $this->redirect(RACINE_SITE . 'recettes');
            return;
        }

        // 1. Récupérer les informations de la recette
        $recette = $this->recetteModel->getRecipeById($recipeId);
        if (!$recette) {
            $this->redirect(RACINE_SITE . 'recettes');
            return;
        }

        // 2. Récupérer les étiquettes de la recette
        $etiquettes = $this->etiquetteModel->getRecipeEtiquettes($recipeId);
        $etiquettesIds = [];
        foreach ($etiquettes as $etiquette) {
            $etiquettesIds[] = $etiquette['id'];
        }

        // 3. Vérifier si la recette est en favoris pour l'utilisateur connecté
        $isFavorite = false;
        $ingredientsUser = [];
        $possedeListIngredients = false;

        if ($this->isLoggedIn()) {
            $userId = $_SESSION['user']['id'];
            $isFavorite = $this->recetteModel->isRecipeFavorite($userId, $recipeId);

            // 4. Récupérer les ingrédients de l'utilisateur
            $ingredientsUser = $this->ingredientModel->getUserIngredientsIds($userId);
            $possedeListIngredients = !empty($ingredientsUser);
        }

        // 5. Récupérer les ingrédients de la recette
        $ingredients = $this->recetteModel->getRecipeIngredients($recipeId);
        $ingredientsList = [];
        
        foreach ($ingredients as $ingredient) {
            $estDisponible = in_array($ingredient['id'], $ingredientsUser);
            
            $ingredientsList[] = [
                'id' => $ingredient['id'],
                'nom' => $ingredient['nom'],
                'quantite' => $ingredient['quantite'],
                'unite' => $ingredient['unite'],
                'disponible' => $estDisponible,
                'possedeListIngredients' => $possedeListIngredients
            ];
        }

        // 6. Récupérer toutes les unités pour la légende
        $uniteMesureList = $this->uniteMesureModel->getAllUnitesMesure();

        // 7. 🚀 AJOUT : Générer les breadcrumbs
        $breadcrumbs = [
            [
                'name' => 'Accueil',
                'url' => RACINE_SITE . 'accueil'
            ],
            [
                'name' => 'Recettes',
                'url' => RACINE_SITE . 'recettes'
            ],
            [
                'name' => $recette['nom'],
                'url' => RACINE_SITE . 'recettes/recette?id=' . $recipeId
            ]
        ];

        // 8. 🚀 AJOUT : Générer les données structurées
        $structuredData = [
            'recipe' => StructuredData::getRecipeData($recette, $ingredients, $etiquettes),
            'breadcrumb' => StructuredData::getBreadcrumbData($breadcrumbs),
            'organization' => StructuredData::getOrganizationData()
        ];

        // 9. Charger la vue avec les données
        $this->view('recettes/recette', [
            'titlePage' => $recette['nom'] . " - Recette de cuisine - Recettes AI",
            'descriptionPage' => "Découvrez comment préparer " . $recette['nom'] . ". " . substr(strip_tags($recette['descriptif']), 0, 150) . "...",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "recette " . strtolower($recette['nom']) . ", cuisine, " . strtolower($recette['categorie']) . ", ingrédients, cuisson",
            'recette' => $recette,
            'etiquettes' => $etiquettes,
            'etiquettesIds' => $etiquettesIds,
            'isFavorite' => $isFavorite,
            'ingredientsList' => $ingredientsList,
            'uniteMesureList' => $uniteMesureList,
            'possedeListIngredients' => $possedeListIngredients,
            'structuredData' => $structuredData,
            'breadcrumbs' => $breadcrumbs // 🚀 AJOUT : Variable breadcrumbs
        ]);
    }

    /**
     * Affiche une recette spécifique
     * @return void
     */
    public function show() : void
    {
        // Rediriger vers la méthode recette() pour éviter la duplication
        $this->recette();
    }

}