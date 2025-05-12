<?php
session_start();
require_once 'functions.php';
require_once 'header.php';

$trip = null;
if (isset($_GET['id'])) {
    $trip = get_trip_by_id($_GET['id']);
}

if (!$trip || !is_authenticated()) {
    header('Location: search.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $options = $_POST['options'] ?? [];
    add_to_cart($_SESSION['user_id'], $trip['id'], $options);
    header('Location: cart.php');
    exit;
}
?>

<main class="trip-details">
    <h2><?php echo htmlspecialchars($trip['title']); ?></h2>
    <p><strong>Région :</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
    <p><strong>Description :</strong> <?php echo htmlspecialchars($trip['description']); ?></p>
    <p><strong>Date de début :</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
    <p><strong>Durée :</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
    <p><strong>Prix de base :</strong> <?php echo htmlspecialchars($trip['price']); ?> PO</p>
    <form method="POST" class="options-form">
        <?php foreach ($trip['stages'] as $stage): ?>
            <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
            <?php foreach ($stage['options'] as $option): ?>
                <div class="option-group">
                    <label><?php echo htmlspecialchars($option['name']); ?> :</label>
                    <select name="options[<?php echo $stage['id']; ?>][<?php echo htmlspecialchars($option['name']); ?>]">
                        <?php foreach ($option['values'] as $value): ?>
                            <option value="<?php echo htmlspecialchars($value['value']); ?>" <?php if ($value['default']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($value['value']); ?> (+<?php echo $value['price']; ?> PO)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <input type="hidden" name="action" value="add_to_cart">
        <button type="submit">Ajouter au panier</button>
    </form>
</main>

<?php require_once 'footer.php'; ?>