<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to read JSON data
function readJson($file) {
    if (file_exists($file)) {
        $data = file_get_contents($file);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// Load trip data
$trips = readJson('data/trips.json');
$trip_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$trip = null;

// Find the trip by ID
foreach ($trips as $t) {
    if ($t['id'] == $trip_id) {
        $trip = $t;
        break;
    }
}

// If trip not found, redirect to index
if (!$trip) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RuneTrek Adventures - Détails du Voyage</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h1>Détails du Voyage : <?php echo htmlspecialchars($trip['title']); ?></h1>
        <p><strong>Dates :</strong> <?php echo htmlspecialchars($trip['start_date'] . " au " . $trip['end_date']); ?></p>
        <p><strong>Durée :</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
        <p><strong>Prix de base :</strong> $<?php echo htmlspecialchars($trip['price']); ?></p>

        <form action="trip_summary.php" method="POST">
            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
            <h2>Personnaliser les Étapes</h2>
            <?php foreach ($trip['stages'] as $index => $stage): ?>
                <div class="stage">
                    <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
                    <p><strong>Dates :</strong> <?php echo htmlspecialchars($stage['arrival'] . " au " . $stage['departure']); ?></p>
                    
                    <!-- Accommodation Option -->
                    <label for="accommodation_<?php echo $index; ?>">Hébergement :</label>
                    <select name="options[<?php echo $index; ?>][accommodation]" id="accommodation_<?php echo $index; ?>">
                        <option value="hostel">Auberge ($100)</option>
                        <option value="hotel">Hôtel ($200)</option>
                        <option value="cabin">Cabane ($150)</option>
                    </select>

                    <!-- Activity Option -->
                    <label for="activity_<?php echo $index; ?>">Activité :</label>
                    <select name="options[<?php echo $index; ?>][activity]" id="activity_<?php echo $index; ?>">
                        <option value="hiking">Randonnée ($50)</option>
                        <option value="sightseeing">Visite touristique ($30)</option>
                        <option value="skiing">Ski ($80)</option>
                    </select>

                    <!-- Number of Participants -->
                    <label for="participants_<?php echo $index; ?>">Nombre de participants :</label>
                    <input type="number" name="options[<?php echo $index; ?>][participants]" id="participants_<?php echo $index; ?>" value="1" min="1" max="10">
                </div>
            <?php endforeach; ?>
            
            <button type="submit">Voir le Récapitulatif</button>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
