<?php
session_start();
require_once 'includes/functions.php';

$trips = read_json('data/trips.json')['trips'];
$featured_trips = array_slice($trips, 0, 3); // 3 voyages phares
?>
<?php include 'includes/header.php'; ?>
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Explorez Runeterra avec RuneTrek</h1>
            <p>Découvrez des aventures épiques dans les contrées de League of Legends.</p>
            <a href="presentation.php" class="cta-button-large">Commencer l'aventure</a>
        </div>
    </section>
    <section class="featured-destinations">
        <h2>Destinations Phares</h2>
        <div class="destination-grid">
            <?php foreach ($featured_trips as $trip): ?>
                <div class="destination-card <?php echo strtolower($trip['region']); ?>">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($trip['title']); ?></h3>
                        <p><?php echo htmlspecialchars($trip['description']); ?></p>
                        <a href="trip_details.php?id=<?php echo $trip['id']; ?>" class="destination-link">Découvrir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>