<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/cart_functions.php';

if (!is_authenticated() || !isset($_SESSION['selected_trip'])) {
    header('Location: login.php');
    exit;
}

$trip = get_trip_by_id($_SESSION['selected_trip']['id']);
if (!$trip) {
    header('Location: search.php');
    exit;
}

// Calculer le prix total en fonction des options sélectionnées
$total_price = $_SESSION['selected_trip']['total_price'] ?? $trip['price'];

// Vérifier si le voyage est déjà dans le panier
$in_cart = is_in_cart($trip['id']);

// Traitement de l'ajout manuel au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_to_cart') {
        // Ajouter au panier avec les options sélectionnées
        add_to_cart($trip['id'], $_SESSION['selected_trip']['stages'] ?? []);
        
        // Rediriger pour éviter les soumissions multiples
        header('Location: trip_summary.php?added=1');
        exit;
    }
}

// Ajouter automatiquement au panier si ce n'est pas déjà fait et si ce n'est pas un ajout manuel
$added_manually = isset($_GET['added']) && $_GET['added'] === '1';
if (!$in_cart && !isset($_SESSION['added_to_cart']) && !$added_manually) {
    // Ajouter le voyage au panier avec les options sélectionnées
    add_to_cart($trip['id'], $_SESSION['selected_trip']['stages'] ?? []);
    
    // Marquer comme ajouté pour éviter les ajouts multiples
    $_SESSION['added_to_cart'] = true;
}

// Mise à jour de la variable $in_cart après l'ajout
$in_cart = is_in_cart($trip['id']);
?>
<?php include 'includes/header.php'; ?>
<div class="summary-page">
    <main>
        <div class="summary-container">
            <h2>Récapitulatif de votre voyage</h2>
            <p><strong>Voyage :</strong> <?php echo htmlspecialchars($trip['title']); ?></p>
            <p><strong>Région :</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
            <p><strong>Date de départ :</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
            <p><strong>Durée :</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
            <p><strong>Prix total :</strong> <?php echo htmlspecialchars($total_price); ?> PO</p>
            
            <h3>Étapes</h3>
            <ul>
                <?php foreach ($trip['stages'] as $stage): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($stage['title']); ?></strong> (<?php echo htmlspecialchars($stage['duration']); ?> jours)
                        <?php if (isset($_SESSION['selected_trip']['stages'][$stage['id']])): ?>
                            <ul>
                                <?php foreach ($_SESSION['selected_trip']['stages'][$stage['id']] as $option_name => $option_data): ?>
                                    <li>
                                        <?php echo htmlspecialchars($option_name); ?>: 
                                        <?php 
                                            if (is_array($option_data)) {
                                                echo htmlspecialchars($option_data['value']);
                                                if (isset($option_data['persons']) && $option_data['persons'] > 1) {
                                                    echo ' (' . htmlspecialchars($option_data['persons']) . ' personnes)';
                                                }
                                            } else {
                                                echo htmlspecialchars($option_data);
                                            }
                                        ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <div class="summary-actions">
                <a href="trip_details.php?id=<?php echo $trip['id']; ?>" class="back-button">Modifier</a>
                
                <?php if ($in_cart): ?>
                    <a href="cart.php" class="cart-button">Voir le panier</a>
                <?php else: ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="add_to_cart">
                        <button type="submit" class="add-cart-button">Ajouter au panier</button>
                    </form>
                <?php endif; ?>
                
                <a href="payment.php" class="payment-button">Procéder au paiement</a>
            </div>
            
            <!-- Notification d'ajout au panier -->
            <?php if (($added_manually || (isset($_SESSION['added_to_cart']) && !isset($_SESSION['notification_shown']))) && $in_cart): ?>
                <?php $_SESSION['notification_shown'] = true; ?>
                <div id="cart-notification" class="cart-notification success">
                    <p>Le voyage a été ajouté à votre panier !</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
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
    .summary-actions {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 30px;
    }
    .back-button {
        display: inline-block;
        background-color: #f0f0f0;
        color: #333;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
    }
    .payment-button, .cart-button, .add-cart-button {
        display: inline-block;
        background-color: #1E88E5;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        border: none;
        cursor: pointer;
    }
    .add-cart-button {
        background-color: #FFD700;
        color: #333;
    }
    .cart-button {
        background-color: #4CAF50;
    }
    .payment-button:hover, .cart-button:hover {
        background-color: #1976D2;
    }
    .add-cart-button:hover {
        background-color: #FFC107;
    }
    .cart-notification {
        margin-top: 20px;
        padding: 10px 15px;
        border-radius: 4px;
        text-align: center;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .cart-notification.success {
        background-color: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
        border-left: 4px solid #4CAF50;
    }
    
    /* Mode sombre */
    .dark-mode .summary-container {
        background-color: #1e1e1e;
        border-color: #333;
    }
    .dark-mode .summary-container h2 {
        color: #e0e0e0;
    }
    .dark-mode .back-button {
        background-color: #333;
        color: #e0e0e0;
    }
    .dark-mode .add-cart-button {
        background-color: #FFD700;
        color: #333;
    }
    .dark-mode .add-cart-button:hover {
        background-color: #FFC107;
    }
    .dark-mode .cart-notification.success {
        background-color: rgba(76, 175, 80, 0.1);
        color: #81C784;
    }
</style>

<script>
// Afficher la notification d'ajout au panier avec une animation
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('cart-notification');
    if (notification) {
        // Animation d'entrée
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        
        setTimeout(function() {
            notification.style.transition = 'all 0.3s ease';
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
            
            // Animer le compteur du panier
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.classList.add('pulse');
                setTimeout(function() {
                    cartCount.classList.remove('pulse');
                }, 500);
            }
            
            // Masquer la notification après 5 secondes
            setTimeout(function() {
                notification.style.opacity = '0';
                
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 300);
            }, 5000);
        }, 300);
    }
});
</script>

<?php include 'includes/footer.php'; ?>