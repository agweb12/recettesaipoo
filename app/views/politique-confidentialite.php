<?php
require_once('../inc/functions.php');

$titlePage = "Politique de Confidentialité - Recettes AI";
$descriptionPage = "Découvrez la politique de confidentialité de Recettes AI, expliquant la collecte et l'utilisation de vos données personnelles.";
$indexPage = "index";
$followPage = "follow";
$keywordsPage = "politique de confidentialité, données personnelles, Recettes AI, vie privée, RGPD";

if(isLoggedIn()){
    $user = $_SESSION['user'];
}
require_once('header.php');
?>
<div class="heroIngredients">
  <div class="boxHeroIngredients">
    <h1>Politique de confidentialité</h1>
    <p>Dernière mise à jour : <?= date('d-m-Y')?></p>
  </div>
</div>

<div class="features">
  <p>Recettes AI respecte la vie privée de ses utilisateurs. Cette politique explique quelles données nous collectons, pourquoi et comment elles sont utilisées.</p>

<h3>1. Données collectées</h3>
<ul>
  <li>Nom, prénom, adresse email lors de l’inscription</li>
  <li>Données d’utilisation : listes d’ingrédients, recettes favorites</li>
</ul>

<h3>2. Utilisation des données</h3>
<p>Les données servent à personnaliser votre expérience, vous proposer des recettes adaptées, et améliorer notre service.</p>

<h3>3. Partage des données</h3>
<p>Aucune donnée personnelle n’est vendue ou partagée à des tiers sans consentement, sauf obligation légale.</p>

<h3>4. Durée de conservation</h3>
<p>Les données sont conservées tant que vous possédez un compte utilisateur.</p>

<h3>5. Vos droits</h3>
<p>Conformément au RGPD, vous pouvez accéder, corriger ou supprimer vos données en nous contactant à : contact@recettesai.fr</p>
</div>
<?php
require_once('footer.php');
?>