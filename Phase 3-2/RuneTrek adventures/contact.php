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
        <h2>Contactez-Nous</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form id="contactForm" method="POST" novalidate>
            <div class="form-group">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" required placeholder="Votre nom">
                <span id="nameError" class="error"></span>
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
            </div>
            <button type="submit" class="cta-button">Envoyer</button>
        </form>
    </section>
</main>

<script>
document.getElementById('contactForm').addEventListener('submit', function(event) {
    let isValid = true;

    // Validation nom
    const name = document.getElementById('name');
    const nameError = document.getElementById('nameError');
    if (!name.value.trim()) {
        nameError.textContent = 'Le nom est requis.';
        isValid = false;
    } else {
        nameError.textContent = '';
    }

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

    // Validation message
    const message = document.getElementById('message');
    const messageError = document.getElementById('messageError');
    if (!message.value.trim()) {
        messageError.textContent = 'Le message est requis.';
        isValid = false;
    } else {
        messageError.textContent = '';
    }

    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php
include 'includes/footer.php';
?>