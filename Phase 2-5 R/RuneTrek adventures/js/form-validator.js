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
                
                // Supprimer la classe d'erreur
                input.classList.remove('input-error');
                
                // Vérifier si le champ est requis et vide
                if (input.hasAttribute('required') && !input.value.trim()) {
                    isValid = false;
                    showError(input, 'Ce champ est requis');
                    return;
                }
                
                // Validation des emails
                if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        isValid = false;
                        showError(input, 'Format d\'email invalide');
                        return;
                    }
                }
                
                // Validation des dates
                if (input.type === 'date' && input.value.trim()) {
                    const date = new Date(input.value);
                    const today = new Date();
                    
                    if (isNaN(date.getTime())) {
                        isValid = false;
                        showError(input, 'Date invalide');
                        return;
                    }
                    
                    // Si c'est une date de naissance, vérifier qu'elle n'est pas dans le futur
                    if (input.id === 'birth_date' && date > today) {
                        isValid = false;
                        showError(input, 'La date de naissance ne peut pas être dans le futur');
                        return;
                    }
                }
                
                // Validation des mots de passe
                if (input.type === 'password' && input.value.trim()) {
                    if (input.value.length < 7) {
                        isValid = false;
                        showError(input, 'Le mot de passe doit contenir au moins 7 caractères');
                        return;
                    }
                }
                
                // Vérification de confirmation de mot de passe
                if (input.id === 'confirm_password' && input.value.trim()) {
                    const password = form.querySelector('#password');
                    if (password && password.value !== input.value) {
                        isValid = false;
                        showError(input, 'Les mots de passe ne correspondent pas');
                        return;
                    }
                }
                
                // Validation de la longueur minimale pour les identifiants
                if (input.id === 'login' && input.value.trim()) {
                    if (input.value.length < 7) {
                        isValid = false;
                        showError(input, 'L\'identifiant doit contenir au moins 7 caractères');
                        return;
                    }
                }
                
                // Validation de la longueur minimale pour les autres champs
                if ((input.id === 'name' || input.id === 'nickname') && input.value.trim()) {
                    if (input.value.length < 3) {
                        isValid = false;
                        showError(input, 'Ce champ doit contenir au moins 3 caractères');
                        return;
                    }
                }
                
                // Validation de l'adresse (doit contenir au moins un chiffre et du texte)
                if (input.id === 'address' && input.value.trim()) {
                    const hasNumber = /\d/.test(input.value);
                    const hasText = /[a-z]/i.test(input.value);
                    
                    if (!hasNumber || !hasText) {
                        isValid = false;
                        showError(input, 'L\'adresse doit contenir au moins un numéro et un nom de rue');
                        return;
                    }
                }
            });
            
            // Si tout est valide, soumettre le formulaire
            if (isValid) {
                console.log("Formulaire valide, envoi en cours...");
                form.submit();
            } else {
                console.log("Formulaire invalide, veuillez corriger les erreurs.");
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
            field.style.paddingRight = '40px';
            field.parentNode.appendChild(toggleButton);
            
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
        
        // Validation en temps réel pendant que l'utilisateur tape
        const formInputs = form.querySelectorAll('input, select, textarea');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                // Supprimer l'erreur quand l'utilisateur corrige
                const error = input.parentNode.querySelector('.error-message');
                if (error) {
                    error.remove();
                    input.classList.remove('input-error');
                }
                
                // Validation en temps réel pour certains champs
                if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        showError(input, 'Format d\'email invalide', false);
                    }
                }
                
                if (input.type === 'password' && input.value.trim() && input.value.length < 7) {
                    showError(input, 'Le mot de passe doit contenir au moins 7 caractères', false);
                }
                
                if (input.id === 'login' && input.value.trim() && input.value.length < 7) {
                    showError(input, 'L\'identifiant doit contenir au moins 7 caractères', false);
                }
                
                if (input.id === 'confirm_password' && input.value.trim()) {
                    const password = form.querySelector('#password');
                    if (password && password.value !== input.value) {
                        showError(input, 'Les mots de passe ne correspondent pas', false);
                    }
                }
            });
        });
    });
    
    // Fonction pour afficher une erreur sous un champ
    function showError(input, message, shouldAnimate = true) {
        // Supprimer tout message d'erreur existant
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Créer et ajouter le nouveau message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
        
        // Mettre en surbrillance le champ
        input.classList.add('input-error');
        
        // Si l'animation est désactivée (validation en temps réel)
        if (!shouldAnimate) {
            errorDiv.style.animation = 'none';
        }
    }
});