<?php
session_start();
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = read_json('data/users.json');
    $login = $_POST['login'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $name = $_POST['name'] ?? '';
    $nickname = $_POST['nickname'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $address = $_POST['address'] ?? '';
    $new_user = [
        'id' => count($data['users']) + 1,
        'login' => $login,
        'password' => $password,
        'role' => 'normal',
        'name' => $name,
        'nickname' => $nickname,
        'birth_date' => $birth_date,
        'address' => $address,
        'registration_date' => date('Y-m-d'),
        'last_login' => null,
        'trips_viewed' => [],
        'trips_purchased' => []
    ];
    $data['users'][] = $new_user;
    write_json('data/users.json', $data);
    $_SESSION['user_id'] = $new_user['id'];
    $_SESSION['user_role'] = $new_user['role'];
    header("Location: index.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="register-container">
        <h2>Inscription</h2>
        <form method="POST">
            <div class="form-group">
                <label for="login">Identifiant</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="nickname">Surnom</label>
                <input type="text" id="nickname" name="nickname">
            </div>
            <div class="form-group">
                <label for="birth_date">Date de naissance</label>
                <input type="date" id="birth_date" name="birth_date" required>
            </div>
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" id="address" name="address" required>
            </div>
            <button type="submit" class="register-button">S'inscrire</button>
        </form>
        <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>