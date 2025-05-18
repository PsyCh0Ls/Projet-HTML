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

// Mode lecture seule pour afficher un voyage déjà acheté
$readonly = isset($_GET['readonly']) && $_GET['readonly'] == '1';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$readonly) {
    $selected_options = [];
    $total_price = $trip['price']; // Prix de base
    
    // Parcourir les étapes et les options
    foreach ($trip['stages'] as $stage) {
        $stage_id = $stage['id'];
        $selected_options[$stage_id] = [];
        
        foreach ($stage['options'] as $option) {
            $option_name = $option['name'];
            
            if (isset($_POST["option_{$stage_id}_{$option_name}"])) {
                $selected_value = $_POST["option_{$stage_id}_{$option_name}"];
                
                // Stocker l'option sélectionnée
                $selected_options[$stage_id][$option_name] = [
                    'value' => $selected_value,
                    'persons' => isset($_POST["persons_{$stage_id}_{$option_name}"]) ? (int)$_POST["persons_{$stage_id}_{$option_name}"] : 1
                ];
                
                // Ajouter le prix de l'option au total
                foreach ($option['values'] as $value) {
                    if ($value['value'] === $selected_value) {
                        $persons_count = isset($_POST["persons_{$stage_id}_{$option_name}"]) ? (int)$_POST["persons_{$stage_id}_{$option_name}"] : 1;
                        $total_price += $value['price'] * $persons_count;
                        
                        // Stocker également le prix de l'option pour référence
                        $selected_options[$stage_id][$option_name]['price'] = $value['price'] * $persons_count;
                        break;
                    }
                }
            }
        }
    }
    
    // Stocker les options et le prix total en session
    $_SESSION['selected_trip'] = [
        'id' => $trip_id,
        'stages' => $selected_options,
        'total_price' => $total_price
    ];
    
    // Réinitialiser les flags pour l'ajout au panier
    unset($_SESSION['added_to_cart']);
    unset($_SESSION['notification_shown']);
    
    // Si l'utilisateur a demandé d'ajouter au panier via le formulaire
    if (isset($_POST['add_to_cart']) && $_POST['add_to_cart'] == '1') {
        // Ajouter au panier
        add_to_cart($trip_id, $selected_options);
        
        // Marquer comme ajouté
        $_SESSION['added_to_cart'] = true;
        
        // Rediriger vers le panier
        header('Location: cart.php');
        exit;
    }
    
    // Rediriger vers la page de récapitulatif
    header('Location: trip_summary.php');
    exit;
}

// Préremplir le formulaire avec les options précédemment sélectionnées
$selected_options = isset($_SESSION['selected_trip']) && $_SESSION['selected_trip']['id'] == $trip_id 
    ? $_SESSION['selected_trip']['stages'] 
    : [];
?>

