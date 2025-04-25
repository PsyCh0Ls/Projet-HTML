<?php
session_start();
require_once 'includes/functions.php';
require_auth();

$user = get_user_by_id($_SESSION['user_id']);
$trips = read_json('data/trips.json')['trips'];
$purchased_trips = array_filter($trips, function($trip) use ($user) {
    return in_array($trip['id'], $user['trips_purchased']);
});
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="profile-container">
        <h2>Profil de <?php echo htmlspecialchars($user['nickname'] ?: $user['name']); ?></h2>
        <div class="profile-details">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Identifiant :</strong> <?php echo htmlspecialchars($user['login']); ?></p>
            <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($user['birth_date']); ?></p>
            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Inscription :</strong> <?php echo htmlspecialchars($user['registration_date']); ?></p>
        </div>
        <h3>Vos voyages</h3>
        <?php if (empty($purchased_trips)): ?>
            <p>Aucun voyage acheté.</p>
        <?php else: ?>
            <div class="trip-list">
                <?php foreach ($purchased_trips as $trip): ?>
                    <div class="trip-item">
                        <h4><?php echo htmlspecialchars($trip['title']); ?></h4>
                        <p><?php echo htmlspecialchars($trip['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>