document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#payment-form');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        const cardNumber = form.querySelector('input[name="card_number"]').value;
        const expiryDate = form.querySelector('input[name="expiry_date"]').value;
        const cvv = form.querySelector('input[name="cvv"]').value;

        let errors = [];
        if (!/^\d{16}$/.test(cardNumber)) {
            errors.push('Le numéro de carte doit contenir 16 chiffres.');
        }
        if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
            errors.push('La date d\'expiration doit être au format MM/AA.');
        }
        if (!/^\d{3}$/.test(cvv)) {
            errors.push('Le CVV doit contenir 3 chiffres.');
        }

        if (errors.length > 0) {
            event.preventDefault();
            alert('Erreur(s) :\n' + errors.join('\n'));
        }
    });
});