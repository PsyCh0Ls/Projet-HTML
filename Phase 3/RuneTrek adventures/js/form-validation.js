document.addEventListener('DOMContentLoaded', function() {
    // Récupére tous les formulaires de la page
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Ajoute validation lors de la soumission
        form.addEventListener('submit', function(e) {
            // Empêche l'envoi par défaut
            e.preventDefault();
            
            // Vérifie tous les champs requis
            let isValid = true;
            const formInputs = form.querySelectorAll('input, select, textarea');
            
            formInputs.forEach(input => {
                // Supprime les messages d'erreur existants
                const existingError = input.parentNode.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                // Vérifie si le champ est requis et vide
                if (input.hasAttribute('required') && !input.value.trim()) {
                    isValid = false;
                    showError(input, 'Ce champ est requis');
                }
                
                // Check des emails
                if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        isValid = false;
                        showError(input, 'Email invalide');
                    }
                }
                
                // Check des dates
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
                
                // Check de confirmation de mot de passe
                if (input.name === 'password_confirm' && input.value.trim()) {
                    const password = form.querySelector('input[name="password"]');
                    if (password && password.value !== input.value) {
                        isValid = false;
                        showError(input, 'Les mots de passe ne correspondent pas');
                    }
                }
            });
            
            // soumet le formulaire si tt est bon
            if (isValid) {
                form.submit();
            }
        });
        
        // Ajoute des compteurs pour les champs limités
        const limitedInputs = form.querySelectorAll('input[maxlength], textarea[maxlength]');
        limitedInputs.forEach(input => {
            // Créer un compteur
            const counter = document.createElement('div');
            counter.className = 'character-counter';
            counter.innerHTML = `${input.value.length}/${input.maxLength}`;
            input.parentNode.appendChild(counter);
            
            // Maj compteur lors de la saisie
            input.addEventListener('input', function() {
                counter.innerHTML = `${input.value.length}/${input.maxLength}`;
                
                // Change la couleur si proche de la limite
                if (input.value.length > input.maxLength * 0.8) {
                    counter.style.color = 'orange';
                } else if (input.value.length === input.maxLength) {
                    counter.style.color = 'red';
                } else {
                    counter.style.color = '';
                }
            });
        });
        
        // Montre/cahcher MDP
        const passwordFields = form.querySelectorAll('input[type="password"]');
        passwordFields.forEach(field => {
            // bouton d'affichage
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'password-toggle';
            toggleButton.innerHTML = '👁️';
            toggleButton.title = 'Afficher/masquer le mot de passe';
            
            field.parentNode.style.position = 'relative';
            field.parentNode.appendChild(toggleButton);
            
            // Ajout du style pour le bouton
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
            
            // Gére le clic sur le bouton
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
    
    // afficher une erreur sous un champ au cas ouuu
    function showError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
        
        // Met  le champ en valeur
        input.style.borderColor = 'red';
        
        // Remet le style normal après correction
        input.addEventListener('input', function() {
            input.style.borderColor = '';
            const error = input.parentNode.querySelector('.error-message');
            if (error) {
                error.remove();
            }
        });
    }
});
