<?php
session_start();
require_once 'includes/header.php';
$trips = require_once 'data/trips.json';
$trip = null;

if (isset($_GET['id'])) {
    foreach ($trips['trips'] as $t) {
        if ($t['id'] == $_GET['id']) {
            $trip = $t;
            break;
        }
    }
}

if (!$trip || !isset($_SESSION['user_id'])) {
    header('Location: search.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['selected_trip'] = [
        'id' => $trip['id'],
        'title' => $trip['title'],
        'region' => $trip['region'],
        'description' => $trip['description'],
        'date' => $trip['date'],
        'duration' => $trip['duration'],
        'base_price' => $trip['base_price'],
        'options' => [
            'guide' => isset($_POST['guide']),
            'mount' => isset($_POST['mount'])
        ]
    ];
    $total = $trip['base_price'];
    if ($_POST['guide']) $total += $trip['guide_price'];
    if ($_POST['mount']) $total += $trip['mount_price'];
    $_SESSION['selected_trip']['total_price'] = $total;
    header('Location: trip_summary.php');
    exit;
}
?>
    <div class="trip-details">
        <h2><?php echo htmlspecialchars($trip['title']); ?></h2>
        <p><strong>Région :</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
        <p><strong>Description :</strong> <?php echo htmlspecialchars($trip['description']); ?></p>
        <p><strong>Date :</strong> <?php echo htmlspecialchars($trip['date']); ?></p>
        <p><strong>Durée :</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
        <p><strong>Prix de base :</strong> <?php echo htmlspecialchars($trip['base_price']); ?> PO</p>
        <form method="POST" class="options">
            <div>
                <label>
                    <input type="checkbox" name="guide" value="1"> Guide (+<?php echo htmlspecialchars($trip['guide_price']); ?> PO)
                </label>
            </div>
            <div>
                <label>
                    <input type="checkbox" name="mount" value="1"> Monture (+<?php echo htmlspecialchars($trip['mount_price']); ?> PO)
                </label>
            </div>
            <button type="submit">Réserver</button>
        </form>
    </div>
<?php require_once 'includes/footer.php'; ?>