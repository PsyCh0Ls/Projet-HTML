<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RuneTrek Adventures</title>
    <!-- Préchargeur de thème pour éviter le clignotement -->
    <script src="js/theme-loader.js"></script>
    <link rel="stylesheet" href="styles/runeTrek adventures.css">
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
        'payment.php' => 'payment.css'
    ];
    if (isset($css_files[$current_page])) {
        echo '<link rel="stylesheet" href="styles/' . $css_files[$current_page] . '">';
    }
    ?>
    <!-- Scripts JavaScript pour la phase 3 -->
    <script src="js/theme-switcher.js"></script>
    <script src="js/form-validator.js"></script>
    <script src="js/profile-editor.js"></script>
    <script src="js/admin-controller.js"></script>
    <script src="js/search-filter.js"></script>
    <script src="js/price-calculator.js"></script>
    
    <!-- Remplacer les multiples scripts de panier par notre script unifié -->
    <script src="js/cart-unified.js"></script>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php">Profil</a></li>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="admin.php">Administration</a></li>
                        <?php endif; ?>
                        <!-- Ajouter l'indicateur de panier ici pour les utilisateurs connectés -->
                        <li id="cart-indicator">
                            <a href="cart.php" id="cart-link">
                                🛒 Panier <span id="cart-count"><?php echo isset($_SESSION['cart']['count']) ? $_SESSION['cart']['count'] : '0'; ?></span>
                            </a>
                        </li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Connexion</a></li>
                        <li><a href="register.php" class="cta-button">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>