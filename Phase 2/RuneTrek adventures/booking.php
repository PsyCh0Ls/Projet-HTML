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
$trips = read_json('data/trips.json')['trips'] ?? [];
$trip = null;
foreach ($trips as $t) {
    if ($t['id'] == $trip_id) {
        $trip = $t;
        break;
    }
}

if (!$trip) {
    header('Location: search.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookings_data = read_json('data/bookings.json');
    $bookings = $bookings_data['bookings'] ?? [];
    $new_booking = [
        'id' => count($bookings) + 1,
        'user_id' => $_SESSION['user_id'],
        'trip_id' => $trip_id,
        'booking_date' => date('Y-m-d')
    ];
    $bookings[] = $new_booking;
    $bookings_data['bookings'] = $bookings;
    write_json('data/bookings.json', $bookings_data);
    header('Location: profile.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<div class="booking-page">
    <main>
        <h2>Réserver : <?php echo htmlspecialchars($trip['title']); ?></h2>
        <div class="booking-details">
            <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
            <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
            <p><strong>Durée:</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
            <p><strong>Prix:</strong> <?php echo htmlspecialchars($trip['price']); ?> PO</p>
        </div>
        <form method="POST">
            <button type="submit">Confirmer la réservation</button>
        </form>
    </main>
</div>
<?php include 'includes/footer.php'; ?>