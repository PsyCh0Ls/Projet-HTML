document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.auth-page form');
    if (!form) return;

    const loginInput = form.querySelector('input[name="login"]');
    const passwordInput = form.querySelector('input[name="password"]');

    form.addEventListener('submit', function (event) {
        let errors = [];

        if (loginInput.value.trim().length < 3) {
            errors.push('Le login doit contenir au moins 3 caractères.');
        }
        if (passwordInput.value.length < 6) {
            errors.push('Le mot de passe doit contenir au moins 6 caractères.');
        }

        if (errors.length > 0) {
            event.preventDefault();
            alert('Erreur(s) :\n' + errors.join('\n'));
        }
    });
});