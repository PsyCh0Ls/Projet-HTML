<?php
session_start();
require_once 'includes/header.php';
?>
    <section class="hero">
        <div>
            <h1>Bienvenue à RuneTrek Adventures</h1>
            <p>Explorez les terres magiques de Runeterra avec des voyages uniques.</p>
        </div>
    </section>
    <section class="destinations">
        <h2>Destinations Populaires</h2>
        <?php
        $trips = require_once 'data/trips.json';
        foreach ($trips['trips'] as $trip) {
            echo "<div class='destination-card {$trip['region']}'>";
            echo "<h3>{$trip['title']}</h3>";
            echo "</div>";
        }
        ?>
    </section>
<?php require_once 'includes/footer.php'; ?>