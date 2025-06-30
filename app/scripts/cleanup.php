<?php
// Script de nettoyage automatique à exécuter via cron

require_once dirname(__DIR__, 2) . '/public/index.php';

use App\Core\RateLimiter;

// Nettoyer les anciennes tentatives
$cleaned = RateLimiter::cleanOldAttempts();

if ($cleaned) {
    echo "Nettoyage des tentatives de connexion effectué avec succès.\n";
    error_log("Nettoyage automatique des tentatives de connexion effectué");
} else {
    echo "Erreur lors du nettoyage des tentatives de connexion.\n";
    error_log("Erreur lors du nettoyage automatique des tentatives de connexion");
}