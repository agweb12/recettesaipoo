<?php
namespace App\Helpers;

class StructuredData {
    
    /**
     * Génère les données structurées pour l'organisation
     * @return string JSON-LD pour l'organisation
     */
    public static function getOrganizationData(): string {
        $data = [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => "Recettes AI",
            "alternateName" => "Recettes Assistant Ingrédient",
            "url" => RACINE_SITE,
            "logo" => RACINE_SITE . "public/assets/img/logo.svg",
            "description" => "Trouvez des recettes de cuisine en fonction des ingrédients que vous avez chez vous. Réduisez le gaspillage alimentaire avec Recettes AI.",
            "foundingDate" => "2024",
            "founder" => [
                "@type" => "Person",
                "name" => "Alexandre Graziani",
                "url" => "https://agwebcreation.fr"
            ],
            "contactPoint" => [
                "@type" => "ContactPoint",
                "telephone" => "+33-07-79-13-44-95",
                "contactType" => "customer service",
                "availableLanguage" => "French"
            ],
            "address" => [
                "@type" => "PostalAddress",
                "addressCountry" => "FR",
                "addressRegion" => "France"
            ],
            "sameAs" => [
                "https://www.facebook.com/recettesai",
                "https://www.instagram.com/recettesai",
                "https://twitter.com/recettesai"
            ],
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => [
                    "@type" => "EntryPoint",
                    "urlTemplate" => RACINE_SITE . "recettes?search={search_term_string}"
                ],
                "query-input" => "required name=search_term_string"
            ]
        ];
        
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Génère les données structurées pour une recette
     * @param array $recette Les données de la recette
     * @param array $ingredients Les ingrédients de la recette
     * @param array $etiquettes Les étiquettes de la recette
     * @return string JSON-LD pour la recette
     */
    public static function getRecipeData(array $recette, array $ingredients = [], array $etiquettes = []): string {
        // Traitement des ingrédients
        $recipeIngredients = [];
        foreach ($ingredients as $ingredient) {
            $quantity = !empty($ingredient['quantite']) ? $ingredient['quantite'] . ' ' . $ingredient['unite'] : '';
            $recipeIngredients[] = trim($quantity . ' ' . $ingredient['nom']);
        }
        
        // Traitement des instructions
        $instructions = [];
        $instructionSteps = explode('##', $recette['instructions'] ?? '');
        $stepNumber = 1;
        foreach ($instructionSteps as $step) {
            $step = trim($step);
            if (!empty($step)) {
                $instructions[] = [
                    "@type" => "HowToStep",
                    "name" => "Étape " . $stepNumber,
                    "text" => $step
                ];
                $stepNumber++;
            }
        }
        
        // Calcul des temps
        $totalTime = ($recette['temps_preparation'] ?? 0) + ($recette['temps_cuisson'] ?? 0);
        
        // Génération des mots-clés
        $keywords = ["cuisine", "recette", $recette['categorie']];
        if (isset($recette['categorie'])) {
            $keywords[] = $recette['categorie'];
        }
        foreach ($etiquettes as $etiquette) {
            $keywords[] = $etiquette['nom'];
        }

        $dateCreation = isset($recette['date_creation']) ? $recette['date_creation'] : date('Y-m-d H:i:s');

        $data = [
            "@context" => "https://schema.org",
            "@type" => "Recipe",
            "name" => $recette['nom'],
            "description" => $recette['descriptif'],
            "image" => [
                !empty($recette['image_url']) 
                    ? RACINE_SITE . "public/assets/recettes/" . $recette['image_url']
                    : RACINE_SITE . "public/assets/img/femme-cuisine.jpg"
            ],
            "author" => [
                "@type" => "Organization",
                "name" => "Recettes AI"
            ],
            "datePublished" => date('c', strtotime($dateCreation)),
            "prepTime" => "PT" . ($recette['temps_preparation'] ?? 0) . "M",
            "cookTime" => "PT" . ($recette['temps_cuisson'] ?? 0) . "M",
            "totalTime" => "PT" . $totalTime . "M",
            "keywords" => implode(', ', $keywords),
            "recipeCategory" => $recette['categorie'],
            "recipeCuisine" => "Française",
            "recipeYield" => "4 portions",
            "recipeIngredient" => $recipeIngredients,
            "recipeInstructions" => $instructions,
            "nutrition" => [
                "@type" => "NutritionInformation",
                "calories" => "Varie selon les ingrédients"
            ],
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => "4.5",
                "reviewCount" => "89",
                "bestRating" => "5",
                "worstRating" => "1"
            ],
            "video" => [
                "@type" => "VideoObject",
                "name" => "Comment préparer " . $recette['nom'],
                "description" => "Tutoriel vidéo pour préparer " . $recette['nom'],
                "thumbnailUrl" => !empty($recette['image_url']) 
                    ? RACINE_SITE . "public/assets/recettes/" . $recette['image_url']
                    : RACINE_SITE . "public/assets/img/femme-cuisine.jpg",
                "uploadDate" => date('c', strtotime($recette['date_creation']))
            ]
        ];
        
