<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/getapikey.php';

if (!is_authenticated() || !isset($_SESSION['selected_trip'])) {
    header('Location: login.php');
    exit;
}

$trip = get_trip_by_id($_SESSION['selected_trip']['id']);
if (!$trip) {
    header('Location: search.php');
    exit;
}

// Préparer les données pour CY Bank
$vendeur = 'MIM_C';
$api_key = getAPIKey($vendeur);

if (!preg_match('/^[0-9a-zA-Z]{15}$/', $api_key)) {
    $errors[] = 'Clé API invalide pour le vendeur MIM_C.';
    include 'includes/header.php';
    // Afficher message d'erreur
    include 'includes/footer.php';
    exit;
}

// Générer un identifiant de transaction unique
$transaction = uniqid('TX', true);
$transaction = substr(str_replace('.', '', $transaction), 0, 24);
$montant = number_format($_SESSION['selected_trip']['total_price'], 2, '.', '');

// URL de retour configurable
$return_base_url = 'http://' . $_SERVER['HTTP_HOST'];
$return_path = '/retour_paiement.php';
$retour = $return_base_url . $return_path . '?session=' . session_id();

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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection vers CY Bank - RuneTrek Adventures</title>
    <link rel="stylesheet" href="styles/runeTrek adventures.css">
    <style>
        body {
            font-family: 'Spiegel', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
            flex-direction: column;
        }
        .loading-container {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background-color: white;
            width: 90%;
            max-width: 500px;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(30, 136, 229, 0.2);
            border-radius: 50%;
            border-top-color: #1E88E5;
            animation: spin 1s infinite linear;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        h2 {
            color: #1E88E5;
            font-family: 'Beaufort for LOL', sans-serif;
        }
        .info {
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }
        .payment-details {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: left;
        }
        .payment-details p {
            margin: 5px 0;
        }
        
        /* Dark mode styles */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        
        .dark-mode .loading-container {
            background-color: #1e1e1e;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode h2 {
            color: #64B5F6;
        }
        
        .dark-mode .info {
            color: #aaa;
        }
        
        .dark-mode .payment-details {
            background-color: #2a2a2a;
        }
    </style>
    <!-- Script pour détecter le mode sombre -->
    <script>
        // Vérifier si le mode sombre est activé
        function isDarkMode() {
            return document.cookie.split(';').some(cookie => {
                const trimmed = cookie.trim();
                return trimmed.startsWith('theme=dark');
            });
        }
        
        // Appliquer le mode sombre si nécessaire
        if (isDarkMode()) {
            document.documentElement.classList.add('dark-mode');
            document.body.classList.add('dark-mode');
        }
    </script>
</head>
<body>
    <div class="loading-container">
        <div class="spinner"></div>
        <h2>Redirection vers CY Bank</h2>
        <p>Veuillez patienter pendant que nous vous connectons à l'interface de paiement sécurisée...</p>
        
        <div class="payment-details">
            <p><strong>Voyage:</strong> <?php echo htmlspecialchars($_SESSION['selected_trip']['title'] ?? $trip['title']); ?></p>
            <p><strong>Montant:</strong> <?php echo htmlspecialchars($montant); ?> PO</p>
        </div>
        
        <p class="info">Vous allez être redirigé automatiquement dans quelques instants.</p>
        
        <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST" id="cybank_form">
            <input type="hidden" name="transaction" value="<?php echo htmlspecialchars($transaction); ?>">
            <input type="hidden" name="montant" value="<?php echo htmlspecialchars($montant); ?>">
            <input type="hidden" name="vendeur" value="<?php echo htmlspecialchars($vendeur); ?>">
            <input type="hidden" name="retour" value="<?php echo htmlspecialchars($retour); ?>">
            <input type="hidden" name="control" value="<?php echo htmlspecialchars($control); ?>">
        </form>
        
        <script>
            // Soumettre le formulaire automatiquement après un court délai
            setTimeout(function() {
                document.getElementById('cybank_form').submit();
            }, 1500);
        </script>
    </div>
</body>
</html>