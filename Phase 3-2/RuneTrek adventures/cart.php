<?php
session_start();
require_once 'functions.php';
require_once 'header.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    remove_from_cart((int)$_POST['index']);
    header('Location: cart.php');
    exit;
}

$cart = get_cart();
?>

<main class="cart">
    <h2>Votre Panier</h2>
    <?php if (empty($cart)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Voyage</th>
                    <th>Région</th>
                    <th>Options</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $index => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo htmlspecialchars($item['region']); ?></td>
                        <td>
                            <?php
                            $options_display = [];
                            foreach ($item['options'] as $stage_id => $options) {
                                foreach ($options as $name => $value) {
                                    $options_display[] = "$name (Étape $stage_id) : $value";
                                }
                            }
                            echo htmlspecialchars(implode(', ', $options_display) ?: 'Aucune');
                            ?>
                        </td>
                       坻<td><?php echo htmlspecialchars($item['total_price']); ?> PO</td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="payment.php" class="checkout-button">Procéder au paiement</a>
    <?php endif; ?>
</main>

<?php require_once 'footer.php'; ?>