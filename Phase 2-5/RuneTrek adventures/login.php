<?php
session_start();
require_once 'includes/functions.php';

if (is_authenticated()) {
    header("Location: index.php");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validation côté serveur
    if (strlen($login) < 7) {
        $error = "L'identifiant doit contenir au moins 7 caractères.";
    } elseif (strlen($password) < 7) {
        $error = "Le mot de passe doit contenir au moins 7 caractères.";
    } else {
        $data = read_json('data/users.json');
        $user_found = false;
        
        foreach ($data['users'] as $user) {
            if ($user['login'] === $login && password_verify($password, $user['password'])) {
                $user_found = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                
                // Mettre à jour la date de dernière connexion
                foreach ($data['users'] as &$u) {
                    if ($u['id'] == $user['id']) {
                        $u['last_login'] = date('Y-m-d H:i:s');
                        break;
                    }
                }
                write_json('data/users.json', $data);
                
                header("Location: index.php");
                exit;
            }
        }
        
        if (!$user_found) {
            $error = "Identifiants incorrects.";
        }
    }
}
?>
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="login-container">
        <h2>Connexion</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" id="login-form" novalidate>
            <div class="form-group">
                <label for="login">Identifiant</label>
                <input type="text" id="login" name="login" maxlength="30" value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>" required>
                <small class="form-text">L'identifiant doit contenir au moins 7 caractères</small>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" maxlength="50" required>
                <small class="form-text">Le mot de passe doit contenir au moins 7 caractères</small>
            </div>
            <button type="submit" class="login-button">Se connecter</button>
        </form>
        <p>Pas de compte ? <a href="register.php">S'inscrire</a></p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>