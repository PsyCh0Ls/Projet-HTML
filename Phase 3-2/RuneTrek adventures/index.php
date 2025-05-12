<?php
include 'includes/header.php';
?>

<main>
    <section class="hero">
        <h2>Découvrez Runeterra</h2>
        <p>Explorez les régions les plus emblématiques et vivez des aventures inoubliables !</p>
    </section>
    <section class="featured-destinations">
        <h2>Destinations Populaires</h2>
        <div class="trip-card">
            <div class="piltover-bg"></div>
            <div class="trip-content">
                <h3>Exploration de Piltover</h3>
                <p>Plongez dans la technologie et l'innovation de cette ville futuriste.</p>
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
                <p>Vivez l'honneur et la justice dans cette terre de lumière.</p>
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
                <p>Découvrez les terres gelées et les tribus sauvages.</p>
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
                <p>Explorez la spiritualité et les paysages sereins.</p>
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
                <p>Vivez la puissance et la stratégie de cette nation guerrière.</p>
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