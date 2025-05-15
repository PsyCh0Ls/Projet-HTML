<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/getapikey.php';

if (!is_authenticated() || !isset($_SESSION['selected_trip'])) {
    header('Location: login.php');
    exit;
}

$trip = get_trip_by_id($_SESSION['selected_trip']['id']);
if (!$trip) {
    header('Location: search.php');
    exit;
}

$errors = [];
$submit_to_cybank = false;

// URL de retour configurable (à ajuster selon ton serveur)
$return_base_url = 'http://localhost'; // Remplace par ton URL (ex. https://ton-ngrok-id.ngrok.io)
$return_path = '/retour_paiement.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = trim($_POST['card_number'] ?? '');
    $card_holder = trim($_POST['card_holder'] ?? '');
    $expiry_date = trim($_POST['expiry_date'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    // Validation locale (simulée pour tests)
    if (!preg_match('/^\d{16}$/', $card_number) || $card_number !== '5555123456789000') {
        $errors[] = 'Numéro de carte invalide. Utilisez la carte d’essai : 5555123456789000.';
    }
    if (empty($card_holder) || strlen($card_holder) > 100) {
        $errors[] = 'Le nom du titulaire est requis et doit être inférieur à 100 caractères.';
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiry_date)) {
        $errors[] = 'La date d’expiration doit être au format MM/AA.';
    }
    if (!preg_match('/^\d{3}$/', $cvv) || $cvv !== '555') {
        $errors[] = 'CVV invalide. Utilisez le cryptogramme : 555.';
    }

    if (empty($errors)) {
        // Préparer les données pour CY Bank
        $vendeur = 'MIM_C';
        $api_key = getAPIKey($vendeur);
        if (!preg_match('/^[0-9a-zA-Z]{15}$/', $api_key)) {
            $errors[] = 'Clé API invalide pour le vendeur MIM_C.';
        } else {
            $transaction = uniqid('TX', true);
            $transaction = substr(str_replace('.', '', $transaction), 0, 24);
            $montant = number_format($_SESSION['selected_trip']['total_price'], 2, '.', '');
            $retour = $return_base_url . $return_path . '?session=' . session_id();
            $control = md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $retour . '#');

            // Stocker les données pour vérification au retour
            $_SESSION['payment_data'] = [
                'transaction' => $transaction,
                'montant' => $montant,
                'vendeur' => $vendeur,
                'retour' => $retour,
                'control' => $control
            ];

            $submit_to_cybank = true;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="payment-page">
    <main>
        <h2>Paiement pour <?php echo htmlspecialchars($trip['title']); ?></h2>
        <div class="payment-details">
            <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
            <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
            <p><strong>Durée:</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
            <p><strong>Prix total:</strong> <?php echo htmlspecialchars($_SESSION['selected_trip']['total_price']); ?> PO</p>
        </div>
        <?php if ($submit_to_cybank): ?>
            <p>Redirection vers l’interface de paiement CY Bank...</p>
            <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST" id="cybank_form">
                <input type="hidden" name="transaction" value="<?php echo htmlspecialchars($transaction); ?>">
                <input type="hidden" name="montant" value="<?php echo htmlspecialchars($montant); ?>">
                <input type="hidden" name="vendeur" value="<?php echo htmlspecialchars($vendeur); ?>">
                <input type="hidden" name="retour" value="<?php echo htmlspecialchars($retour); ?>">
                <input type="hidden" name="control" value="<?php echo htmlspecialchars($control); ?>">
            </form>
            <script>document.getElementById('cybank_form').submit();</script>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="payment-form">
                <div class="form-group">
                    <label for="card_number">Numéro de carte</label>
                    <input type="text" id="card_number" name="card_number" maxlength="16" pattern="\d{16}" required
                           value="<?php echo isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : '5555123456789000'; ?>">
                </div>
                <div class="form-group">
                    <label for="card_holder">Titulaire de la carte</label>
                    <input type="text" id="card_holder" name="card_holder" maxlength="100" required
                           value="<?php echo isset($_POST['card_holder']) ? htmlspecialchars($_POST['card_holder']) : 'Test User'; ?>">
                </div>
                <div class="form-group">
                    <label for="expiry_date">Date d’expiration (MM/AA)</label>
                    <input type="text" id="expiry_date" name="expiry_date" pattern="(0[1-9]|1[0-2])\/[0-9]{2}" placeholder="MM/AA" required
                           value="<?php echo isset($_POST['expiry_date']) ? htmlspecialchars($_POST['expiry_date']) : '12/25'; ?>">
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" maxlength="3" pattern="\d{3}" required
                           value="<?php echo isset($_POST['cvv']) ? htmlspecialchars($_POST['cvv']) : '555'; ?>">
                </div>
                <button type="submit" class="book-now">Payer maintenant</button>
            </form>
            <p><a href="trip_summary.php">Retour au récapitulatif</a></p>
        <?php endif; ?>
    </main>
</div>
<?php include 'includes/footer.php'; ?>