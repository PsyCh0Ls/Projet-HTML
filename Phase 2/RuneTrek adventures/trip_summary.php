<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['selected_trip'])) {
    header("Location: search.php");
    exit;
}

$trip = get_trip_by_id($_SESSION['selected_trip']['id']);
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="summary-container">
        <h2>Récapitulatif de votre voyage</h2>
        <p><strong>Voyage :</strong> <?php echo htmlspecialchars($trip['title']); ?></p>
        <p><strong>Description :</strong> <?php echo htmlspecialchars($trip['description']); ?></p>
        <p><strong>Prix de base :</strong> <?php echo $trip['price']; ?> PO</p>
        <h3>Options sélectionnées</h3>
        <?php if (empty($_SESSION['selected_trip']['options'])): ?>
            <p>Aucune option sélectionnée.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($_SESSION['selected_trip']['options'] as $option): ?>
                    <li><?php echo htmlspecialchars($option); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <p><strong>Prix total :</strong> <?php echo $_SESSION['selected_trip']['total_price']; ?> PO</p>
        <a href="payment.php" class="payment-button">Procéder au paiement</a>
    </div>
</main>
<?php include 'includes/footer.php'; ?>