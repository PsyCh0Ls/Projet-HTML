<?php
session_start();
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$users = require_once 'data/users.json';
$user = null;
foreach ($users['users'] as $u) {
    if ($u['id'] == $_SESSION['user_id']) {
        $user = $u;
        break;
    }
}
?>
    <div class="profile">
        <h2>Mon Profil</h2>
        <div class="user-info">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Dernière connexion :</strong> <?php echo htmlspecialchars($user['last_login']); ?></p>
            <p><a href="edit_profile.php" class="edit-link">Modifier mon profil</a></p>
        </div>
        <div class="purchased-trips">
            <h3>Vos Voyages Achetés</h3>
            <?php
            $trips = require_once 'data/trips.json';
            if (!empty($user['trips_purchased'])) {
                foreach ($user['trips_purchased'] as $purchased) {
                    foreach ($trips['trips'] as $trip) {
                        if ($trip['id'] == $purchased['trip_id']) {
                            echo "<p>{$trip['title']} - Réservé le : {$purchased['booking_date']}</p>";
                            break;
                        }
                    }
                }
            } else {
                echo "<p>Aucun voyage acheté pour l'instant.</p>";
            }
            ?>
        </div>
    </div>
<?php require_once 'includes/footer.php'; ?>