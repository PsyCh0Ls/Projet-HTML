<?php
session_start();
require_once 'includes/functions.php';

if (!is_authenticated()) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: search.php');
    exit;
}

$trip_id = (int)$_GET['id'];
$trip = get_trip_by_id($trip_id);

if (!$trip) {
    header('Location: search.php');
    exit;
}

// Si une personnalisation existe, rediriger vers trip_summary.php
if (isset($_SESSION['selected_trip']) && $_SESSION['selected_trip']['id'] == $trip_id) {
    header('Location: trip_summary.php');
    exit;
}

// Si aucune personnalisation, permettre une réservation directe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_data = read_json('data/bookings.json');
    $bookings = $booking_data['bookings'] ?? [];
    $new_booking = [
        'id' => count($bookings) + 1,
        'user_id' => $_SESSION['user_id'],
        'trip_id' => $trip_id,
        'booking_date' => date('Y-m-d'),
        'options' => [] // Pas d'options pour réservation directe
    ];
    $bookings[] = $new_booking;
    $booking_data['bookings'] = $bookings;
    write_json('data/bookings.json', $booking_data);

    // Mettre à jour trips_purchased
    $users_data = read_json('data/users.json');
    foreach ($users_data['users'] as &$user) {
        if ($user['id'] == $_SESSION['user_id']) {
            $user['trips_purchased'][] = $trip_id;
            break;
        }
    }
    write_json('data/users.json', $users_data);

    header('Location: profile.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<div class="booking-page">
    <main>
        <h2>Réservation pour <?php echo htmlspecialchars($trip['title']); ?></h2>
        <div class="booking-details">
            <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
            <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
            <p><strong>Durée:</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
            <p><strong>Prix:</strong> <?php echo htmlspecialchars($trip['price']); ?> PO</p>
            <p>Aucune personnalisation n'a été sélectionnée.</p>
            <p><a href="trip_details.php?id=<?php echo $trip_id; ?>">Personnaliser ce voyage</a></p>
        </div>
        <form method="POST">
            <button type="submit" class="book-now">Confirmer la réservation</button>
        </form>
    </main>
</div>
<?php include 'includes/footer.php'; ?>