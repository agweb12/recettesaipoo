<?php
namespace App\Helpers;

class SEO {
    
    /**
     * Génère un sitemap XML basique
     * @return string XML du sitemap
     */
    public static function generateSitemap(): string {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Pages principales
        $pages = [
            [
                'loc' => RACINE_SITE . 'accueil',
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'loc' => RACINE_SITE . 'recettes',
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'loc' => RACINE_SITE . 'inscription',
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => RACINE_SITE . 'connexion',
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            [
                'loc' => RACINE_SITE . 'contact',
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ]
        ];
        
        foreach ($pages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($page['loc']) . "</loc>\n";
            $xml .= "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $page['priority'] . "</priority>\n";
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        return $xml;
    }
    
    /**
     * Génère un robots.txt
     * @return string Contenu du robots.txt
     */
    public static function generateRobotsTxt(): string {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: /app/\n";
        $content .= "\n";
        $content .= "Sitemap: " . RACINE_SITE . "sitemap.xml\n";
        
        return $content;
    }
    
    /**
     * Optimise le titre pour le SEO
     * @param string $title Titre de base
     * @param string $siteName Nom du site
     * @return string Titre optimisé
     */
    public static function optimizeTitle(string $title, string $siteName = "Recettes AI"): string {
        // Limite à 60 caractères pour Google
        $maxLength = 60 - strlen(" - " . $siteName);
        
        if (strlen($title) > $maxLength) {
            $title = substr($title, 0, $maxLength - 3) . "...";
        }
        
        return $title . " - " . $siteName;
    }
    
    /**
     * Optimise la description pour le SEO
     * @param string $description Description de base
     * @return string Description optimisée
     */
    public static function optimizeDescription(string $description): string {
        // Limite à 160 caractères pour Google
        $maxLength = 160;
        
        if (strlen($description) > $maxLength) {
            $description = substr($description, 0, $maxLength - 3) . "...";
        }
        
        return $description;
    }
}