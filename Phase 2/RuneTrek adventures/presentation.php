<?php
session_start();
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>
<main>
    <section class="quick-search">
        <div class="search-container">
            <h2>Trouver votre aventure</h2>
            <form class="search-form" action="search.php" method="GET">
                <div class="search-input-group">
                    <select name="region" required>
                        <option value="">Choisir une région</option>
                        <option value="piltover">Piltover</option>
                        <option value="demacia">Demacia</option>
                        <option value="ionia">Ionia</option>
                        <option value="freljord">Freljord</option>
                    </select>
                    <input type="date" name="start_date">
                    <button type="submit" class="search-button">Rechercher</button>
                </div>
            </form>
        </div>
    </section>
    <section class="about-section">
        <h1>À propos de RuneTrek Adventures</h1>
        <p class="lead-text">Découvrez l'univers de Runeterra comme jamais auparavant.</p>
        <div class="about-grid">
            <div class="about-content">
                <h2>Notre histoire</h2>
                <p>Fondée par des passionnés de League of Legends, RuneTrek offre des voyages immersifs.</p>
            </div>
            <div class="about-image history-image"></div>
        </div>
        <div class="about-grid reverse">
            <div class="about-content">
                <h2>Notre philosophie</h2>
                <p>Nous croyons en l'aventure, la découverte et le respect des cultures de Runeterra.</p>
            </div>
            <div class="about-image philosophy-image"></div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>