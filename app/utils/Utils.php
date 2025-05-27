<?php
// app/Utils/Utils.php
class Utils {

    // alert() est une fonction qui génère un message d'alerte HTML
    public static function alert(string $message, string $type = "error"): string 
    {
        return "<div class='alert alert-{$type}' role='alert'>{$message}</div>";
    }

    // debug() est une fonction qui affiche le contenu d'une variable pour le débogage
    public static function debug($var) {
        echo "<pre class='border border-dark bg-light text-danger fw-bold w-50 p-5 mt-5'>";
        var_dump($var);
        echo "</pre>";
    }

    // sanitize() est une fonction qui échappe les caractères dangereux pour éviter les attaques XSS
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}