<?php
session_start();
require_once 'includes/functions.php';

if (is_authenticated()) {
    header("Location: index.php");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = read_json('data/users.json');
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['confirm_password'] ?? '';
    $name = $_POST['name'] ?? '';
    $nickname = $_POST['nickname'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Validation côté serveur
    if (strlen($login) < 7) {
        $error = "L'identifiant doit contenir au moins 7 caractères.";
    } elseif (strlen($password) < 7) {
        $error = "Le mot de passe doit contenir au moins 7 caractères.";
    } elseif ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (empty($name) || strlen($name) < 3) {
        $error = "Le nom doit contenir au moins 3 caractères.";
    } elseif (empty($birth_date)) {
        $error = "La date de naissance est requise.";
    } elseif (empty($address)) {
        $error = "L'adresse est requise.";
    } else {
        // Vérifier si le login existe déjà
        $login_exists = false;
        foreach ($data['users'] as $user) {
            if ($user['login'] === $login) {
                $login_exists = true;
                break;
            }
        }
        
        if ($login_exists) {
            $error = "Cet identifiant est déjà utilisé.";
        } else {
            // Toutes les validations sont passées, créer le nouvel utilisateur
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $new_user = [
                'id' => count($data['users']) + 1,
                'login' => $login,
                'password' => $password_hash,
                'role' => 'normal',
                'name' => $name,
                'nickname' => $nickname,
                'birth_date' => $birth_date,
                'address' => $address,
                'registration_date' => date('Y-m-d'),
                'last_login' => date('Y-m-d H:i:s'),
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
    }
}
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="register-container">
        <h2>Inscription</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" id="register-form" novalidate>
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
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" id="name" name="name" maxlength="100" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="nickname">Surnom</label>
                <input type="text" id="nickname" name="nickname" maxlength="30" value="<?php echo isset($_POST['nickname']) ? htmlspecialchars($_POST['nickname']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="birth_date">Date de naissance</label>
                <input type="date" id="birth_date" name="birth_date" value="<?php echo isset($_POST['birth_date']) ? htmlspecialchars($_POST['birth_date']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" id="address" name="address" maxlength="200" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>
            </div>
            <button type="submit" class="register-button">S'inscrire</button>
        </form>
        <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>