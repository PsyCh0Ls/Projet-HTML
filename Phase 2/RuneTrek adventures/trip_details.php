<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: search.php');
    exit;
}

$trip_id = (int)$_GET['id'];
$trip = get_trip_by_id($trip_id);

if (!$trip) {
    header('Location: search.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_options = $_POST['options'] ?? [];
    $total_price = $trip['price'];
    $selected_stages = [];

    if (isset($trip['stages']) && is_array($trip['stages'])) {
        foreach ($trip['stages'] as $stage) {
            $stage_options = [];
            if (isset($stage['options']) && is_array($stage['options'])) {
                foreach ($stage['options'] as $option) {
                    $selected_value = $selected_options[$stage['id']][$option['name']] ?? ($option['values'][0]['value'] ?? '');
                    $option_price = 0;
                    foreach ($option['values'] as $value) {
                        if ($value['value'] === $selected_value) {
                            $option_price = $value['price'];
                            break;
                        }
                    }
                    $stage_options[$option['name']] = [
                        'value' => $selected_value,
                        'price' => $option_price
                    ];
                    $total_price += $option_price;
                }
            }
            $selected_stages[$stage['id']] = $stage_options;
        }
    }

    $_SESSION['selected_trip'] = [
        'id' => $trip_id,
        'stages' => $selected_stages,
        'total_price' => $total_price
    ];
    header('Location: trip_summary.php');
    exit;
}

$related_trips = array_filter(read_json('data/trips.json')['trips'], function($t) use ($trip) {
    return strtolower($t['region']) == strtolower($trip['region']) && $t['id'] != $trip['id'];
});
?>
<?php include 'includes/header.php'; ?>
<div class="trip-details-page">
    <main>
        <section class="trip-details">
            <h1><?php echo htmlspecialchars($trip['title']); ?></h1>
            <div class="trip-image <?php echo strtolower($trip['region']); ?>-bg"></div>
            <div class="trip-content">
                <p><?php echo htmlspecialchars($trip['description']); ?></p>
                <div class="trip-info">
                    <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
                    <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
                    <p><strong>Durée:</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
                    <p><strong>Prix de base:</strong> <?php echo htmlspecialchars($trip['price']); ?> PO</p>
                </div>
                <?php if (isset($trip['stages']) && is_array($trip['stages']) && !empty($trip['stages'])): ?>
                    <h2>Personnaliser votre voyage</h2>
                    <form method="POST">
                        <?php foreach ($trip['stages'] as $stage): ?>
                            <div class="stage">
                                <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
                                <p><strong>Dates:</strong> <?php echo htmlspecialchars($stage['start_date']); ?> (<?php echo $stage['duration']; ?> jours)</p>
                                <p><strong>Lieu:</strong> <?php echo htmlspecialchars($stage['position']); ?></p>
                                <?php if (isset($stage['options']) && is_array($stage['options']) && !empty($stage['options'])): ?>
                                    <?php foreach ($stage['options'] as $option): ?>
                                        <div class="form-group">
                                            <label for="option-<?php echo $stage['id'] . '-' . htmlspecialchars(str_replace(' ', '-', $option['name'])); ?>">
                                                <?php echo htmlspecialchars($option['name']); ?>
                                            </label>
                                            <select name="options[<?php echo $stage['id']; ?>][<?php echo htmlspecialchars($option['name']); ?>]"
                                                    id="option-<?php echo $stage['id'] . '-' . htmlspecialchars(str_replace(' ', '-', $option['name'])); ?>">
                                                <?php foreach ($option['values'] as $value): ?>
                                                    <option value="<?php echo htmlspecialchars($value['value']); ?>"
                                                            <?php echo isset($value['default']) && $value['default'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($value['value']); ?> (<?php echo $value['price']; ?> PO)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Aucune option disponible pour cette étape.</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="book-now">Voir le récapitulatif</button>
                    </form>
                <?php else: ?>
                    <h2>Réservation directe</h2>
                    <p>Aucune personnalisation disponible pour ce voyage.</p>
                    <a href="booking.php?id=<?php echo $trip['id']; ?>" class="book-now">Réserver maintenant</a>
                <?php endif; ?>
            </div>
        </section>
        <section class="related-trips">
            <h2>Autres voyages dans cette région</h2>
            <div class="trip-cards">
                <?php if (empty($related_trips)): ?>
                    <p>Aucun autre voyage disponible dans cette région.</p>
                <?php else: ?>
                    <?php foreach ($related_trips as $related_trip): ?>
                        <div class="trip-card">
                            <div class="trip-image <?php echo strtolower($related_trip['region']); ?>-bg"></div>
                            <div class="trip-content">
                                <h3><?php echo htmlspecialchars($related_trip['title']); ?></h3>
                                <p><?php echo htmlspecialchars($related_trip['description']); ?></p>
                                <div class="trip-footer">
                                    <span class="price"><?php echo htmlspecialchars($related_trip['price']); ?> PO</span>
                                    <a href="trip_details.php?id=<?php echo $related_trip['id']; ?>" class="view-details">Détails</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
<?php include 'includes/footer.php'; ?>