<?php
include 'includes/header.php';
?>

<main>
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <h1>Explorez Runeterra avec RuneTrek</h1>
            <p>Découvrez des aventures épiques dans les contrées de League of Legends.</p>
            <a href="search.php" class="cta-button">Commencer l'aventure</a>
        </div>
    </section>

    <section class="featured-destinations">
        <h2>Destinations Phares</h2>
        <div class="trip-card">
            <div class="piltover-bg"></div>
            <div class="trip-content">
                <h3>Exploration de Piltover</h3>
                <p>Découvrez les merveilles technologiques de Piltover.</p>
            </div>
            <div class="trip-footer">
                <span class="price">500 PO</span>
                <a href="booking.php?trip=piltover" class="view-details">Découvrir</a>
            </div>
        </div>

        <div class="trip-card">
            <div class="demacia-bg"></div>
            <div class="trip-content">
                <h3>Découverte de Demacia</h3>
                <p>Explorez la gloire et l’honneur de Demacia.</p>
            </div>
            <div class="trip-footer">
                <span class="price">600 PO</span>
                <a href="booking.php?trip=demacia" class="view-details">Découvrir</a>
            </div>
        </div>

        <div class="trip-card">
            <div class="freljord-bg"></div>
            <div class="trip-content">
                <h3>Aventure dans le Freljord</h3>
                <p>Partez à la conquête des terres gelées.</p>
            </div>
            <div class="trip-footer">
                <span class="price">700 PO</span>
                <a href="booking.php?trip=freljord" class="view-details">Découvrir</a>
            </div>
        </div>

        <div class="trip-card">
            <div class="ionia-bg"></div>
            <div class="trip-content">
                <h3>Voyage à Ionia</h3>
                <p>Découvrez la sérénité spirituelle d’Ionia.</p>
            </div>
            <div class="trip-footer">
                <span class="price">550 PO</span>
                <a href="booking.php?trip=ionia" class="view-details">Découvrir</a>
            </div>
        </div>

        <div class="trip-card">
            <div class="noxus-bg"></div>
            <div class="trip-content">
                <h3>Expédition à Noxus</h3>
                <p>Vivez la puissance impériale de Noxus.</p>
            </div>
            <div class="trip-footer">
                <span class="price">650 PO</span>
                <a href="booking.php?trip=noxus" class="view-details">Découvrir</a>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>