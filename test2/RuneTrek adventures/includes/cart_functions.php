<?php
/**
 * Fonctions de gestion du panier
 */

/**
 * Initialise le panier dans la session si nécessaire
 */
function initialize_cart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [
            'items' => [],
            'total' => 0,
            'count' => 0,
            'last_updated' => time()
        ];
    }
}

/**
 * Ajoute un voyage au panier
 * 
 * @param int $trip_id ID du voyage
 * @param array $options Options sélectionnées pour le voyage
 * @return bool Succès de l'opération
 */
function add_to_cart($trip_id, $options = []) {
    require_once 'functions.php';
    initialize_cart();
    
    // Récupérer les détails du voyage
    $trip = get_trip_by_id($trip_id);
    if (!$trip) {
        return false;
    }
    
    // Vérifier si le voyage est déjà dans le panier
    $existing_item_index = -1;
    foreach ($_SESSION['cart']['items'] as $index => $item) {
        if ($item['id'] == $trip_id) {
            $existing_item_index = $index;
            break;
        }
    }
    
    // Calculer le prix total avec les options
    $total_price = calculate_trip_price($trip, $options);
    
    // Si le voyage existe déjà, mettre à jour les options et le prix
    if ($existing_item_index !== -1) {
        $_SESSION['cart']['items'][$existing_item_index]['options'] = $options;
        $_SESSION['cart']['items'][$existing_item_index]['price'] = $total_price;
    } else {
        // Sinon, ajouter le nouveau voyage
        $_SESSION['cart']['items'][] = [
            'id' => $trip_id,
            'title' => $trip['title'],
            'region' => $trip['region'],
            'options' => $options,
            'price' => $total_price,
            'added_at' => time()
        ];
        
        // Incrémenter le compteur d'articles
        $_SESSION['cart']['count']++;
    }
    
    // Recalculer le total du panier
    update_cart_total();
    
    return true;
}

/**
 * Supprime un voyage du panier
 * 
 * @param int $trip_id ID du voyage à supprimer
 * @return bool Succès de l'opération
 */
function remove_from_cart($trip_id) {
    initialize_cart();
    
    $found = false;
    foreach ($_SESSION['cart']['items'] as $index => $item) {
        if ($item['id'] == $trip_id) {
            unset($_SESSION['cart']['items'][$index]);
            $found = true;
            break;
        }
    }
    
    if ($found) {
        // Réindexer le tableau
        $_SESSION['cart']['items'] = array_values($_SESSION['cart']['items']);
        
        // Mettre à jour le compteur et le total
        $_SESSION['cart']['count'] = count($_SESSION['cart']['items']);
        update_cart_total();
        
        return true;
    }
    
    return false;
}

/**
 * Vide complètement le panier
 */
function clear_cart() {
    $_SESSION['cart'] = [
        'items' => [],
        'total' => 0,
        'count' => 0,
        'last_updated' => time()
    ];
}

/**
 * Met à jour le prix total du panier
 */
function update_cart_total() {
    $total = 0;
    
    foreach ($_SESSION['cart']['items'] as $item) {
        $total += $item['price'];
    }
    
    $_SESSION['cart']['total'] = $total;
    $_SESSION['cart']['last_updated'] = time();
}

/**
 * Calcule le prix total d'un voyage avec ses options
 * 
 * @param array $trip Données du voyage
 * @param array $options Options sélectionnées
 * @return float Prix total calculé
 */
