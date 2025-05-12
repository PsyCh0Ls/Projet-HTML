<?php
session_start();
include 'includes/header.php';

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Supprimer un article du panier
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Réindexer le tableau
    }
    header('Location: cart.php');
    exit;
}
?>

<main>
    <section class="cart-section">
        <h2>Votre Panier</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Destination</th>
                        <th>Prix</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['destination']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?> PO</td>
                            <td>
                                <a href="cart.php?remove=<?php echo $index; ?>" class="remove-button">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-actions">
                <a href="checkout.php" class="cta-button">Passer à la caisse</a>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
include 'includes/footer.php';
?>