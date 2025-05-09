<?php
function read_json($file) {
    if (!file_exists($file)) {
        return ['users' => [], 'trips' => [], 'bookings' => [], 'payments' => []][$file] ?? [];
    }
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

function write_json($file, $data) {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($file, $json_data);
}

function authenticate_user($login, $password) {
    $users = read_json('data/users.json')['users'];
    foreach ($users as $user) {
        if ($user['login'] === $login && password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}

function generate_transaction_id() {
    return 'TX' . uniqid();
}
?>