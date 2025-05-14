<?php
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = trim($_POST['message']);

    // Validation côté serveur
    if (empty($name) || !$email || empty($message)) {
        $error = "Tous les champs sont requis.";
    } elseif (!$email) {
        $error = "Veuillez entrer un email valide.";
    } else {
        // Simulation de l'envoi (à remplacer par un vrai envoi d'email plus tard)
        $success = "Votre message a été envoyé avec succès ! Nous vous répondrons bientôt.";
    }
}
?>

<main>
    <section class="contact-section">
        <div class="contact-container">
            <h2>Contactez-Nous</h2>
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif (isset($success)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form id="contactForm" method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required placeholder="Votre nom">
                    <span id="nameError" class="error"></span>
                    <div class="char-counter">
                        <span id="nameCount">0</span> caractères
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                    <span id="emailError" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="message">Message :</label>
                    <textarea id="message" name="message" required placeholder="Votre message"></textarea>
                    <span id="messageError" class="error"></span>
                    <div class="char-counter">
                        <span id="messageCount">0</span> caractères
                    </div>
                </div>
                <button type="submit" class="cta-button">Envoyer</button>
            </form>
        </div>
    </section>
</main>

<style>
    .contact-section {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
        margin: 0 auto;
        box-sizing: border-box;
    }
    
    .contact-container {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .contact-container h2 {
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
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #555;
        background-color: rgba(255, 255, 255, 0.8);
        box-sizing: border-box;
    }
    
    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }
    
    .char-counter {
        text-align: right;
        font-size: 0.8em;
        color: #ccc;
        margin-top: 5px;
    }
    
    .error {
        color: #ff6b6b;
        font-size: 0.85em;
        display: block;
        margin-top: 5px;
    }
    
    .error-message {
        background-color: rgba(255, 107, 107, 0.2);
        border-left: 3px solid #ff6b6b;
        padding: 10px;
        margin-bottom: 20px;
        color: #ff6b6b;
    }
    
    .success-message {
        background-color: rgba(107, 255, 107, 0.2);
        border-left: 3px solid #6bff6b;
        padding: 10px;
        margin-bottom: 20px;
        color: #6bff6b;
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
    const contactForm = document.getElementById('contactForm');
    const name = document.getElementById('name');
    const nameError = document.getElementById('nameError');
    const nameCount = document.getElementById('nameCount');
    const email = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const message = document.getElementById('message');
    const messageError = document.getElementById('messageError');
    const messageCount = document.getElementById('messageCount');
    
    // Fonction pour mettre à jour le compteur de caractères du nom
    function updateNameCount() {
        const count = name.value.length;
        nameCount.textContent = count;
    }
    
    // Fonction pour mettre à jour le compteur de caractères du message
    function updateMessageCount() {
        const count = message.value.length;
        messageCount.textContent = count;
        
        // Change la couleur en fonction de la longueur
        if (count > 10) {
            messageCount.style.color = '#8BC34A'; // Vert si message suffisamment long
        } else {
            messageCount.style.color = '#ccc';
        }
    }
    
    // Événements pour mettre à jour les compteurs à chaque frappe
    name.addEventListener('input', updateNameCount);
    message.addEventListener('input', updateMessageCount);
    
    // Validation du formulaire
    contactForm.addEventListener('submit', function(event) {
        let isValid = true;
        
        // Validation nom
        if (!name.value.trim()) {
            nameError.textContent = 'Le nom est requis.';
            isValid = false;
        } else {
            nameError.textContent = '';
        }
        
        // Validation email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value)) {
            emailError.textContent = 'Veuillez entrer un email valide.';
            isValid = false;
        } else {
            emailError.textContent = '';
        }
        
        // Validation message
        if (!message.value.trim()) {
            messageError.textContent = 'Le message est requis.';
            isValid = false;
        } else if (message.value.trim().length < 10) {
            messageError.textContent = 'Le message doit contenir au moins 10 caractères.';
            isValid = false;
        } else {
            messageError.textContent = '';
        }
        
        if (!isValid) {
            event.preventDefault();
        }
    });
    
    // Initialiser les compteurs
    updateNameCount();
    updateMessageCount();
});
</script>

<?php
include 'includes/footer.php';
?>
