<?php
function read_json($file) {
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return [];
}

function write_json($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function is_authenticated() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_authenticated() && $_SESSION['user_role'] === 'admin';
}

function require_auth() {
    if (!is_authenticated()) {
        header("Location: login.php");
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header("Location: index.php");
        exit;
    }
}

function get_user_by_id($user_id) {
    $data = read_json('data/users.json');
    foreach ($data['users'] as $user) {
        if ($user['id'] == $user_id) {
            return $user;
        }
    }
    return null;
}

function get_trip_by_id($trip_id) {
    $data = read_json('data/trips.json');
    foreach ($data['trips'] as $trip) {
        if ($trip['id'] == $trip_id) {
            return $trip;
        }
    }
    return null;
}

// Ajouter au panier
function add_to_cart($user_id, $trip_id, $options) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $trip = get_trip_by_id($trip_id);
    if ($trip) {
        $total_price = $trip['price'];
        $selected_options = [];
        foreach ($trip['stages'] as $stage) {
            foreach ($stage['options'] as $option) {
                $option_name = $option['name'];
                if (isset($options[$stage['id']][$option_name])) {
                    $selected_value = $options[$stage['id']][$option_name];
                    foreach ($option['values'] as $value) {
                        if ($value['value'] === $selected_value && !$value['default']) {
                            $total_price += $value['price'];
                            $selected_options[$stage['id']][$option_name] = $selected_value;
                        }
                    }
                }
            }
        }
        $_SESSION['cart'][] = [
            'user_id' => $user_id,
            'trip_id' => $trip_id,
            'title' => $trip['title'],
            'region' => $trip['region'],
            'options' => $selected_options,
            'total_price' => $total_price
        ];
    }
}

// Supprimer du panier
function remove_from_cart($index) {
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
}

// Récupérer le panier
function get_cart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}
?>