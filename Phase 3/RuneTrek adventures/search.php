<?php
session_start();
require_once 'includes/header.php';
$trips = require_once 'data/trips.json';
$filtered_trips = $trips['trips'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $region = $_GET['region'] ?? '';
    $min_price = $_GET['min_price'] ?? '';
    $max_price = $_GET['max_price'] ?? '';

    if (!empty($region)) {
        $filtered_trips = array_filter($filtered_trips, fn($trip) => $trip['region'] === $region);
    }
    if (!empty($min_price) && is_numeric($min_price)) {
        $filtered_trips = array_filter($filtered_trips, fn($trip) => $trip['base_price'] >= $min_price);
    }
    if (!empty($max_price) && is_numeric($max_price)) {
        $filtered_trips = array_filter($filtered_trips, fn($trip) => $trip['base_price'] <= $max_price);
    }
}
?>
    <div class="search-container">
        <div class="filters">
            <label for="region">Région :</label>
            <select id="region" name="region">
                <option value="">Toutes</option>
                <option value="Piltover" <?php echo isset($_GET['region']) && $_GET['region'] === 'Piltover' ? 'selected' : ''; ?>>Piltover</option>
                <option value="Demacia" <?php echo isset($_GET['region']) && $_GET['region'] === 'Demacia' ? 'selected' : ''; ?>>Demacia</option>
                <option value="Ionia" <?php echo isset($_GET['region']) && $_GET['region'] === 'Ionia' ? 'selected' : ''; ?>>Ionia</option>
            </select>
            <label for="min_price">Prix minimum :</label>
            <input type="number" id="min_price" name="min_price" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>" min="0">
            <label for="max_price">Prix maximum :</label>
            <input type="number" id="max_price" name="max_price" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>" min="0">
            <button onclick="window.location.href='search.php?'+new URLSearchParams(new FormData(document.querySelector('.filters'))).toString()">Filtrer</button>
        </div>
        <div class="trip-list">
            <?php foreach ($filtered_trips as $trip): ?>
                <div class="trip-item">
                    <h3><?php echo htmlspecialchars($trip['title']); ?></h3>
                    <p>Région : <?php echo htmlspecialchars($trip['region']); ?></p>
                    <p>Prix : <?php echo htmlspecialchars($trip['base_price']); ?> PO</p>
                    <p><a href="trip_details.php?id=<?php echo $trip['id']; ?>">Détails</a></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php require_once 'includes/footer.php'; ?>