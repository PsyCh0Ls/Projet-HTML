<?php
include 'includes/header.php';
?>

<main>
    <section class="login-section">
        <h2>Connexion</h2>
        <form id="loginForm" action="login_process.php" method="POST" novalidate>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com">
                <span id="emailError" class="error"></span>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required minlength="6">
                <span id="passwordError" class="error"></span>
            </div>
            <button type="submit" class="cta-button">Se connecter</button>
        </form>
    </section>
</main>

<script>
document.getElementById('loginForm').addEventListener('submit', function(event) {
    let isValid = true;

    // Validation email
    const email = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
        emailError.textContent = 'Veuillez entrer un email valide.';
        isValid = false;
    } else {
        emailError.textContent = '';
    }

    // Validation mot de passe
    const password = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    if (password.value.length < 6) {
        passwordError.textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
        isValid = false;
    } else {
        passwordError.textContent = '';
    }

    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php
include 'includes/footer.php';
?>