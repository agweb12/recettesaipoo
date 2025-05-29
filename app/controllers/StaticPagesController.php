<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Contrôleur pour les pages statiques du site
 */
class StaticPagesController extends Controller {

    /**
     * Affiche la page de contact
     */
    public function contact() {
        $this->view('contact', [
            'titlePage' => "Contactez-nous",
            'descriptionPage' => "Contactez-nous pour toute question ou demande d'information concernant nos services de recettes AI.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "Recettes AI, recette, ai, intelligence artificielle, cuisine, ingrédients, recettes, trouver une recette"
        ]);
    }

    /**
     * Affiche la page des mentions légales
     */
    public function mentionsLegales() {
        $this->view('mentions-legales', [
            'titlePage' => "Mentions Légales - Recettes AI",
            'descriptionPage' => "Consultez les mentions légales de Recettes AI pour connaître l'éditeur, l'hébergeur et les conditions d'utilisation du site.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "mentions légales, éditeur, hébergeur, Recettes AI, conditions d'utilisation"
        ]);
    }

    /**
     * Affiche la page de politique de confidentialité
     */
    public function politiqueConfidentialite() {
        $this->view('politique-confidentialite', [
            'titlePage' => "Politique de Confidentialité - Recettes AI",
            'descriptionPage' => "Découvrez la politique de confidentialité de Recettes AI, expliquant la collecte et l'utilisation de vos données personnelles.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "politique de confidentialité, données personnelles, Recettes AI, vie privée, RGPD"
        ]);
    }

    /**
     * Affiche la page des conditions générales d'utilisation
     */
    public function cgu() {
        $this->view('cgu', [
            'titlePage' => "CGU - Recettes AI",
            'descriptionPage' => "Consultez les Conditions Générales d'Utilisation de Recettes AI pour comprendre vos droits et obligations lors de l'utilisation de notre site.",
            'indexPage' => "index",
            'followPage' => "follow",
            'keywordsPage' => "CGU, conditions générales d'utilisation, Recettes AI, utilisation du site, droits et obligations"
        ]);
    }
}