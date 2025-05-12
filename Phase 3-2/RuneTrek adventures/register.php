<?php
session_start();
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validation
    if ($password !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Simulation d'enregistrement (à remplacer par une base de données)
        // Ici, on pourrait insérer dans une table users
        $_SESSION['message'] = "Inscription réussie ! Connectez-vous.";
        header('Location: login.php');
        exit;
    }
}
?>

<main>
    <section class="register-section">
        <h2>Inscription</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (isset($_SESSION['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <form id="registerForm" method="POST" novalidate>
            <div class="form-group">
                <label for="regEmail">Email :</label>
                <input type="email" id="regEmail" name="email" required placeholder="votre@email.com">
                <span id="regEmailError" class="error"></span>
            </div>
            <div class="form-group">
                <label for="regPassword">Mot de passe :</label>
                <input type="password" id="regPassword" name="password" required minlength="6">
                <span id="regPasswordError" class="error"></span>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirmer le mot de passe :</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <span id="confirmPasswordError" class="error"></span>
            </div>
            <button type="submit" class="cta-button">S'inscrire</button>
        </form>
        <p><a href="login.php">Déjà inscrit ? Connectez-vous</a></p>
    </section>
</main>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {
    let isValid = true;

    const regEmail = document.getElementById('regEmail');
    const regEmailError = document.getElementById('regEmailError');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(regEmail.value)) {
        regEmailError.textContent = 'Veuillez entrer un email valide.';
        isValid = false;
    } else {
        regEmailError.textContent = '';
    }

    const regPassword = document.getElementById('regPassword');
    const regPasswordError = document.getElementById('regPasswordError');
    if (regPassword.value.length < 6) {
        regPasswordError.textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
        isValid = false;
    } else {
        regPasswordError.textContent = '';
    }

    const confirmPassword = document.getElementById('confirmPassword');
    const confirmPasswordError = document.getElementById('confirmPasswordError');
    if (confirmPassword.value !== regPassword.value) {
        confirmPasswordError.textContent = 'Les mots de passe ne correspondent pas.';
        isValid = false;
    } else {
        confirmPasswordError.textContent = '';
    }

    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php
include 'includes/footer.php';
?>