<?php include 'includes/header.php'; ?>
<div class="trip-details-page">
    <div class="trip-details">
        <h1><?php echo htmlspecialchars($trip['title']); ?></h1>
        <div class="trip-image <?php echo strtolower($trip['region']); ?>-bg"></div>
        <div class="trip-content">
            <div class="trip-info">
                <p><strong>Région:</strong> <?php echo htmlspecialchars($trip['region']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($trip['description']); ?></p>
                <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($trip['start_date']); ?></p>
                <p><strong>Durée:</strong> <?php echo htmlspecialchars($trip['duration']); ?> jours</p>
                <p><strong>Prix de base:</strong> <?php echo htmlspecialchars($trip['price']); ?> PO</p>
            </div>
            
            <h2>Étapes du voyage</h2>
            
            <?php if ($readonly): ?>
                <!-- Mode lecture seule -->
                <?php foreach ($trip['stages'] as $stage): ?>
                    <div class="stage">
                        <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($stage['start_date']); ?></p>
                        <p><strong>Durée:</strong> <?php echo htmlspecialchars($stage['duration']); ?> jours</p>
                        <p><strong>Lieu:</strong> <?php echo htmlspecialchars($stage['position']); ?></p>
                        
                        <?php if (isset($selected_options[$stage['id']])): ?>
                            <h4>Options sélectionnées:</h4>
                            <ul>
                                <?php foreach ($selected_options[$stage['id']] as $option_name => $option_data): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars($option_name); ?>:</strong> 
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
                    </div>
                <?php endforeach; ?>
                
                <a href="profile.php" class="book-now">Retour au profil</a>
                
            <?php else: ?>
                <!-- Mode édition -->
                <form method="POST" id="trip-options-form">
                    <!-- Champ caché pour l'ajout au panier -->
                    <input type="hidden" name="add_to_cart" id="add_to_cart_field" value="0">
                    
                    <!-- Champ caché pour le prix calculé -->
                    <input type="hidden" name="calculated_price" id="calculated-price" value="<?php echo $trip['price']; ?>">
                    
                    <!-- Nombre global de personnes -->
                    <div class="form-group global-persons">
                        <label for="global_persons">Nombre de personnes:</label>
                        <select name="global_persons" id="global_persons">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>"
                                    <?php 
                                        // Obtenir le nombre de personnes précédemment sélectionné, s'il existe
                                        $global_persons = 1; // Valeur par défaut
                                        if (!empty($selected_options)) {
                                            foreach ($selected_options as $stage_options) {
                                                foreach ($stage_options as $option_data) {
                                                    if (is_array($option_data) && isset($option_data['persons'])) {
                                                        $global_persons = $option_data['persons'];
                                                        break 2; // Sortir des deux boucles
                                                    }
                                                }
                                            }
                                        }
                                        
                                        if ($global_persons == $i) {
                                            echo 'selected';
                                        }
                                    ?>
                                >
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <?php foreach ($trip['stages'] as $stage): ?>
                        <div class="stage">
                            <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($stage['start_date']); ?></p>
                            <p><strong>Durée:</strong> <?php echo htmlspecialchars($stage['duration']); ?> jours</p>
                            <p><strong>Lieu:</strong> <?php echo htmlspecialchars($stage['position']); ?></p>
                            
                            <?php if (isset($stage['options'])): ?>
                                <?php foreach ($stage['options'] as $option): ?>
                                    <div class="form-group">
                                        <label for="option_<?php echo $stage['id']; ?>_<?php echo $option['name']; ?>">
                                            <?php echo htmlspecialchars($option['name']); ?>:
                                        </label>
                                        <select name="option_<?php echo $stage['id']; ?>_<?php echo $option['name']; ?>" id="option_<?php echo $stage['id']; ?>_<?php echo $option['name']; ?>" class="option-select">
                                            <?php foreach ($option['values'] as $value): ?>
                                                <option 
                                                    value="<?php echo htmlspecialchars($value['value']); ?>" 
                                                    data-price="<?php echo htmlspecialchars($value['price']); ?>"
                                                    <?php 
                                                        // Sélectionner l'option précédemment choisie ou celle par défaut
                                                        if (isset($selected_options[$stage['id']][$option['name']]) && 
                                                            (
                                                                (is_array($selected_options[$stage['id']][$option['name']]) && 
                                                                $selected_options[$stage['id']][$option['name']]['value'] === $value['value']) ||
                                                                (!is_array($selected_options[$stage['id']][$option['name']]) && 
                                                                $selected_options[$stage['id']][$option['name']] === $value['value'])
                                                            )
                                                        ) {
                                                            echo 'selected';
                                                        } elseif (!isset($selected_options[$stage['id']][$option['name']]) && isset($value['default']) && $value['default']) {
                                                            echo 'selected';
                                                        }
                                                    ?>
                                                >
                                                    <?php echo htmlspecialchars($value['value']); ?> (<?php echo htmlspecialchars($value['price']); ?> PO)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <?php if (isset($option['nb_persons']) && $option['nb_persons']): ?>
                                            <!-- Ne pas afficher le sélecteur individuel de personnes - on utilise la valeur globale -->
                                            <input type="hidden" name="persons_<?php echo $stage['id']; ?>_<?php echo $option['name']; ?>" class="persons-input" value="1">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="form-actions">
                        <button type="submit" class="book-now">Voir le récapitulatif</button>
                        <button type="button" id="add-to-cart-button" class="add-to-cart-button">Ajouter au panier</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Styles pour le bouton d'ajout au panier et la section d'actions */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.add-to-cart-button {
    background-color: #FFD700;
    color: #2F3136;
    padding: 10px 20px;
    border-radius: 4px;
    border: none;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
}

.add-to-cart-button:hover {
    background-color: #FFC107;
}

.add-to-cart-button::before {
    content: "🛒";
    margin-right: 8px;
}

.global-persons {
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #1E88E5;
}

/* Mode sombre */
.dark-mode .global-persons {
    background-color: #333;
    border-left-color: #64B5F6;
}

.dark-mode .add-to-cart-button {
    background-color: #FFD700;
    color: #333;
}

.dark-mode .add-to-cart-button:hover {
    background-color: #FFC107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire pour le bouton d'ajout au panier
    const addToCartButton = document.getElementById('add-to-cart-button');
    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            // Mettre à jour le champ caché pour indiquer l'ajout au panier
            const addToCartField = document.getElementById('add_to_cart_field');
            if (addToCartField) {
                addToCartField.value = '1';
            }
            
            // Soumettre le formulaire
            const form = document.getElementById('trip-options-form');
            if (form) {
                form.submit();
            }
        });
    }
    
    // Gestionnaire pour le nombre global de personnes
    const globalPersonsSelect = document.getElementById('global_persons');
    if (globalPersonsSelect) {
        globalPersonsSelect.addEventListener('change', function() {
            // Mettre à jour tous les champs cachés de personnes
            const personsInputs = document.querySelectorAll('.persons-input');
            const personCount = this.value;
            
            personsInputs.forEach(input => {
                input.value = personCount;
            });
            
            // Recalculer le prix si le script de calcul de prix est présent
            if (typeof recalculatePrice === 'function') {
                recalculatePrice();
            } else {
                // Forcer la mise à jour des sélecteurs d'options pour déclencher les calculs
                const optionSelects = document.querySelectorAll('.option-select');
                if (optionSelects.length > 0) {
                    // Déclencher un événement change sur le premier sélecteur
                    const event = new Event('change');
                    optionSelects[0].dispatchEvent(event);
                }
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>