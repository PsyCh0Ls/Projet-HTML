<?php
include 'includes/header.php';
?>

<main>
    <section class="login-section">
        <div class="login-container">
            <h2>Connexion</h2>
            <form id="loginForm" action="login_process.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                    <span id="emailError" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required minlength="6">
                        <i id="togglePassword" class="toggle-password">👁️</i>
                    </div>
                    <span id="passwordError" class="error"></span>
                    <div id="passwordStrength" class="strength-meter">
                        <span class="character-count">0/6</span>
                    </div>
                </div>
                <button type="submit" class="cta-button">Se connecter</button>
            </form>
        </div>
    </section>
</main>

<style>
    .login-section {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
        margin: 0 auto;
        box-sizing: border-box;
    }
    
    .login-container {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .login-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #fff;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #fff;
    }
    
    .form-group input {
        width: 100%;
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #555;
        background-color: rgba(255, 255, 255, 0.8);
        box-sizing: border-box;
    }
    
    .password-container {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        user-select: none;
        color: #333;
    }
    
    .strength-meter {
        margin-top: 5px;
        font-size: 0.75em;
        color: #ccc;
        text-align: right;
    }
    
    .error {
        color: #ff6b6b;
        font-size: 0.85em;
        display: block;
        margin-top: 5px;
    }
    
    .cta-button {
        width: 100%;
        padding: 12px;
        background-color: #FFC107;
        border: none;
        border-radius: 4px;
        color: #333;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .cta-button:hover {
        background-color: #FFD54F;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const email = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const password = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    const togglePassword = document.getElementById('togglePassword');
    const characterCount = document.querySelector('.character-count');

    // Fonction pour mettre à jour le compteur de caractères
    function updateCharacterCount() {
        const count = password.value.length;
        characterCount.textContent = `${count}/6`;
        
        if (count >= 6) {
            characterCount.style.color = '#8BC34A'; // Vert si longueur suffisante
        } else {
            characterCount.style.color = '#ccc';
        }
    }

    // Événement pour afficher/masquer le mot de passe
    togglePassword.addEventListener('click', function() {
        if (password.type === 'password') {
            password.type = 'text';
            togglePassword.textContent = '🔒';
        } else {
            password.type = 'password';
            togglePassword.textContent = '👁️';
        }
    });

    // Événement pour mettre à jour le compteur à chaque frappe
    password.addEventListener('input', updateCharacterCount);

    // Validation du formulaire
    loginForm.addEventListener('submit', function(event) {
        let isValid = true;
        
        // Validation email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailPattern.test(email.value)) {
            emailError.textContent = 'Veuillez entrer un email valide.';
            isValid = false;
        } else {
            emailError.textContent = '';
        }
        
        // Validation mot de passe
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

    // Initialiser le compteur
    updateCharacterCount();
});
</script>

<?php
include 'includes/footer.php';
?>
