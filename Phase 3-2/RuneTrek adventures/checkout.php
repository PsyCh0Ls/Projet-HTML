<?php
session_start();
include 'includes/header.php';

// Vérifier si le panier existe
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Calculer le total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'];
}

// Confirmer la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ici, tu peux ajouter une logique pour sauvegarder la commande (par exemple, dans une base de données)
    // Pour l'instant, on vide simplement le panier
    $_SESSION['cart'] = [];
    $confirmationMessage = "Votre réservation a été confirmée ! Un email de confirmation vous a été envoyé.";
}
?>

<main>
    <section class="checkout-section">
        <h2>Finaliser Votre Réservation</h2>
        <?php if (isset($confirmationMessage)): ?>
            <p class="success"><?php echo htmlspecialchars($confirmationMessage); ?></p>
            <a href="index.php" class="cta-button">Retour à l'accueil</a>
        <?php else: ?>
            <h3>Résumé de votre commande</h3>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Destination</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['destination']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?> PO</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong><?php echo $total; ?> PO</strong></td>
                    </tr>
                </tbody>
            </table>

            <h3>Informations de confirmation</h3>
            <form method="POST" id="checkoutForm">
                <div class="form-group">
                    <label for="email">Email de confirmation :</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                    <span id="emailError" class="error"></span>
                </div>
                <button type="submit" class="cta-button">Confirmer la réservation</button>
            </form>
        <?php endif; ?>
    </section>
</main>

<script>
document.getElementById('checkoutForm')?.addEventListener('submit', function(event) {
    let isValid = true;

    // Validation email
    const email = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
        emailError.textContent = 'Veuillez entrer un email valide.';
        isValid = false;
    } else {
        emailError.textContent = '';
    }

    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php
include 'includes/footer.php';
?>