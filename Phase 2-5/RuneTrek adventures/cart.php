<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/cart_functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialiser le panier si nécessaire
initialize_cart();

// Traiter les actions sur le panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Action: Supprimer un voyage du panier
        if ($_POST['action'] === 'remove' && isset($_POST['trip_id'])) {
            $trip_id = (int)$_POST['trip_id'];
            remove_from_cart($trip_id);
            header('Location: cart.php');
            exit;
        }
        // Action: Vider le panier
        elseif ($_POST['action'] === 'clear') {
            clear_cart();
            header('Location: cart.php');
            exit;
        }
        // Action: Passer à la caisse
        elseif ($_POST['action'] === 'checkout') {
            // Rediriger vers la page de paiement si le panier n'est pas vide
            if (!empty($_SESSION['cart']['items'])) {
                // Sélectionner le premier voyage du panier comme voyage actif pour le paiement
                if (!empty($_SESSION['cart']['items'])) {
                    $first_item = $_SESSION['cart']['items'][0];
                    $_SESSION['selected_trip'] = [
                        'id' => $first_item['id'],
                        'stages' => $first_item['options'],
                        'total_price' => $first_item['price']
                    ];
                }
                
                header('Location: payment.php');
                exit;
            }
        }
    }
}

// Récupérer le contenu du panier
$cart = get_cart();
?>

<?php include 'includes/header.php'; ?>

<div class="cart-page">
    <main>
        <h1>Votre panier</h1>
        
        <?php if (empty($cart['items'])): ?>
            <p class="empty-cart-message">Votre panier est vide. <a href="search.php">Découvrir nos voyages</a></p>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cart['items'] as $index => $item): ?>
                        <div class="cart-item" data-id="<?php echo $item['id']; ?>">
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="item-region"><?php echo htmlspecialchars($item['region']); ?></p>
                                <p class="item-price" data-price="<?php echo htmlspecialchars($item['price']); ?>"><?php echo htmlspecialchars($item['price']); ?> PO</p>
                                
                                <?php if (!empty($item['options'])): ?>
                                    <div class="item-options">
                                        <p><strong>Options sélectionnées:</strong></p>
                                        <ul>
                                            <?php 
                                            $trip = get_trip_by_id($item['id']);
                                            
                                            if ($trip && isset($trip['stages']) && !empty($item['options'])):
                                                foreach ($trip['stages'] as $stage): 
                                                    $stage_id = $stage['id'];
                                                    if (!isset($item['options'][$stage_id])) continue;
                                            ?>
                                                <li>
                                                    <strong><?php echo htmlspecialchars($stage['title']); ?>:</strong>
                                                    <ul>
                                                        <?php foreach ($item['options'][$stage_id] as $option_name => $option_data): ?>
                                                            <li>
                                                                <?php echo htmlspecialchars($option_name); ?>: 
                                                                <?php 
                                                                    if (is_array($option_data)) {
                                                                        echo htmlspecialchars($option_data['value']) . ' (' . $option_data['price'] . ' PO)';
                                                                    } else {
                                                                        echo htmlspecialchars($option_data);
                                                                    }
                                                                ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </li>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-actions">
                                <a href="trip_details.php?id=<?php echo $item['id']; ?>" class="view-details">Modifier</a>
                                <form method="POST" class="remove-item-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="trip_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="remove-item">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h2>Récapitulatif</h2>
                    <p class="cart-total"><strong>Total:</strong> <?php echo htmlspecialchars($cart['total']); ?> PO</p>
                    
                    <div class="cart-actions">
                        <form method="POST" class="cart-action-form">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="clear-cart">Vider le panier</button>
                        </form>
                        
                        <form method="POST" class="cart-action-form">
                            <input type="hidden" name="action" value="checkout">
                            <button type="submit" class="checkout">Procéder au paiement</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.cart-page main {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

.cart-page h1 {
    text-align: center;
    margin-bottom: 2rem;
    font-family: 'Beaufort for LOL', sans-serif;
    color: #1E88E5;
}

.empty-cart-message {
    text-align: center;
    font-size: 1.2rem;
    padding: 2rem;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.cart-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    background-color: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease-out;
}

.item-details {
    flex: 1;
}

.item-details h3 {
    margin: 0 0 0.5rem;
    color: #1E88E5;
}

.item-price {
    font-weight: bold;
    color: #1E88E5;
    font-size: 1.2rem;
}

.item-options {
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

.item-actions {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.5rem;
}

.view-details, .remove-item {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-align: center;
    text-decoration: none;
}

.view-details {
    background-color: #1E88E5;
    color: white;
}

.remove-item {
    background-color: #f44336;
    color: white;
    border: none;
    cursor: pointer;
}

.cart-summary {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    position: sticky;
    top: 2rem;
}

.cart-summary h2 {
    margin-top: 0;
    font-family: 'Beaufort for LOL', sans-serif;
    color: #1E88E5;
}

.cart-total {
    font-size: 1.5rem;
    margin: 1.5rem 0;
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.clear-cart, .checkout {
    padding: 0.8rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: bold;
}

.clear-cart {
    background-color: #f5f5f5;
    color: #333;
}

.checkout {
    background-color: #1E88E5;
    color: white;
}

@keyframes price-highlight {
    0% { color: #1E88E5; transform: scale(1); }
    50% { color: #FFD700; transform: scale(1.1); }
    100% { color: #1E88E5; transform: scale(1); }
}

.price-updated {
    animation: price-highlight 0.5s ease-out;
}

/* Mode sombre */
.dark-mode .cart-page main {
    background-color: #121212;
}

.dark-mode .empty-cart-message {
    background-color: #1e1e1e;
    color: #e0e0e0;
}

.dark-mode .cart-item {
    background-color: #1e1e1e;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.dark-mode .cart-summary {
    background-color: #1e1e1e;
    color: #e0e0e0;
}

.dark-mode .clear-cart {
    background-color: #333;
    color: #e0e0e0;
}

@media (max-width: 768px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
    
    .cart-item {
        flex-direction: column;
    }
    
    .item-actions {
        flex-direction: row;
        margin-top: 1rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>