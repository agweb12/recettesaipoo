<?php

/**
 * Génère un message d'alerte HTML
 *
 * @param string $message Le message à afficher
 * @param string $type Le type d'alerte (error, success, info, warning)
 * @return string Le HTML de l'alerte
 */
function alert(string $message, string $type = "error"): string 
{
    return \App\Utils\Utils::alert($message, $type);
}

/**
 * Affiche le contenu d'une variable pour le débogage
 *
 * @param mixed $var La variable à déboguer
 */
function debug($var) 
{
    \App\Utils\Utils::debug($var);
}

/**
 * Échappe les caractères dangereux pour éviter les attaques XSS
 *
 * @param string $input Le texte à assainir
 * @return string Le texte assaini
 */
function sanitize($input) 
{
    return \App\Utils\Utils::sanitize($input);
}


/**
 * Génère un champ input hidden CSRF
 * @return string Le HTML du champ CSRF
 */
function csrf_field(): string {
    return \App\Core\CSRF::getTokenField();
}

/**
 * Génère le token CSRF uniquement
 * @return string Le token CSRF
 */
function csrf_token(): string {
    return \App\Core\CSRF::generateToken();
}