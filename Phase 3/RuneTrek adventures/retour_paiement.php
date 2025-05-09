<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/getapikey.php';

$errors = [];
$success = false;

// Vérifier les paramètres de retour
$transaction = $_GET['transaction'] ?? '';
$montant = $_GET['montant'] ?? '';
$vendeur = $_GET['vendeur'] ?? '';
$status = $_GET['status'] ?? '';
$control = $_GET['control'] ?? '';
$session_id = $_GET['session'] ?? '';

// Créer le dossier logs s'il n'existe pas
if (!is_dir('logs')) {
    mkdir('logs', 0755, true);
}

// Journaliser la réception des données
error_log(date('Y-m-d H:i:s') . " - Retour CY Bank: transaction=$transaction, montant=$montant, vendeur=$vendeur, status=$status\n", 3, 'logs/payments.log');

// Vérifier la session
if ($session_id !== session_id()) {
    $errors[] = 'Session invalide.';
    error_log(date('Y-m-d H:i:s') . " - Erreur: Session invalide, reçu=$session_id, attendu=" . session_id() . "\n", 3, 'logs/payments.log');
}

// Vérifier les données de paiement
if (!isset($_SESSION['payment_data'])) {
    $errors[] = 'Données de paiement manquantes.';
    error_log(date('Y-m-d H:i:s') . " - Erreur: Données de paiement manquantes\n", 3, 'logs/payments.log');
} else {
    $payment_data = $_SESSION['payment_data'];
    $api_key = getAPIKey($vendeur);

    // Vérifier l'intégrité
    $expected_control = md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $payment_data['retour'] . '#');
    if ($control !== $expected_control) {
        $errors[] = 'Contrôle d’intégrité échoué.';
        error_log(date('Y-m-d H:i:s') . " - Erreur: Contrôle d’intégrité échoué, reçu=$control, attendu=$expected_control\n", 3, 'logs/payments.log');
    }

    // Vérifier les autres données
    if ($transaction !== $payment_data['transaction'] || $montant !== $payment_data['montant'] || $vendeur !== $payment_data['vendeur']) {
        $errors[] = 'Données de transaction incohérentes.';
        error_log(date('Y-m-d H:i:s') . " - Erreur: Données incohérentes, transaction=$transaction, montant=$montant, vendeur=$vendeur\n", 3, 'logs/payments.log');
    }

    // Traiter le statut
    if (empty($errors)) {
        if ($status === 'accepted') {
            // Enregistrer le paiement
            $payments_data = read_json('data/payments.json');
            $payments_data['payments'][] = [
                'user_id' => $_SESSION['user_id'],
                'trip_id' => $_SESSION['selected_trip']['id'],
                'amount' => $montant,
                'transaction' => $transaction,
                'date' => date('Y-m-d H:i:s')
            ];
            write_json('data/payments.json', $payments_data);

            // Enregistrer la réservation
            $bookings_data = read_json('data/bookings.json');
            $bookings_data['bookings'][] = [
                'user_id' => $_SESSION['user_id'],
                'trip_id' => $_SESSION['selected_trip']['id'],
                'options' => $_SESSION['selected_trip']['options'],
                'total_price' => $montant,
                'booking_date' => date('Y-m-d H:i:s')
            ];
            write_json('data/bookings.json', $bookings_data);

            // Mettre à jour l'utilisateur
            $users_data = read_json('data/users.json');
            foreach ($users_data['users'] as &$user) {
                if ($user['id'] == $_SESSION['user_id']) {
                    if (!isset($user['trips_purchased']) || !is_array($user['trips_purchased'])) {
                        $user['trips_purchased'] = [];
                    }
                    $user['trips_purchased'][] = [
                        'trip_id' => $_SESSION['selected_trip']['id'],
                        'booking_date' => date('Y-m-d H:i:s')
                    ];
                    break;
                }
            }
            write_json('data/users.json', $users_data);

            $success = true;
            error_log(date('Y-m-d H:i:s') . " - Paiement réussi: transaction=$transaction, user_id={$_SESSION['user_id']}\n", 3, 'logs/payments.log');
        } else {
            $errors[] = 'Paiement refusé par CY Bank.';
            error_log(date('Y-m-d H:i:s') . " - Erreur: Paiement refusé, status=$status\n", 3, 'logs/payments.log');
        }
    }
}

// Nettoyer la session
unset($_SESSION['selected_trip']);
unset($_SESSION['payment_data']);
?>

<?php include 'includes/header.php'; ?>
<main>
    <div class="payment-result">
        <h2>Résultat du paiement</h2>
        <?php if ($success): ?>
            <p class="success">Paiement accepté ! Votre voyage est réservé.</p>
            <p><a href="profile.php">Voir mes voyages</a></p>
        <?php else: ?>
            <p class="error">Erreur lors du paiement :</p>
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
            <p><a href="search.php">Retour à la recherche</a></p>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>