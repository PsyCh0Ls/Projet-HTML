<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Vérifier si les fichiers inclus existent
$required_files = ['includes/functions.php', 'includes/header.php', 'includes/footer.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Erreur : Le fichier $file n'a pas été trouvé.");
    }
}

require_once 'includes/functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $errors[] = 'Veuillez remplir tous les champs.';
    } else {
        $users_data = read_json('data/users.json');
        if (empty($users_data['users'])) {
            $errors[] = 'Aucun utilisateur trouvé dans la base de données.';
        } else {
            $user = authenticate_user($login, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_login'] = date('Y-m-d H:i:s');
                foreach ($users_data['users'] as &$u) {
                    if ($u['id'] == $user['id']) {
                        $u['last_login'] = $_SESSION['last_login'];
                        break;
                    }
                }
                write_json('data/users.json', $users_data);
                header('Location: index.php');
                exit;
            } else {
                $errors[] = 'Login ou mot de passe incorrect.';
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="auth-page">
    <main>
        <h2>Connexion</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div>
                <label for="login">Login</label>
                <input type="text" id="login" name="login" value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>" required>
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
    </main>
</div>
<?php include 'includes/footer.php'; ?>