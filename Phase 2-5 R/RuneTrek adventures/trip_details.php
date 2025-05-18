<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/cart_functions.php';

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

// Vérifier si le mode lecture seule est activé
$readonly = isset($_GET['readonly']) && $_GET['readonly'] == '1';

// Si l'utilisateur est connecté, enregistrer ce voyage dans les vues récentes
if (is_authenticated() && !$readonly) {
    // Logique pour enregistrer dans les vues récentes si nécessaire
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_authenticated()) {
        header('Location: login.php');
        exit;
    }
    
    $selected_options = $_POST['options'] ?? [];
    $total_price = $trip['price'];
    $selected_stages = [];

    if (isset($trip['stages']) && is_array($trip['stages'])) {
        foreach ($trip['stages'] as $stage) {
            $stage_options = [];
            if (isset($stage['options']) && is_array($stage['options'])) {
                foreach ($stage['options'] as $option) {
                    $selected_value = $selected_options[$stage['id']][$option['name']] ?? '';
                    $option_price = 0;
                    
                    // Si aucune valeur n'est sélectionnée, prendre la valeur par défaut
                    if (empty($selected_value) && isset($option['values'])) {
                        foreach ($option['values'] as $value) {
                            if (isset($value['default']) && $value['default']) {
                                $selected_value = $value['value'];
                                $option_price = $value['price'];
                                break;
                            }
                        }
                    } else if (!empty($selected_value) && isset($option['values'])) {
                        // Trouver le prix de l'option sélectionnée
                        foreach ($option['values'] as $value) {
                            if ($value['value'] === $selected_value) {
                                $option_price = $value['price'];
                                break;
                            }
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
    
    // Vérifier si l'utilisateur souhaite ajouter ce voyage au panier
    if (isset($_POST['add_to_cart']) && $_POST['add_to_cart'] == '1') {
        // Nous passons aussi le prix total calculé
        add_to_cart($trip_id, $selected_stages, $total_price);
        
        // Rediriger vers le panier au lieu du récapitulatif
        header('Location: cart.php');
        exit;
    }
    
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
                <?php if (isset($trip['stages']) && is_array($trip['stages']) && !empty($trip['stages']) && !$readonly): ?>
                    <h2>Personnaliser votre voyage</h2>
                    <form method="POST">
                        <div class="cart-status">
                            <p>Modifiez les options pour ajouter ce voyage au panier</p>
                        </div>
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
                        <input type="hidden" name="add_to_cart" id="add_to_cart_field" value="0">
                        <input type="hidden" name="calculated_price" id="calculated-price" value="<?php echo $trip['price']; ?>">
                        <div class="trip-actions">
                            <button type="submit" class="view-summary">Voir le récapitulatif</button>
                            <?php if (is_authenticated()): ?>
                                <button type="button" class="add-to-cart-button" data-id="<?php echo $trip['id']; ?>">Ajouter au panier</button>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php elseif ($readonly): ?>
                    <h2>Détails du voyage acheté</h2>
                    <p class="readonly-notice">Ce voyage fait partie de vos achats. Les options ne peuvent plus être modifiées.</p>
                    <?php foreach ($trip['stages'] as $stage): ?>
                        <div class="stage readonly">
                            <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
                            <p><strong>Lieu:</strong> <?php echo htmlspecialchars($stage['position']); ?></p>
                            <?php if (isset($stage['options']) && is_array($stage['options']) && !empty($stage['options'])): ?>
                                <div class="options-list">
                                    <h4>Options sélectionnées:</h4>
                                    <ul>
                                        <?php foreach ($stage['options'] as $option): ?>
                                            <li>
                                                <strong><?php echo htmlspecialchars($option['name']); ?>:</strong>
                                                <?php 
                                                    // Afficher l'option par défaut
                                                    foreach ($option['values'] as $value) {
                                                        if (isset($value['default']) && $value['default']) {
                                                            echo htmlspecialchars($value['value']) . ' (' . $value['price'] . ' PO)';
                                                            break;
                                                        }
                                                    }
                                                ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <p>Aucune option disponible pour cette étape.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="trip-actions">
                        <a href="profile.php" class="back-to-profile">Retour au profil</a>
                    </div>
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

<style>
    .trip-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .add-to-cart-button {
        background-color: #FFD700;
        color: #2F3136;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    
    .add-to-cart-button:hover {
        background-color: #FFC107;
        transform: translateY(-2px);
    }
    
    .view-summary {
        background-color: #1E88E5;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    
    .view-summary:hover {
        background-color: #1976D2;
    }
    
    .readonly-notice {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .stage.readonly {
        background-color: #f9f9f9;
        border-left: 3px solid #ccc;
    }
    
    .options-list ul {
        list-style: none;
        padding-left: 15px;
    }
    
    .options-list li {
        margin-bottom: 8px;
    }
    
    .back-to-profile {
        background-color: #6c757d;
        color: white;
        padding: 10px 15px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
    }
    
    .cart-status {
        padding: 10px;
        margin: 15px 0;
        background-color: #f8f9fa;
        border-radius: 4px;
        text-align: center;
        color: #666;
        transition: all 0.3s;
    }
    
    .cart-status.active {
        background-color: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
        border-left: 4px solid #4CAF50;
    }
    
    .option-modified {
        background-color: rgba(255, 215, 0, 0.1);
        border-radius: 4px;
        padding: 5px;
        transition: background-color 0.3s;
    }
    
    /* Animation pour le bouton */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .button-pulse {
        animation: pulse 0.5s ease;
    }
    
    /* Mode sombre */
    .dark-mode .cart-status {
        background-color: #2a2a2a;
        color: #aaa;
    }
    
    .dark-mode .cart-status.active {
        background-color: rgba(76, 175, 80, 0.05);
        color: #81C784;
    }
    
    .dark-mode .option-modified {
        background-color: rgba(255, 215, 0, 0.05);
    }
    
    .dark-mode .stage.readonly {
        background-color: #2a2a2a;
        border-left-color: #444;
    }
    
    .dark-mode .readonly-notice {
        background-color: rgba(220, 53, 69, 0.1);
        color: #f8d7da;
    }
</style>

<?php include 'includes/footer.php'; ?>