<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/getapikey.php';

if (!isset($_SESSION['selected_trip'])) {
    header('Location: search.php');
    exit;
}

$trip = get_trip_by_id($_SESSION['selected_trip']['id']);

// Préparation des données pour CY Bank
$vendeur = 'MIM_C';
$api_key = getAPIKey($vendeur);
$transaction = uniqid('TX', true);
$transaction = substr(str_replace('.', '', $transaction), 0, 24);
$montant = number_format($_SESSION['selected_trip']['total_price'], 2, '.', '');
$retour_base_url = 'http://localhost/Phase%203/RuneTrek%20adventures/';
$retour_path = '/retour_paiement.php';
$retour = $retour_base_url . $retour_path . '?session=' . session_id();
$control = md5($api_key . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $retour . '#');

// Stocker les données pour vérification au retour
$_SESSION['payment_data'] = [
    'transaction' => $transaction,
    'montant' => $montant,
    'vendeur' => $vendeur,
    'retour' => $retour,
    'control' => $control
];
?>

<?php include 'includes/header.php'; ?>
<div class="summary-page">
    <div class="summary-container">
        <h2>Récapitulatif de votre voyage</h2>
        
        <p><strong>Destination:</strong> <?php echo htmlspecialchars($trip['title']); ?></p>
        <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
        <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
        
        <h3>Etapes du voyage</h3>
        <ul>
            <?php foreach ($trip['stages'] as $stage): ?>
                <li>
                    <strong><?php echo htmlspecialchars($stage['title']); ?></strong>
                    <ul>
                        <?php foreach ($stage['options'] as $option): ?>
                            <li>
                                <?php echo htmlspecialchars($option['name']); ?>: 
                                <?php 
                                    $selected_value = $_SESSION['selected_trip']['stages'][$stage['id']][$option['name']]['value'];
                                    $option_price = 0;
                                    foreach ($option['values'] as $value) {
                                        if ($value['value'] == $selected_value) {
                                            $option_price = $value['price'];
                                            break;
                                        }
                                    }
                                    echo htmlspecialchars($selected_value) . ' (' . $option_price . ' PO)';
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <p><strong>Prix total:</strong> <?php echo htmlspecialchars($_SESSION['selected_trip']['total_price']); ?> PO</p>
        
        <div class="summary-actions">
            <a href="trip_details.php?id=<?php echo $_SESSION['selected_trip']['id']; ?>" class="back-button">Modifier les options</a>
            
            <!-- Formulaire direct vers CY Bank -->
            <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST" id="cybank_form">
                <input type="hidden" name="transaction" value="<?php echo htmlspecialchars($transaction); ?>">
                <input type="hidden" name="montant" value="<?php echo htmlspecialchars($montant); ?>">
                <input type="hidden" name="vendeur" value="<?php echo htmlspecialchars($vendeur); ?>">
                <input type="hidden" name="retour" value="<?php echo htmlspecialchars($retour); ?>">
                <input type="hidden" name="control" value="<?php echo htmlspecialchars($control); ?>">
                <button type="submit" class="payment-button" id="direct-payment-btn">Procéder au paiement</button>
            </form>
        </div>
        
        <!-- Notification d'ajout au panier si nécessaire -->
        <?php if (isset($_GET['added_to_cart']) && $_GET['added_to_cart'] == '1'): ?>
            <div id="cart-notification" class="cart-notification success">
                Ce voyage a été ajouté à votre panier
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.summary-container {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background: #F8F9FA;
    border: 1px solid #2F3136;
    border-radius: 4px;
}
.summary-container h2 {
    font-family: 'Beaufort for LOL', sans-serif;
    color: #2F3136;
    margin-bottom: 20px;
}
.summary-container p {
    margin: 10px 0;
}
.summary-container ul {
    list-style: none;
    margin: 10px 0;
}
.summary-container ul li {
    margin: 5px 0;
}
.payment-button {
    display: inline-block;
    background-color: #1E88E5;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}
.payment-button:hover {
    background-color: #1976D2;
}
.back-button {
    display: inline-block;
    background-color: #f0f0f0;
    color: #333;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    margin-right: 10px;
}
.summary-actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}
.cart-notification {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border-radius: 4px;
    margin-top: 20px;
    text-align: center;
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation du bouton de paiement
    const paymentButton = document.getElementById('direct-payment-btn');
    if (paymentButton) {
        paymentButton.addEventListener('click', function() {
            this.textContent = 'Redirection vers CY Bank...';
            this.style.backgroundColor = '#90CAF9';
            
            // Ajouter un effet de chargement
            const overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = '9999';
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.3s ease';
            overlay.innerHTML = '<div style="color: white; font-size: 1.5rem; text-align: center; padding: 20px; background-color: rgba(0,0,0,0.7); border-radius: 8px;">Connexion à CY Bank<br><div class="spinner" style="margin-top: 15px;"></div></div>';
            
            // Ajouter le style pour le spinner
            const spinnerStyle = document.createElement('style');
            spinnerStyle.textContent = `
                .spinner {
                    width: 40px;
                    height: 40px;
                    margin: 0 auto;
                    border: 4px solid rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    border-top-color: white;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(spinnerStyle);
            
            document.body.appendChild(overlay);
            
            // Afficher l'overlay avec un délai pour voir l'animation
            setTimeout(() => {
                overlay.style.opacity = '1';
            }, 50);
        });
    }
    
    // Afficher la notification si nécessaire
    const notification = document.getElementById('cart-notification');
    if (notification) {
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.opacity = '1';
        }, 100);
        
        // Masquer après 5 secondes
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 500);
        }, 5000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