function calculate_trip_price($trip, $options) {
    $base_price = isset($trip['price']) ? $trip['price'] : 0;
    $total_price = $base_price;
    
    // Si aucune option n'est spécifiée, retourner le prix de base
    if (empty($options)) {
        return $total_price;
    }
    
    // Calculer le prix en fonction des options sélectionnées
    if (isset($trip['stages'])) {
        foreach ($trip['stages'] as $stage) {
            $stage_id = $stage['id'];
            
            // Vérifier si des options sont définies pour cette étape
            if (isset($options[$stage_id]) && isset($stage['options'])) {
                foreach ($stage['options'] as $option_index => $option) {
                    $option_name = $option['name'];
                    
                    // Vérifier si cette option a été sélectionnée
                    if (isset($options[$stage_id][$option_name])) {
                        $selected_value = $options[$stage_id][$option_name];
                        
                        // Si c'est un tableau, prendre la valeur
                        if (is_array($selected_value) && isset($selected_value['value'])) {
                            $selected_value = $selected_value['value'];
                        }
                        
                        // Chercher le prix de l'option sélectionnée
                        if (isset($option['values'])) {
                            foreach ($option['values'] as $value) {
                                if (isset($value['value']) && $value['value'] == $selected_value) {
                                    // Ajouter le prix de cette option au total
                                    $total_price += $value['price'];
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    return $total_price;
}

/**
 * Récupère le contenu actuel du panier
 * 
 * @return array Contenu du panier
 */
function get_cart() {
    initialize_cart();
    return $_SESSION['cart'];
}

/**
 * Vérifie si un voyage est dans le panier
 * 
 * @param int $trip_id ID du voyage
 * @return bool True si le voyage est dans le panier
 */
function is_in_cart($trip_id) {
    initialize_cart();
    
    foreach ($_SESSION['cart']['items'] as $item) {
        if ($item['id'] == $trip_id) {
            return true;
        }
    }
    
    return false;
}

/**
 * Sauvegarde le panier en cours dans les voyages achetés de l'utilisateur
 * 
 * @return bool Succès de l'opération
 */
function save_cart_to_purchases() {
    initialize_cart();
    
    if (empty($_SESSION['cart']['items']) || !isset($_SESSION['user_id'])) {
        return false;
    }
    
    require_once 'functions.php';
    
    // Ajouter chaque voyage du panier aux voyages achetés de l'utilisateur
    $user_id = $_SESSION['user_id'];
    $trips = [];
    $bookings_data = read_json('data/bookings.json');
    $bookings = isset($bookings_data['bookings']) ? $bookings_data['bookings'] : [];
    $payments_data = read_json('data/payments.json');
    $payments = isset($payments_data['payments']) ? $payments_data['payments'] : [];
    
    foreach ($_SESSION['cart']['items'] as $item) {
        // Créer une nouvelle réservation
        $new_booking = [
            'id' => count($bookings) + 1,
            'user_id' => $user_id,
            'trip_id' => $item['id'],
            'booking_date' => date('Y-m-d'),
            'options' => $item['options']
        ];
        
        // Créer un nouveau paiement
        $new_payment = [
            'id' => count($payments) + 1,
            'user_id' => $user_id,
            'trip_id' => $item['id'],
            'amount' => $item['price'],
            'date' => date('Y-m-d H:i:s'),
            'status' => 'completed',
            'options' => $item['options'],
            'transaction_id' => 'CART' . time() . rand(1000, 9999)
        ];
        
        $bookings[] = $new_booking;
        $payments[] = $new_payment;
        $trips[] = $item['id'];
    }
    
    // Sauvegarder les nouvelles réservations et paiements
    $bookings_data['bookings'] = $bookings;
    $payments_data['payments'] = $payments;
    write_json('data/bookings.json', $bookings_data);
    write_json('data/payments.json', $payments_data);
    
    // Mettre à jour la liste des voyages achetés de l'utilisateur
    $users_data = read_json('data/users.json');
    foreach ($users_data['users'] as &$user) {
        if ($user['id'] == $user_id) {
            if (!isset($user['trips_purchased'])) {
                $user['trips_purchased'] = [];
            }
            $user['trips_purchased'] = array_merge($user['trips_purchased'], $trips);
            break;
        }
    }
    write_json('data/users.json', $users_data);
    
    // Vider le panier
    clear_cart();
    
    return true;
}

/**
 * Renvoie le nombre d'articles dans le panier sous format JSON
 */
function get_cart_count_json() {
    initialize_cart();
    header('Content-Type: application/json');
    echo json_encode(['count' => $_SESSION['cart']['count']]);
    exit;
}