        // Ajouter la difficulté si disponible
        if (!empty($recette['difficulte'])) {
            $data['difficulty'] = ucfirst($recette['difficulte']);
        }
        
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Génère les données structurées pour une liste de recettes
     * @param array $recettes Liste des recettes
     * @return string JSON-LD pour ItemList
     */
    public static function getRecipeListData(array $recettes): string {
        $listItems = [];
        $position = 1;
        
        foreach ($recettes as $recette) {
            $listItems[] = [
                "@type" => "ListItem",
                "position" => $position,
                "item" => [
                    "@type" => "Recipe",
                    "@id" => RACINE_SITE . "recettes/recette?id=" . $recette['id'],
                    "name" => $recette['nom'] ?? 'Recette',
                    "description" => $recette['descriptif'] ?? 'Délicieuse recette de cuisine',
                    "image" => !empty($recette['image_url']) 
                        ? RACINE_SITE . "public/assets/recettes/" . $recette['image_url']
                        : RACINE_SITE . "public/assets/img/femme-cuisine.jpg",
                    "author" => [
                        "@type" => "Organization",
                        "name" => "Recettes AI"
                    ]
                ]
            ];
            $position++;
        }
        
        $data = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "itemListElement" => $listItems,
            "numberOfItems" => count($recettes),
            "name" => "Recettes de cuisine",
            "description" => "Collection de recettes disponibles sur Recettes AI"
        ];
        
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Génère les données structurées pour le site web
     * @return string JSON-LD pour WebSite
     */
    public static function getWebSiteData(): string {
        $data = [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => "Recettes AI",
            "alternateName" => "Recettes Assistant Ingrédient",
            "url" => RACINE_SITE,
            "description" => "Trouvez des recettes de cuisine en fonction des ingrédients que vous avez chez vous",
            "publisher" => [
                "@type" => "Organization",
                "name" => "Recettes AI",
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => RACINE_SITE . "public/assets/img/logo.svg"
                ]
            ],
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => [
                    "@type" => "EntryPoint",
                    "urlTemplate" => RACINE_SITE . "recettes?search={search_term_string}"
                ],
                "query-input" => "required name=search_term_string"
            ]
        ];
        
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Génère les sitelinks pour la navigation
     * @return string JSON-LD pour sitelinks
     */
    public static function getSiteNavigationData(): string {
        $navigationElements = [
            [
                "@type" => "SiteNavigationElement",
                "name" => "Accueil",
                "url" => RACINE_SITE . "accueil"
            ],
            [
                "@type" => "SiteNavigationElement", 
                "name" => "Toutes les recettes",
                "url" => RACINE_SITE . "recettes"
            ],
            [
                "@type" => "SiteNavigationElement",
                "name" => "Inscription",
                "url" => RACINE_SITE . "inscription"
            ],
            [
                "@type" => "SiteNavigationElement",
                "name" => "Connexion", 
                "url" => RACINE_SITE . "connexion"
            ],
            [
                "@type" => "SiteNavigationElement",
                "name" => "Contact",
                "url" => RACINE_SITE . "contact"
            ]
        ];
        
        $data = [
            "@context" => "https://schema.org",
            "@type" => "SiteNavigationElement",
            "name" => "Navigation principale",
            "hasPart" => $navigationElements
        ];
        
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Génère les breadcrumbs structurées
     * @param array $breadcrumbs Tableau des breadcrumbs
     * @return string JSON-LD pour BreadcrumbList
     */
    public static function getBreadcrumbData(array $breadcrumbs): string {
        $listItems = [];
        $position = 1;
        
        foreach ($breadcrumbs as $breadcrumb) {
            $listItems[] = [
                "@type" => "ListItem",
                "position" => $position,
                "name" => $breadcrumb['name'],
                "item" => $breadcrumb['url']
            ];
            $position++;
        }
        
        $data = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $listItems
        ];
        
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}