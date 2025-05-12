<?php
session_start();
require_once 'includes/functions.php';

$trips_data = read_json('data/trips.json');
$trips = $trips_data['trips'] ?? [];
$filtered_trips = $trips;
$regions = array_unique(array_column($trips, 'region'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Filtre par mots-clés
    if (!empty($_GET['keywords'])) {
        $keywords = strtolower(trim($_GET['keywords']));
        $filtered_trips = array_filter($filtered_trips, function($trip) use ($keywords) {
            return strpos(strtolower($trip['title']), $keywords) !== false ||
                   strpos(strtolower($trip['description']), $keywords) !== false;
        });
    }

    // Filtre par région
    if (!empty($_GET['region']) && is_array($_GET['region'])) {
        $selected_regions = array_map('strtolower', $_GET['region']);
        $filtered_trips = array_filter($filtered_trips, function($trip) use ($selected_regions) {
            return in_array(strtolower($trip['region']), $selected_regions);
        });
    }

    // Filtre par date
    if (!empty($_GET['start_date'])) {
        $start_date = $_GET['start_date'];
        if (DateTime::createFromFormat('Y-m-d', $start_date) !== false) {
            $filtered_trips = array_filter($filtered_trips, function($trip) use ($start_date) {
                return $trip['start_date'] >= $start_date;
            });
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="search-page">
    <main>
        <div class="filters">
            <h2>Filtres</h2>
            <form method="GET" class="search-form">
                <div class="filter-group">
                    <h3>Mots-clés</h3>
                    <input type="text" name="keywords" value="<?php echo isset($_GET['keywords']) ? htmlspecialchars($_GET['keywords']) : ''; ?>" placeholder="Rechercher un voyage...">
                </div>
                <div class="filter-group">
                    <h3>Régions</h3>
                    <?php foreach ($regions as $region): ?>
                        <label>
                            <input type="checkbox" name="region[]" value="<?php echo htmlspecialchars($region); ?>"
                                   <?php echo isset($_GET['region']) && in_array($region, $_GET['region']) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($region); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="filter-group">
                    <h3>Date de départ</h3>
                    <div class="date-inputs">
                        <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                    </div>
                </div>
                <button type="submit" class="apply-filters">Appliquer les filtres</button>
            </form>
        </div>
        <div class="results">
            <h2>Résultats</h2>
            <?php if (empty($filtered_trips)): ?>
                <p>Aucun voyage trouvé correspondant à vos critères.</p>
            <?php else: ?>
                <div class="trip-cards">
                    <?php foreach ($filtered_trips as $trip): ?>
                        <div class="trip-card">
                            <div class="trip-image <?php echo strtolower($trip['region']); ?>-bg"></div>
                            <div class="trip-content">
                                <h3><?php echo htmlspecialchars($trip['title']); ?></h3>
                                <p><?php echo htmlspecialchars($trip['description']); ?></p>
                                <div class="trip-footer">
                                    <span class="price"><?php echo htmlspecialchars($trip['price']); ?> PO</span>
                                    <a href="trip_details.php?id=<?php echo $trip['id']; ?>" class="view-details">Détails</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>