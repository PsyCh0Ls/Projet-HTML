document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#admin-form');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        const title = form.querySelector('input[name="title"]').value;
        const duration = form.querySelector('input[name="duration"]').value;
        const basePrice = form.querySelector('input[name="base_price"]').value;

        let errors = [];
        if (title.trim().length < 3) {
            errors.push('Le titre doit contenir au moins 3 caractères.');
        }
        if (duration <= 0 || !Number.isInteger(Number(duration))) {
            errors.push('La durée doit être un nombre entier positif.');
        }
        if (basePrice <= 0 || isNaN(basePrice)) {
            errors.push('Le prix de base doit être un nombre positif.');
        }

        if (errors.length > 0) {
            event.preventDefault();
            alert('Erreur(s) :\n' + errors.join('\n'));
        }
    });
});