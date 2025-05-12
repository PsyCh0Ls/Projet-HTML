<?php
include 'includes/header.php';
?>

<main>
    <section class="register-section">
        <h2>Inscription</h2>
        <form id="registerForm" action="register_process.php" method="POST" novalidate>
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
    </section>
</main>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {
    let isValid = true;

    // Validation email
    const regEmail = document.getElementById('regEmail');
    const regEmailError = document.getElementById('regEmailError');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(regEmail.value)) {
        regEmailError.textContent = 'Veuillez entrer un email valide.';
        isValid = false;
    } else {
        regEmailError.textContent = '';
    }

    // Validation mot de passe
    const regPassword = document.getElementById('regPassword');
    const regPasswordError = document.getElementById('regPasswordError');
    if (regPassword.value.length < 6) {
        regPasswordError.textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
        isValid = false;
    } else {
        regPasswordError.textContent = '';
    }

    // Validation confirmation mot de passe
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