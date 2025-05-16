<?php
session_start();
require_once 'includes/functions.php';

require_auth();

$user = get_user_by_id($_SESSION['user_id']);

// Formater les données pour l'affichage
$birth_date = $user['birth_date'] ? date('d/m/Y', strtotime($user['birth_date'])) : 'Non renseigné';
$registration_date = $user['registration_date'] ? date('d/m/Y', strtotime($user['registration_date'])) : 'Non renseigné';
$last_login = $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais';

$trips_data = read_json('data/trips.json');
$purchased_trips = [];

if (!empty($user['trips_purchased'])) {
    foreach ($user['trips_purchased'] as $trip_id) {
        foreach ($trips_data['trips'] as $trip) {
            if ($trip['id'] == $trip_id) {
                $purchased_trips[] = $trip;
                break;
            }
        }
    }
}

$payments_data = read_json('data/payments.json');
$payments = [];
if (isset($payments_data['payments'])) {
    foreach ($payments_data['payments'] as $payment) {
        if ($payment['user_id'] == $_SESSION['user_id']) {
            $payments[] = $payment;
        }
    }
}

// Gestion de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = $_POST['name'] ?? $user['name'];
    $nickname = $_POST['nickname'] ?? $user['nickname'];
    $birth_date = $_POST['birth_date'] ?? $user['birth_date'];
    $address = $_POST['address'] ?? $user['address'];
    
    // Mise à jour des données utilisateur
    $users_data = read_json('data/users.json');
    foreach ($users_data['users'] as &$u) {
        if ($u['id'] == $_SESSION['user_id']) {
            $u['name'] = $name;
            $u['nickname'] = $nickname;
            $u['birth_date'] = $birth_date;
            $u['address'] = $address;
            break;
        }
    }
    
    write_json('data/users.json', $users_data);
    
    // Recharger l'utilisateur avec les nouvelles données
    $user = get_user_by_id($_SESSION['user_id']);
    $birth_date = $user['birth_date'] ? date('d/m/Y', strtotime($user['birth_date'])) : 'Non renseigné';
    
    // Message de succès
    $success_message = "Votre profil a été mis à jour avec succès.";
    
    // Redirection pour éviter la resoumission du formulaire
    header("Location: profile.php?updated=1");
    exit;
}

// Afficher un message de succès si le profil a été mis à jour
$profile_updated = isset($_GET['updated']) && $_GET['updated'] == '1';
?>

<?php include 'includes/header.php'; ?>

<main>
    <div class="profile-container">
        <h2>Votre Profil</h2>
        
        <?php if ($profile_updated): ?>
            <div class="success-message">Votre profil a été mis à jour avec succès.</div>
        <?php endif; ?>
        
        <div class="profile-info">
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Surnom:</strong> <?php echo htmlspecialchars($user['nickname']); ?></p>
            <p><strong>Date de naissance:</strong> <?php echo htmlspecialchars($birth_date); ?></p>
            <p><strong>Adresse:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Date d'inscription:</strong> <?php echo htmlspecialchars($registration_date); ?></p>
            <p><strong>Dernière connexion:</strong> <?php echo htmlspecialchars($last_login); ?></p>
            
            <!-- Formulaire caché pour le traitement côté serveur -->
            <form id="profile-update-form" method="POST" style="display: none;">
                <input type="hidden" name="action" value="update_profile">
                <input type="hidden" id="server-name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                <input type="hidden" id="server-nickname" name="nickname" value="<?php echo htmlspecialchars($user['nickname']); ?>">
                <input type="hidden" id="server-birth_date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>">
                <input type="hidden" id="server-address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            </form>
        </div>

        <h2>Vos Voyages Achetés</h2>
        <div class="trip-cards">
            <?php if (empty($purchased_trips)): ?>
                <p>Aucun voyage acheté pour le moment.</p>
            <?php else: ?>
                <?php foreach ($purchased_trips as $trip): ?>
                    <article class="trip-card">
                        <div class="trip-image <?php echo strtolower(htmlspecialchars($trip['region'])); ?>-bg"></div>
                        <div class="trip-content">
                            <h3><?php echo htmlspecialchars($trip['title']); ?></h3>
                            <p class="trip-description"><?php echo htmlspecialchars($trip['description']); ?></p>
                            <div class="trip-details">
                                <span class="duration"><i class="icon-clock"></i> <?php echo htmlspecialchars($trip['duration']); ?> jours</span>
                                <span class="price"><i class="icon-price"></i> <?php echo htmlspecialchars($trip['price']); ?> PO</span>
                            </div>
                            <div class="trip-footer">
                                <a href="trip_details.php?id=<?php echo $trip['id']; ?>&readonly=1" class="view-details">Voir les détails</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($payments)): ?>
        <h2>Vos Derniers Paiements</h2>
        <div class="payments-list">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Voyage</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($payment['date'])); ?></td>
                            <td>
                                <?php 
                                foreach ($trips_data['trips'] as $trip) {
                                    if ($trip['id'] == $payment['trip_id']) {
                                        echo htmlspecialchars($trip['title']);
                                        break;
                                    }
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($payment['amount']); ?> PO</td>
                            <td>
                                <span class="payment-status <?php echo $payment['status']; ?>">
                                    <?php echo $payment['status'] === 'completed' ? 'Complété' : 'En attente'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</main>

<style>
.profile-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #F8F9FA;
    border: 1px solid #2F3136;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.profile-container h2 {
    font-family: 'Beaufort for LOL', sans-serif;
    color: #1E88E5;
    margin: 20px 0;
}

.profile-info {
    margin-bottom: 30px;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.profile-info p {
    margin: 10px 0;
    font-family: 'Spiegel', sans-serif;
    line-height: 1.6;
}

.success-message {
    background-color: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    text-align: center;
    border-left: 4px solid #4CAF50;
    font-weight: bold;
}

.trip-cards {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

.payments-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.payments-table th, .payments-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.payments-table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.payment-status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
}

.payment-status.completed {
    background-color: rgba(76, 175, 80, 0.2);
    color: #4CAF50;
}

.payment-status.pending {
    background-color: rgba(255, 193, 7, 0.2);
    color: #FFC107;
}

/* Mode sombre */
.dark-mode .profile-container {
    background-color: #1e1e1e;
    border-color: #333;
}

.dark-mode .profile-info {
    background-color: #2a2a2a;
}

.dark-mode .payments-table th {
    background-color: #333;
}

.dark-mode .payments-table td {
    border-bottom-color: #444;
}
</style>

<?php include 'includes/footer.php'; ?>