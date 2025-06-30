<?php
require_once '../app/config/config.php';
require_once '../app/helpers/SEO.php';

use App\Helpers\SEO;
use App\Core\Database;

header('Content-Type: application/xml; charset=utf-8');

echo SEO::generateSitemap();

// Ajouter dynamiquement les recettes
try {
    $db = Database::getInstance()->getPdo();
    $stmt = $db->query("SELECT id, nom, date_creation FROM recette ORDER BY date_creation DESC LIMIT 1000");
    $recettes = $stmt->fetchAll();
    
    $xml = '';
    foreach ($recettes as $recette) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . RACINE_SITE . "recettes/recette?id=" . $recette['id'] . "</loc>\n";
        $xml .= "    <changefreq>weekly</changefreq>\n";
        $xml .= "    <priority>0.8</priority>\n";
        $xml .= "    <lastmod>" . date('Y-m-d', strtotime($recette['date_creation'])) . "</lastmod>\n";
        $xml .= "  </url>\n";
    }
    
    echo str_replace('</urlset>', $xml . '</urlset>', '');
} catch (Exception $e) {
    // En cas d'erreur, on affiche juste le sitemap de base
    echo SEO::generateSitemap();
    error_log('Erreur lors de la génération du sitemap des recettes : ' . $e->getMessage());
}