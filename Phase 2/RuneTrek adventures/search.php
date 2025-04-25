<?php
session_start();
require_once 'includes/functions.php';

$data = read_json('data/trips.json');
$trips = isset($data['trips']) && is_array($data['trips']) ? $data['trips'] : [];
$filtered_trips = $trips;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Filtre par région (plusieurs régions possibles)
    if (!empty($_GET['region']) && is_array($_GET['region'])) {
        $selected_regions = array_map('strtolower', $_GET['region']);
        $filtered_trips = array_filter($trips, function($trip) use ($selected_regions) {
            return in_array(strtolower($trip['region']), $selected_regions);
        });
    }

    // Filtre par date de départ
    if (!empty($_GET['start_date'])) {
        $start_date = $_GET['start_date'];
        // Valider la date
        if (DateTime::createFromFormat('Y-m-d', $start_date) !== false) {
            $filtered_trips = array_filter($filtered_trips, function($trip) use ($start_date) {
                return $trip['start_date'] >= $start_date;
            });
        }
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
                        <label><input type="checkbox" name="region[]" value="piltover" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('piltover', $_GET['region']) ? 'checked' : ''; ?>> Piltover</label>
                        <label><input type="checkbox" name="region[]" value="demacia" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('demacia', $_GET['region']) ? 'checked' : ''; ?>> Demacia</label>
                        <label><input type="checkbox" name="region[]" value="ionia" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('ionia', $_GET['region']) ? 'checked' : ''; ?>> Ionia</label>
                        <label><input type="checkbox" name="region[]" value="freljord" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('freljord', $_GET['region']) ? 'checked' : ''; ?>> Freljord</label>
                        <label><input type="checkbox" name="region[]" value="zaun" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('zaun', $_GET['region']) ? 'checked' : ''; ?>> Zaun</label>
                        <label><input type="checkbox" name="region[]" value="noxus" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('noxus', $_GET['region']) ? 'checked' : ''; ?>> Noxus</label>
                        <label><input type="checkbox" name="region[]" value="bilgewater" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('bilgewater', $_GET['region']) ? 'checked' : ''; ?>> Bilgewater</label>
                        <label><input type="checkbox" name="region[]" value="shurima" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('shurima', $_GET['region']) ? 'checked' : ''; ?>> Shurima</label>
                        <label><input type="checkbox" name="region[]" value="targon" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('targon', $_GET['region']) ? 'checked' : ''; ?>> Targon</label>
                        <label><input type="checkbox" name="region[]" value="îles obscures" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('îles obscures', $_GET['region']) ? 'checked' : ''; ?>> Îles Obscures</label>
                        <label><input type="checkbox" name="region[]" value="bandle city" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('bandle city', $_GET['region']) ? 'checked' : ''; ?>> Bandle City</label>
                        <label><input type="checkbox" name="region[]" value="ixtal" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('ixtal', $_GET['region']) ? 'checked' : ''; ?>> Ixtal</label>
                        <label><input type="checkbox" name="region[]" value="faille de l'invocateur" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('faille de l\'invocateur', $_GET['region']) ? 'checked' : ''; ?>> Faille de l’Invocateur</label>
                        <label><input type="checkbox" name="region[]" value="icathia" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('icathia', $_GET['region']) ? 'checked' : ''; ?>> Icathia</label>
                        <label><input type="checkbox" name="region[]" value="runeterra" <?php echo !empty($_GET['region']) && is_array($_GET['region']) && in_array('runeterra', $_GET['region']) ? 'checked' : ''; ?>> Runeterra</label>
                    </div>
                </div>
                <div class="filter-group">
                    <h3>Date de départ</h3>
                    <div class="date-inputs">
                        <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
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
                <?php if (empty($paginated_trips)): ?>
                    <p>Aucun voyage ne correspond à vos critères.</p>
                <?php else: ?>
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
                <?php endif; ?>
            </div>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($_GET['region']) ? '&region=' . implode(',', $_GET['region']) : ''; ?><?php echo !empty($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : ''; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </section>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
