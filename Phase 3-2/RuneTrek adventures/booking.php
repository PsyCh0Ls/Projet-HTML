<?php
session_start();
include 'includes/header.php';

// Liste des destinations avec leurs prix
$destinations = [
    'piltover' => ['name' => 'Exploration de Piltover', 'price' => 500],
    'demacia' => ['name' => 'Découverte de Demacia', 'price' => 600],
    'freljord' => ['name' => 'Aventure dans le Freljord', 'price' => 700],
    'ionia' => ['name' => 'Voyage à Ionia', 'price' => 550],
    'noxus' => ['name' => 'Expédition à Noxus', 'price' => 650],
];

// Vérifier si un voyage est sélectionné
if (isset($_GET['trip']) && array_key_exists($_GET['trip'], $destinations)) {
    $trip = $_GET['trip'];
    $destination = $destinations[$trip];

    // Ajouter au panier si le formulaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = [
            'destination' => $destination['name'],
            'price' => $destination['price']
        ];
        header('Location: cart.php');
        exit;
    }
} else {
    // Rediriger si aucune destination valide n'est sélectionnée
    header('Location: index.php');
    exit;
}
?>

<main>
    <section class="booking-section">
        <h2>Réservation : <?php echo htmlspecialchars($destination['name']); ?></h2>
        <p>Prix : <?php echo htmlspecialchars($destination['price']); ?> PO</p>
        <form method="POST">
            <button type="submit" class="cta-button">Ajouter au panier</button>
        </form>
        <a href="index.php" class="view-details">Retour aux destinations</a>
    </section>
</main>

<?php
include 'includes/footer.php';
?>