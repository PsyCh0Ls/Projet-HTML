<?php
session_start();
require_once 'includes/functions.php';

$data = read_json('data/trips.json');
$trips = isset($data['trips']) && is_array($data['trips']) ? $data['trips'] : [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: search.php');
    exit;
}

$trip_id = (int)$_GET['id'];
$trip = null;

// Recherche du voyage correspondant à l'ID
foreach ($trips as $t) {
    if ($t['id'] == $trip_id) {
        $trip = $t;
        break;
    }
}

if (!$trip) {
    header('Location: search.php');
    exit;
}

// Préparer les variables pour la closure
$region = isset($_GET['region']) ? $_GET['region'] : $trip['region']; // Utiliser la région du voyage si $_GET['region'] n'est pas défini
$trip_id_for_filter = $trip_id;

// Filtrer les voyages liés (même région, ID différent)
$related_trips = array_filter($trips, function($t) use ($region, $trip_id_for_filter) {
    return strtolower($t['region']) == strtolower($region) && $t['id'] != $trip_id_for_filter;
});
?>
<?php include 'includes/header.php'; ?>
<div class="trip-details-page">
    <main>
        <section class="trip-details">
            <h1><?php echo htmlspecialchars($trip['title']); ?></h1>
            <div class="trip-image <?php echo strtolower($trip['region']); ?>-bg"></div>
            <div class="trip-content">
                <p><?php echo htmlspecialchars($trip['description']); ?></p>
                <div class="trip-info">
                    <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
                    <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
                    <p><strong>Durée:</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
                    <p><strong>Prix:</strong> <?php echo htmlspecialchars($trip['price']); ?> PO</p>
                </div>
                <a href="booking.php?id=<?php echo $trip['id']; ?>" class="book-now">Réserver</a>
            </div>
        </section>
        <section class="related-trips">
            <h2>Autres voyages dans cette région</h2>
            <div class="trip-cards">
                <?php if (empty($related_trips)): ?>
                    <p>Aucun autre voyage disponible dans cette région.</p>
                <?php else: ?>
                    <?php foreach ($related_trips as $related_trip): ?>
                        <div class="trip-card">
                            <div class="trip-image <?php echo strtolower($related_trip['region']); ?>-bg"></div>
                            <div class="trip-content">
                                <h3><?php echo htmlspecialchars($related_trip['title']); ?></h3>
                                <p><?php echo htmlspecialchars($related_trip['description']); ?></p>
                                <div class="trip-footer">
                                    <span class="price"><?php echo htmlspecialchars($related_trip['price']); ?> PO</span>
                                    <a href="trip_details.php?id=<?php echo $related_trip['id']; ?>®ion=<?php echo urlencode($related_trip['region']); ?>" class="view-details">Détails</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
<?php include 'includes/footer.php'; ?>
