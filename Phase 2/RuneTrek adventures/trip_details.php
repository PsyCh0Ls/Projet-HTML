<?php
session_start();
require_once 'includes/functions.php';

$trip_id = $_GET['id'] ?? null;
$trip = get_trip_by_id($trip_id);

if (!$trip) {
    header("Location: search.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['selected_trip'] = [
        'id' => $trip['id'],
        'options' => $_POST['options'] ?? [],
        'total_price' => $trip['price'] + array_sum(array_map(function($opt) {
            return $opt['price'];
        }, array_filter($trip['options'], function($opt) use ($_POST) {
            return in_array($opt['name'], $_POST['options'] ?? []);
        })))
    ];
    header("Location: trip_summary.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="trip-details-container">
        <h2><?php echo htmlspecialchars($trip['title']); ?></h2>
        <p><?php echo htmlspecialchars($trip['description']); ?></p>
        <p><strong>Prix de base :</strong> <?php echo $trip['price']; ?> PO</p>
        <form method="POST">
            <h3>Options</h3>
            <?php foreach ($trip['options'] as $option): ?>
                <div class="option-group">
                    <label>
                        <input type="checkbox" name="options[]" value="<?php echo htmlspecialchars($option['name']); ?>">
                        <?php echo htmlspecialchars($option['name']); ?> (+<?php echo $option['price']; ?> PO)
                    </label>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="confirm-button">Confirmer</button>
        </form>
    </div>
</main>
<?php include 'includes/footer.php'; ?>