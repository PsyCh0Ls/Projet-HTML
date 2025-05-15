<?php
session_start();
require_once 'includes/functions.php';

require_auth();

$user = get_user_by_id($_SESSION['user_id']);
$payments_data = read_json('data/payments.json');
$purchased_trips = [];
foreach ($payments_data['payments'] as $payment) {
    if ($payment['user_id'] == $_SESSION['user_id']) {
        $trip = get_trip_by_id($payment['trip_id']);
        if ($trip) {
            $purchased_trips[] = $trip;
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<main>
    <div class="profile-container">
        <h2>Votre Profil</h2>
        <div class="profile-info">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Surnom :</strong> <?php echo htmlspecialchars($user['nickname']); ?></p>
            <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($user['birth_date']); ?></p>
            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Date d'inscription :</strong> <?php echo htmlspecialchars($user['registration_date']); ?></p>
            <p><strong>Dernière connexion :</strong> <?php echo htmlspecialchars($user['last_login'] ?: 'Jamais'); ?></p>
            <a href="#" class="edit-profile">Modifier le profil</a>
        </div>

        <h2>Vos Voyages Achetés</h2>
        <div class="trip-cards">
            <?php if (empty($purchased_trips)): ?>
                <p>Aucun voyage acheté pour le moment.</p>
            <?php else: ?>
                <?php foreach ($purchased_trips as $trip): ?>
                    <article class="trip-card">
                        <div class="trip-image <?php echo htmlspecialchars($trip['region']); ?>-bg"></div>
                        <div class="trip-content">
                            <h3><?php echo htmlspecialchars($trip['title']); ?></h3>
                            <p class="trip-description"><?php echo htmlspecialchars($trip['description']); ?></p>
                            <div class="trip-details">
                                <span class="duration"><i class="icon-clock"></i> <?php echo $trip['duration_days']; ?> jours</span>
                                <span class="price"><i class="icon-price"></i> <?php echo $trip['total_price']; ?> PO</span>
                            </div>
                            <div class="trip-footer">
                                <a href="trip_details.php?id=<?php echo $trip['id']; ?>&readonly=1" class="view-details">Voir les détails</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
.profile-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #F8F9FA;
    border: 1px solid #2F3136;
    border-radius: 4px;
}
.profile-container h2 {
    color: #1E88E5;
}
.profile-info {
    margin-bottom: 30px;
}
.profile-info p {
    margin: 10px 0;
    font-family: 'Spiegel', sans-serif;
}
.edit-profile {
    display: inline-block;
    background: #1E88E5;
    color: #FFFFFF;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
}
.edit-profile:hover {
    background: #1976D2;
}
.trip-cards {
    display: grid;
    gap: 20px;
}
</style>

<?php include 'includes/footer.php'; ?>