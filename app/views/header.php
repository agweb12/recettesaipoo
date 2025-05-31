<!DOCTYPE html>
<html lang="fr">
<!-- Head -->
<head>
    <!-- Encodage -->
    <meta charset="UTF-8" />
    <!-- Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= $descriptionPage ?>" />
    <!-- Meta Tags -->
    <meta name="robots" content="<?= $indexPage ?>, <?= $followPage ?>">
    <meta name="keywords" content="<?= $keywordsPage ?>" />
    <meta name="author" content="Recettes AI">
    <meta name="application-name" content="Recettes AI">
    <meta name="racine-site" content="<?= RACINE_SITE ?>">
    <title><?= $titlePage ?></title>

    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="Recettes AI" />
    <meta property="og:description" content="<?= $descriptionPage ?>" />
    <meta property="og:image" content="<?= RACINE_SITE ?>public/assets/img/logo.svg" />
    <meta property="og:site_name" content="Recettes AI" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= RACINE_SITE ?>" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@recettesai">
    <meta name="twitter:creator" content="@recettesai">
    <meta name="twitter:title" content="Recettes AI">
    <meta name="twitter:description" content="<?= $descriptionPage ?>">
    <meta name="twitter:image" content="<?= RACINE_SITE ?>public/assets/img/logo.svg">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= RACINE_SITE ?>public/assets/css/root.css" />
    <link rel="stylesheet" href="<?= RACINE_SITE ?>public/assets/css/style.css" />

    <link rel="image_src" href="<?= RACINE_SITE ?>public/assets/img/logo.svg">
    <link rel="canonical" href="<?= RACINE_SITE ?>">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= RACINE_SITE ?>favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="57x57" href="<?= RACINE_SITE ?>apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= RACINE_SITE ?>apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= RACINE_SITE ?>apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= RACINE_SITE ?>apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= RACINE_SITE ?>apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= RACINE_SITE ?>apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= RACINE_SITE ?>apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= RACINE_SITE ?>apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= RACINE_SITE ?>apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="36x36" href="<?= RACINE_SITE ?>android-icon-36x36.png">
    <link rel="icon" type="image/png" sizes="48x48" href="<?= RACINE_SITE ?>android-icon-48x48.png">
    <link rel="icon" type="image/png" sizes="72x72" href="<?= RACINE_SITE ?>android-icon-72x72.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= RACINE_SITE ?>android-icon-96x96.png">
    <link rel="icon" type="image/png" sizes="144x144" href="<?= RACINE_SITE ?>android-icon-144x144.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= RACINE_SITE ?>android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= RACINE_SITE ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= RACINE_SITE ?>favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= RACINE_SITE ?>favicon-16x16.png">
    <link rel="manifest" href="<?= RACINE_SITE ?>manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= RACINE_SITE ?>ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="<?= RACINE_SITE ?>public/assets/icons/webfonts/uicons-solid-rounded.css" rel="stylesheet">
    <link href="<?= RACINE_SITE ?>public/assets/icons/webfonts/uicons-brands.css" rel="stylesheet">
    <link href="<?= RACINE_SITE ?>public/assets/icons/webfonts/uicons-regular-rounded.css" rel="stylesheet">

</head>
<body>
<header>
    <?php
    function is_active($pageName) {
        // Récupérer l'URL actuelle
        $currentUrl = $_SERVER['REQUEST_URI']; // Par exemple, "/recettesaipoo/recettes/pizza-margherita?param=value"
        
        // Nettoyer l'URL des paramètres de requête et récupérer uniquement le chemin
        $path = parse_url($currentUrl, PHP_URL_PATH); // Par exemple, "/recettesaipoo/recettes/pizza-margherita"
        
        // Enlever le chemin de base (par exemple "/recettesaipoo/")
        $basePath = parse_url(RACINE_SITE, PHP_URL_PATH); // parse_url pour obtenir le chemin de base, par exemple "/recettesaipoo/"
        $cleanedPath = str_replace($basePath, '', $path); // str_replace pour enlever le chemin de base, par exemple "recettes/pizza-margherita"
        
        // Suppression des slashes au début et à la fin
        $cleanedPath = trim($cleanedPath, '/'); // Par exemple, "recettes/pizza-margherita"
        
        // Cas spéciaux
        if ($pageName === 'accueil' && ($cleanedPath === '' || $cleanedPath === 'accueil')) {
            return true;
        }
        
        if ($pageName === 'monCompte' && strpos($cleanedPath, 'profil/monCompte') === 0) {
            return true;
        }
        
        if ($pageName === 'recettes' && (strpos($cleanedPath, 'recettes') === 0)) {
            return true;
        }
        
        if ($pageName === 'contact' && $cleanedPath === 'contact') {
            return true;
        }
        
        if ($pageName === 'connexion' && $cleanedPath === 'connexion') {
            return true;
        }
        
        if ($pageName === 'inscription' && $cleanedPath === 'inscription') {
            return true;
        }
        
        return false;
    }
?>
    <!-- Menu Application Recettes AI -->
    <nav class="menu">
        <a href="<?= RACINE_SITE ?>accueil" class="imgMenu"><img src="<?= RACINE_SITE ?>public/assets/img/logo-white.webp" alt="Logo Recettes AI"></a>
        <div class="linkNav">
            <a href="<?= RACINE_SITE ?>recettes" class="<?= is_active('recettes') ? 'active' : '' ?>"><i class="fi fi-sr-utensils"></i> <span>Recettes</span></a>
            <a href="<?= RACINE_SITE ?>contact" class="<?= is_active('contact') ? 'active' : '' ?>"><i class="fi fi-sr-headset"></i> <span>Contact</span></a>
            <button id="theme-toggle" class="theme-toggle" aria-label="Basculer entre mode clair et sombre">
                <i class="fi fi-sr-moon"></i>
            </button>
        </div>
        <div class="ctaButtons">
            <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
                <a href="<?= RACINE_SITE ?>profil/monCompte?id=<?= $user['id'] ?>" class="<?= is_active('monCompte') ? 'active' : '' ?>"><i class="fi fi-sr-user-trust"></i> <span>Mon Compte</span></a>
                <a href="<?= RACINE_SITE ?>deconnexion" class="cta"><i class="fi fi-sr-sign-out-alt"></i> <span>Déconnexion</span></a>
            <?php else: ?>
                <a href="<?= RACINE_SITE ?>inscription" class="cta<?= is_active('inscription') ? ' active' : '' ?>"><i class="fi fi-sr-sign-up"></i> <span>S'inscrire</span></a>
                <a href="<?= RACINE_SITE ?>connexion" class="cta<?= is_active('connexion') ? ' active' : '' ?>"><i class="fi fi-sr-enter"></i> <span>Se Connecter</span></a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<main>