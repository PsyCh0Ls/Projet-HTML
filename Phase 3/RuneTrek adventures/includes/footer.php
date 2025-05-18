<?php
?>
<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>À propos de RuneTrek</h3>
            <p>Votre portail vers les aventures fantastiques de Runeterra</p>
            <p>© <?php echo date('Y'); ?> RuneTrek Adventures. Tous droits réservés.</p>
        </div>
        <div class="footer-section">
            <h3>Destinations populaires</h3>
            <ul>
                <li><a href="search.php?region[]=piltover">Piltover & Zaun</a></li>
                <li><a href="search.php?region[]=demacia">Demacia</a></li>
                <li><a href="search.php?region[]=ionia">Ionia</a></li>
                <li><a href="search.php?region[]=freljord">Freljord</a></li>
                <li><a href="search.php?region[]=noxus">Noxus</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Liens utiles</h3>
            <ul>
                <li><a href="presentation.php">À propos de nous</a></li>
                <li><a href="search.php">Rechercher un voyage</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Mon profil</a></li>
                    <li><a href="cart.php">Mon panier</a></li>
                <?php else: ?>
                    <li><a href="login.php">Se connecter</a></li>
                    <li><a href="register.php">S'inscrire</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</footer>
</body>
</html>
