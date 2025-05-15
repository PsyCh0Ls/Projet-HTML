document.addEventListener('DOMContentLoaded', function() {
    // Récupérer tous les formulaires de la page
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Ajouter validation lors de la soumission
        form.addEventListener('submit', function(e) {
            // Empêcher l'envoi par défaut
            e.preventDefault();
            
            // Vérifier tous les champs requis
            let isValid = true;
            const formInputs = form.querySelectorAll('input, select, textarea');
            
            formInputs.forEach(input => {
                // Supprimer les messages d'erreur existants
                const existingError = input.parentNode.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                // Vérifier si le champ est requis et vide
                if (input.hasAttribute('required') && !input.value.trim()) {
                    isValid = false;
                    showError(input, 'Ce champ est requis');
                }
                
                // Validation des emails
                if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        isValid = false;
                        showError(input, 'Email invalide');
                    }
                }
                
                // Validation des dates
                if (input.type === 'date' && input.value.trim()) {
                    const date = new Date(input.value);
                    if (isNaN(date.getTime())) {
                        isValid = false;
                        showError(input, 'Date invalide');
                    }
                }
                
                // Validation des mots de passe
                if (input.type === 'password' && input.value.trim()) {
                    if (input.value.length < 6) {
                        isValid = false;
                        showError(input, 'Le mot de passe doit contenir au moins 6 caractères');
                    }
                }
                
                // Vérification de confirmation de mot de passe
                if (input.name === 'password_confirm' && input.value.trim()) {
                    const password = form.querySelector('input[name="password"]');
                    if (password && password.value !== input.value) {
                        isValid = false;
                        showError(input, 'Les mots de passe ne correspondent pas');
                    }
                }
            });
            
            // Si tout est valide, soumettre le formulaire
            if (isValid) {
                form.submit();
            }
        });
        
        // Ajouter des compteurs pour les champs limités
        const limitedInputs = form.querySelectorAll('input[maxlength], textarea[maxlength]');
        limitedInputs.forEach(input => {
            // Créer un compteur
            const counter = document.createElement('div');
            counter.className = 'character-counter';
            counter.innerHTML = `${input.value.length}/${input.maxLength}`;
            input.parentNode.appendChild(counter);
            
            // Mettre à jour le compteur lors de la saisie
            input.addEventListener('input', function() {
                counter.innerHTML = `${input.value.length}/${input.maxLength}`;
                
                // Changer la couleur si proche de la limite
                if (input.value.length > input.maxLength * 0.8) {
                    counter.style.color = 'orange';
                } else if (input.value.length === input.maxLength) {
                    counter.style.color = 'red';
                } else {
                    counter.style.color = '';
                }
            });
        });
        
        // Montrer/cacher le mot de passe
        const passwordFields = form.querySelectorAll('input[type="password"]');
        passwordFields.forEach(field => {
            // Créer le bouton d'affichage
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'password-toggle';
            toggleButton.innerHTML = '👁️';
            toggleButton.title = 'Afficher/masquer le mot de passe';
            
            // Insérer le bouton après le champ
            field.parentNode.style.position = 'relative';
            field.parentNode.appendChild(toggleButton);
            
            // Ajouter du style pour le bouton
            const style = document.createElement('style');
            style.textContent = `
                .password-toggle {
                    position: absolute;
                    right: 10px;
                    top: 50%;
                    transform: translateY(-50%);
                    background: none;
                    border: none;
                    cursor: pointer;
                    font-size: 1rem;
                }
                .character-counter {
                    font-size: 0.8rem;
                    text-align: right;
                    margin-top: 5px;
                }
                .error-message {
                    color: red;
                    font-size: 0.85rem;
                    margin-top: 5px;
                }
            `;
            document.head.appendChild(style);
            
            // Gérer le clic sur le bouton
            toggleButton.addEventListener('click', function() {
                if (field.type === 'password') {
                    field.type = 'text';
                    toggleButton.innerHTML = '🔒';
                } else {
                    field.type = 'password';
                    toggleButton.innerHTML = '👁️';
                }
            });
        });
    });
    
    // Fonction pour afficher une erreur sous un champ
    function showError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
        
        // Mettre en surbrillance le champ
        input.style.borderColor = 'red';
        
        // Remettre le style normal après correction
        input.addEventListener('input', function() {
            input.style.borderColor = '';
            const error = input.parentNode.querySelector('.error-message');
            if (error) {
                error.remove();
            }
        });
    }
});