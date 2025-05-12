<?php
include 'includes/header.php';

// Liste des destinations (simulée, pourrait venir d'une base de données)
$destinations = [
    ['name' => 'Exploration de Piltover', 'price' => 500, 'region' => 'Piltover', 'bg_class' => 'piltover-bg'],
    ['name' => 'Découverte de Demacia', 'price' => 600, 'region' => 'Demacia', 'bg_class' => 'demacia-bg'],
    ['name' => 'Aventure dans le Freljord', 'price' => 700, 'region' => 'Freljord', 'bg_class' => 'freljord-bg'],
    ['name' => 'Voyage à Ionia', 'price' => 550, 'region' => 'Ionia', 'bg_class' => 'ionia-bg'],
    ['name' => 'Expédition à Noxus', 'price' => 650, 'region' => 'Noxus', 'bg_class' => 'noxus-bg'],
];

// Récupérer les paramètres de tri et de filtrage
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price-asc';
$region = isset($_GET['region']) ? $_GET['region'] : 'all';

// Filtrer par région
$filtered_destinations = $destinations;
if ($region !== 'all') {
    $filtered_destinations = array_filter($destinations, function($dest) use ($region) {
        return $dest['region'] === $region;
    });
}

// Trier les destinations
usort($filtered_destinations, function($a, $b) use ($sort) {
    if ($sort === 'price-asc') {
        return $a['price'] <=> $b['price'];
    } elseif ($sort === 'price-desc') {
        return $b['price'] <=> $a['price'];
    } elseif ($sort === 'name-asc') {
        return strcmp($a['name'], $b['name']);
    } elseif ($sort === 'name-desc') {
        return strcmp($b['name'], $a['name']);
    }
    return 0;
});
?>

<main>
    <section class="search-section">
        <h2>Rechercher une Destination</h2>

        <form method="GET" class="search-filters">
            <div class="filter-group">
                <label for="sort">Trier par :</label>
                <select name="sort" id="sort">
                    <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>Prix (croissant)</option>
                    <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>Prix (décroissant)</option>
                    <option value="name-asc" <?php echo $sort === 'name-asc' ? 'selected' : ''; ?>>Nom (A-Z)</option>
                    <option value="name-desc" <?php echo $sort === 'name-desc' ? 'selected' : ''; ?>>Nom (Z-A)</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="region">Région :</label>
                <select name="region" id="region">
                    <option value="all" <?php echo $region === 'all' ? 'selected' : ''; ?>>Toutes</option>
                    <option value="Piltover" <?php echo $region === 'Piltover' ? 'selected' : ''; ?>>Piltover</option>
                    <option value="Demacia" <?php echo $region === 'Demacia' ? 'selected' : ''; ?>>Demacia</option>
                    <option value="Freljord" <?php echo $region === 'Freljord' ? 'selected' : ''; ?>>Freljord</option>
                    <option value="Ionia" <?php echo $region === 'Ionia' ? 'selected' : ''; ?>>Ionia</option>
                    <option value="Noxus" <?php echo $region === 'Noxus' ? 'selected' : ''; ?>>Noxus</option>
                </select>
            </div>
            <button type="submit" class="cta-button">Appliquer</button>
        </form>

        <div class="featured-destinations">
            <?php if (empty($filtered_destinations)): ?>
                <p>Aucune destination trouvée pour cette région.</p>
            <?php else: ?>
                <?php foreach ($filtered_destinations as $dest): ?>
                    <div class="trip-card">
                        <div class="<?php echo htmlspecialchars($dest['bg_class']); ?>"></div>
                        <div class="trip-content">
                            <h3><?php echo htmlspecialchars($dest['name']); ?></h3>
                            <p>Région : <?php echo htmlspecialchars($dest['region']); ?></p>
                        </div>
                        <div class="trip-footer">
                            <span class="price"><?php echo htmlspecialchars($dest['price']); ?> PO</span>
                            <a href="booking.php?trip=<?php echo strtolower(str_replace(' ', '', $dest['region'])); ?>" class="view-details">Découvrir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>