<?php
session_start();
require_once 'includes/functions.php';

if (!is_authenticated() || !isset($_SESSION['selected_trip'])) {
    header('Location: login.php');
    exit;
}

$trip = get_trip_by_id($_SESSION['selected_trip']['id']);
if (!$trip) {
    header('Location: search.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="summary-container">
        <h2>Récapitulatif de votre voyage</h2>
        <p><strong>Titre:</strong> <?php echo htmlspecialchars($trip['title']); ?></p>
        <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
        <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
        <p><strong>Durée:</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
        <p><strong>Prix de base:</strong> <?php echo htmlspecialchars($trip['price']); ?> PO</p>
        <?php if (isset($trip['stages']) && isset($_SESSION['selected_trip']['stages']) && !empty($_SESSION['selected_trip']['stages'])): ?>
            <h3>Options sélectionnées</h3>
            <ul>
                <?php foreach ($trip['stages'] as $stage): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($stage['title']); ?>:</strong>
                        <ul>
                            <?php if (isset($_SESSION['selected_trip']['stages'][$stage['id']])): ?>
                                <?php foreach ($_SESSION['selected_trip']['stages'][$stage['id']] as $option_name => $option): ?>
                                    <li>
                                        <?php echo htmlspecialchars($option_name); ?>:
                                        <?php echo htmlspecialchars($option['value']); ?>
                                        (<?php echo $option['price']; ?> PO)
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Aucune option sélectionnée pour cette étape.</li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucune personnalisation sélectionnée.</p>
        <?php endif; ?>
        <p><strong>Prix total:</strong> <?php echo htmlspecialchars($_SESSION['selected_trip']['total_price']); ?> PO</p>
        <a href="payment.php" class="payment-button">Procéder au paiement</a>
        <a href="trip_details.php?id=<?php echo $trip['id']; ?>" class="back-button">Modifier les options</a>
    </div>
</main>
<?php include 'includes/footer.php'; ?>