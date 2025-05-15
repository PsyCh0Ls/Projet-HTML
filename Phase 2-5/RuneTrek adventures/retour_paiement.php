<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/getapikey.php';

if (!is_authenticated() || !isset($_SESSION['payment_data'])) {
    error_log('Erreur retour_paiement.php: Utilisateur non authentifié ou payment_data absent');
    header('Location: login.php');
    exit;
}

// Journaliser l'accès à la page
error_log('Accès à retour_paiement.php: ' . print_r($_GET, true));

$errors = [];
$success = false;

// Récupérer les paramètres de retour
$transaction = $_GET['transaction'] ?? '';
$montant = $_GET['montant'] ?? '';
$vendeur = $_GET['vendeur'] ?? '';
$status = $_GET['status'] ?? '';
$control = $_GET['control'] ?? '';
$session_id = $_GET['session'] ?? '';

// Vérifier les paramètres
if (empty($transaction) || empty($montant) || empty($vendeur) || empty($status) || empty($control)) {
    $errors[] = 'Paramètres de retour incomplets.';
    error_log('Erreur retour_paiement.php: Paramètres incomplets - ' . print_r($_GET, true));
} elseif ($vendeur !== 'MIM_C' || ($status !== 'accepted' && $status !== 'declined')) {
    $errors[] = 'Paramètres invalides.';
    error_log('Erreur retour_paiement.php: Vendeur ou statut invalide - vendeur=' . $vendeur . ', status=' . $status);
} elseif ($transaction !== $_SESSION['payment_data']['transaction'] || $montant !== $_SESSION['payment_data']['montant']) {
    $errors[] = 'Données de transaction incohérentes.';
    error_log('Erreur retour_paiement.php: Transaction ou montant incohérents');
}

// Vérifier le hachage control
$api_key = getAPIKey('MIM_C');
if (preg_match('/^[0-9a-zA-Z]{15}$/', $api_key)) {
    $expected_control = md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $status . '#');
    if ($control !== $expected_control) {
        $errors[] = 'Valeur de contrôle invalide.';
        error_log('Erreur retour_paiement.php: Control invalide - attendu=' . $expected_control . ', reçu=' . $control);
    }
} else {
    $errors[] = 'Clé API invalide.';
    error_log('Erreur retour_paiement.php: Clé API invalide pour MIM_C');
}

if (empty($errors) && $status === 'accepted') {
    // Enregistrer la transaction dans payments.json
    $payments_data = read_json('data/payments.json');
    $payments = $payments_data['payments'] ?? [];
    $new_payment = [
        'id' => count($payments) + 1,
        'user_id' => $_SESSION['user_id'],
        'trip_id' => $_SESSION['selected_trip']['id'],
        'amount' => floatval($montant),
        'date' => date('Y-m-d H:i:s'),
        'status' => 'completed',
        'options' => $_SESSION['selected_trip']['stages'],
        'transaction_id' => $transaction
    ];
    $payments[] = $new_payment;
    $payments_data['payments'] = $payments;
    write_json('data/payments.json', $payments_data);

    // Enregistrer la réservation dans bookings.json
    $bookings_data = read_json('data/bookings.json');
    $bookings = $bookings_data['bookings'] ?? [];
    $new_booking = [
        'id' => count($bookings) + 1,
        'user_id' => $_SESSION['user_id'],
        'trip_id' => $_SESSION['selected_trip']['id'],
        'booking_date' => date('Y-m-d'),
        'options' => $_SESSION['selected_trip']['stages']
    ];
    $bookings[] = $new_booking;
    $bookings_data['bookings'] = $bookings;
    write_json('data/bookings.json', $bookings_data);

    // Mettre à jour trips_purchased dans users.json
    $users_data = read_json('data/users.json');
    foreach ($users_data['users'] as &$user) {
        if ($user['id'] == $_SESSION['user_id']) {
            $user['trips_purchased'][] = $_SESSION['selected_trip']['id'];
            break;
        }
    }
    write_json('data/users.json', $users_data);

    $success = true;
    unset($_SESSION['selected_trip']);
    unset($_SESSION['payment_data']);
    error_log('Succès retour_paiement.php: Paiement accepté, transaction=' . $transaction);
}
?>
<?php include 'includes/header.php'; ?>
<div class="payment-page">
    <main>
        <h2>Résultat du paiement</h2>
        <?php if ($success): ?>
            <div class="success-message">
                <p>Paiement effectué avec succès ! Votre voyage est réservé.</p>
                <a href="profile.php" class="book-now">Voir mon profil</a>
            </div>
        <?php else: ?>
            <div class="error-message">
                <?php if (empty($errors)): ?>
                    <p>Le paiement a été refusé par CY Bank.</p>
                <?php else: ?>
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="payment.php" class="book-now">Réessayer le paiement</a>
                <p><a href="trip_summary.php">Retour au récapitulatif</a></p>
            </div>
        <?php endif; ?>
    </main>
</div>
<?php include 'includes/footer.php'; ?>