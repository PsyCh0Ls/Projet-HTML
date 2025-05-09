<?php
session_start();
require_once 'includes/functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($login) || empty($password) || empty($name) || empty($email)) {
        $errors[] = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email invalide.';
    } else {
        $users = read_json('data/users.json')['users'];
        foreach ($users as $user) {
            if ($user['login'] === $login) {
                $errors[] = 'Ce login est déjà utilisé.';
                break;
            }
            if ($user['email'] === $email) {
                $errors[] = 'Cet email est déjà utilisé.';
                break;
            }
        }
        if (empty($errors)) {
            $new_user = [
                'id' => count($users) + 1,
                'login' => $login,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'name' => $name,
                'email' => $email,
                'role' => 'normal',
                'last_login' => date('Y-m-d H:i:s'),
                'trips_purchased' => []
            ];
            $users[] = $new_user;
            write_json('data/users.json', ['users' => $users]);
            $success = true;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="auth-page">
    <main>
        <h2>Inscription</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success">Inscription réussie ! <a href="login.php">Connectez-vous</a>.</p>
        <?php else: ?>
            <form method="POST">
                <div>
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>" required>
                </div>
                <div>
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div>
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <button type="submit">S'inscrire</button>
            </form>
        <?php endif; ?>
        <p>Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
    </main>
</div>
<?php include 'includes/footer.php'; ?>