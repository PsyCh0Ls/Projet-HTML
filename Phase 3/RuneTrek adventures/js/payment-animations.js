document.addEventListener('DOMContentLoaded', function() {
    // Animation pour les messages de succès ou d'erreur
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');

    if (successMessage) {
        successMessage.style.opacity = '0';
        successMessage.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            successMessage.style.transition = 'all 0.5s ease';
            successMessage.style.opacity = '1';
            successMessage.style.transform = 'translateY(0)';
        }, 100);
    }

    if (errorMessage) {
        errorMessage.style.opacity = '0';
        errorMessage.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            errorMessage.style.transition = 'all 0.5s ease';
            errorMessage.style.opacity = '1';
            errorMessage.style.transform = 'translateY(0)';
        }, 100);
    }

    // Animation pour le formulaire de paiement
    const paymentForm = document.getElementById('payment-form');
    if (paymentForm) {
        const inputs = paymentForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    }

    // Animation pour la redirection vers CY Bank
    const cybankForm = document.getElementById('cybank_form');
    if (cybankForm) {
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
        overlay.style.display = 'flex';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.5s ease';
        overlay.innerHTML = '<div style="color: white; font-size: 1.5rem;">Connexion à CY Bank...</div>';
        document.body.appendChild(overlay);

        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 100);
    }
});
