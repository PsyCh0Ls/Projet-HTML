<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/getapikey.php';

if (!isset($_SESSION['selected_trip']) || !isset($_SESSION['user_id'])) {
    header('Location: search.php');
    exit;
}

$trip = $_SESSION['selected_trip'];
$transaction = generate_transaction_id();
$montant = $trip['total_price'] . '.00';
$vendeur = 'MIM_C';
$api_key = getAPIKey($vendeur);
$return_base_url = 'http://localhost:8000'; // À remplacer par ton URL ngrok (ex. https://abcd1234.ngrok.io)
$return_path = '/retour_paiement.php';
$session_id = session_id();
$control = md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $return_base_url . $return_path . '?session=' . $session_id . '#');

$_SESSION['payment_data'] = [
    'transaction' => $transaction,
    'montant' => $montant,
    'vendeur' => $vendeur,
    'retour' => $return_base_url . $return_path . '?session=' . $session_id
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = $_POST['card_number'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    $payment_url = 'https://www.plateforme-smc.fr/cybank/index.php';
    $params = http_build_query([
        'transaction' => $transaction,
        'montant' => $montant,
        'vendeur' => $vendeur,
        'retour' => $return_base_url . $return_path . '?session=' . $session_id,
        'control' => $control
    ]);
    header('Location: ' . $payment_url . '?' . $params);
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<div class="payment">
    <h2>Paiement</h2>
    <form id="payment-form" method="POST">
        <div>
            <label for="card_number">Numéro de carte</label>
            <input type="text" id="card_number" name="card_number" required>
        </div>
        <div>
            <label for="expiry_date">Date d'expiration (MM/AA)</label>
            <input type="text" id="expiry_date" name="expiry_date" required>
        </div>
        <div>
            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" required>
        </div>
        <button type="submit">Payer <?php echo htmlspecialchars($trip['total_price']); ?> PO</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>