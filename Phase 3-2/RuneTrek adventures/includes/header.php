<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RuneTrek Adventures</title>
    <link rel="stylesheet" href="styles/runeTrek adventures.css">
    <link id="theme-style" rel="stylesheet" href="styles/runeTrek adventures.css">
    <script src="scripts/theme.js" defer></script>
    <?php
    // Inclure le CSS spécifique en fonction de la page
    $current_page = basename($_SERVER['PHP_SELF']);
    $css_files = [
        'index.php' => 'home.css',
        'presentation.php' => 'presentation.css',
        'search.php' => 'search.css',
        'login.php' => 'login.css',
        'register.php' => 'register.css',
        'profile.php' => 'profile.css',
        'admin.php' => 'admin.css',
        'trip_details.php' => 'trip_details.css',
        'trip_summary.php' => 'trip_summary.css',
        'payment.php' => 'payment.css',
        'cart.php' => 'cart.css'
    ];
    if (isset($css_files[$current_page])) {
        echo '<link rel="stylesheet" href="styles/' . $css_files[$current_page] . '">';
    }
    ?>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">
                    <h1>RuneTrek Adventures</h1>
                </a>
            </div>
            <nav class="main-nav">
    <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="presentation.php">Destinations</a></li>
        <li><a href="search.php">Recherche</a></li>
        <?php if (is_authenticated()): ?>
            <li><a href="profile.php">Profil</a></li>
            <li><a href="cart.php">Panier</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="login.php">Connexion</a></li>
            <li><a href="register.php" class="cta-button">S'inscrire</a></li>
        <?php endif; ?>
        <li><button id="theme-toggle" class="theme-button">🌓</button></li>
    </ul>
</nav>
        </div>
    </header>