<?php
include 'includes/header.php';
?>

<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Explorez Runeterra</h1>
            <p>Découvrez des aventures épiques à travers les régions de Runeterra avec RuneTrek Adventures.</p>
            <a href="search.php" class="cta-button">Commencer l'aventure</a>
        </div>
    </section>

    <section class="featured-destinations">
        <h2>Destinations Populaires</h2>
        <div class="trip-card">
            <div class="piltover-bg"></div>
            <div class="trip-content">
                <h3>Piltover</h3>
                <p>La cité du progrès et de l'innovation.</p>
            </div>
            <div class="trip-footer">
                <span class="price">500 PO</span>
                <a href="booking.php?trip=piltover" class="view-details">Voir les détails</a>
            </div>
        </div>

        <div class="trip-card">
            <div class="demacia-bg"></div>
            <div class="trip-content">
                <h3>Demacia</h3>
                <p>Le royaume de la justice et de l'honneur.</p>
            </div>
            <div class="trip-footer">
                <span class="price">600 PO</span>
                <a href="booking.php?trip=demacia" class="view-details">Voir les détails</a>
            </div>
        </div>

        <div class="trip-card">
            <div class="freljord-bg"></div>
            <div class="trip-content">
                <h3>Freljord</h3>
                <p>Les terres gelées du nord.</p>
            </div>
            <div class="trip-footer">
                <span class="price">700 PO</span>
                <a href="booking.php?trip=freljord" class="view-details">Voir les détails</a>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>