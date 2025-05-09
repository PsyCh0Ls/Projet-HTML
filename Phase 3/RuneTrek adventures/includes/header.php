<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RuneTrek Adventures</title>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/search.css">
    <link rel="stylesheet" href="styles/trip_details.css">
    <link rel="stylesheet" href="styles/profile.css">
    <link rel="stylesheet" href="styles/booking.css">
    <link rel="stylesheet" href="styles/auth.css">
    <link rel="stylesheet" href="styles/admin.css">
    <link rel="stylesheet" href="styles/edit_profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="logo">RuneTrek Adventures</div>
            <ul class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="search.php">Rechercher</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="?logout=1">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
            <div class="menu-toggle">☰</div>
        </nav>
    </header>
    <main>