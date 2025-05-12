<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RuneTrek Adventures</title>
    <!-- Balise dynamique pour le thème -->
    <link id="theme-style" rel="stylesheet" href="styles/runeTrek adventures.css">
    <script src="scripts/theme.js" defer></script>
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
                    <li><a href="contact.php">Contact</a></li>
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