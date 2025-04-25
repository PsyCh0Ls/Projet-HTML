<?php
session_start();
require_once 'includes/functions.php';

$trips = read_json('data/trips.json')['trips'];
$filtered_trips = $trips;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['region'])) {
        $region = strtolower($_GET['region']);
        $filtered_trips = array_filter($trips, function($trip) use ($region) {
            return strtolower($trip['region']) === $region;
        });
    }
    if (!empty($_GET['start_date'])) {
        $start_date = $_GET['start_date'];
        $filtered_trips = array_filter($filtered_trips, function($trip) use ($start_date) {
            return $trip['start_date'] >= $start_date;
        });
    }
}
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6;
$total_trips = count($filtered_trips);
$total_pages = ceil($total_trips / $per_page);
$offset = ($page - 1) * $per_page;
$paginated_trips = array_slice($filtered_trips, $offset, $per_page);
?>
<?php include 'includes/header.php'; ?>
<main class="search-page">
    <div class="search-container">
        <aside class="search-filters">
            <h2>Filtres</h2>
            <form method="GET">
                <div class="filter-group">
                    <h3>Région</h3>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="region[]" value="piltover"> Piltover</label>
                        <label><input type="checkbox" name="region[]" value="demacia"> Demacia</label>
                        <label><input type="checkbox" name="region[]" value="ionia"> Ionia</label>
                        <label><input type="checkbox" name="region[]" value="freljord"> Freljord</label>
                    </div>
                </div>
                <div class="filter-group">
                    <h3>Date de départ</h3>
                    <div class="date-inputs">
                        <input type="date" name="start_date">
                    </div>
                </div>
                <button type="submit" class="apply-filters">Appliquer</button>
            </form>
        </aside>
        <section class="search-results">
            <div class="results-header">
                <h2><?php echo $total_trips; ?> voyage(s) trouvé(s)</h2>
                <div class="sort-options">
                    <select name="sort">
                        <option value="price">Prix</option>
                        <option value="duration">Durée</option>
                    </select>
                </div>
            </div>
            <div class="trip-cards">
                <?php foreach ($paginated_trips as $trip): ?>
                    <div class="trip-card">
                        <div class="trip-image <?php echo strtolower($trip['region']); ?>-bg"></div>
                        <div class="trip-content">
                            <h3><?php echo htmlspecialchars($trip['title']); ?></h3>
                            <p class="trip-description"><?php echo htmlspecialchars($trip['description']); ?></p>
                            <div class="trip-details">
                                <span><?php echo $trip['duration']; ?> jours</span>
                                <span><?php echo $trip['start_date']; ?></span>
                            </div>
                            <div class="trip-footer">
                                <span class="price"><?php echo $trip['price']; ?> PO</span>
                                <a href="trip_details.php?id=<?php echo $trip['id']; ?>" class="view-details">Détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo isset($_GET['region']) ? '&region=' . $_GET['region'] : ''; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                </div>
            </div>
        </section>
    </div>
</main>
<?php include 'includes/footer.php'; ?>