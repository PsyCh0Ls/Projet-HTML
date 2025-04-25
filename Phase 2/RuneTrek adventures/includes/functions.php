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
?>
