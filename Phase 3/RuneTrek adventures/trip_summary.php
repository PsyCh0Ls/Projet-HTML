<?php
session_start();
require_once 'includes/header.php';

if (!isset($_SESSION['selected_trip']) || !isset($_SESSION['user_id'])) {
    header('Location: search.php');
    exit;
}

$trip = $_SESSION['selected_trip'];
?>
    <div class="booking-summary">
        <h2>Récapitulatif de la réservation</h2>
        <div class="summary-item">
            <p><strong>Voyage :</strong> <?php echo htmlspecialchars($trip['title']); ?></p>
            <p><strong>Région :</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
            <p><strong>Date :</strong> <?php echo htmlspecialchars($trip['date']); ?></p>
            <p><strong>Durée :</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
            <p><strong>Options :</strong>
                <?php echo (isset($trip['options']['guide']) && $trip['options']['guide']) ? 'Guide, ' : ''; ?>
                <?php echo (isset($trip['options']['mount']) && $trip['options']['mount']) ? 'Monture' : ''; ?>
            </p>
            <p><strong>Total :</strong> <?php echo htmlspecialchars($trip['total_price']); ?> PO</p>
            <button onclick="window.location.href='payment.php'">Procéder au paiement</button>
        </div>
    </div>
<?php require_once 'includes/footer.php'; ?>