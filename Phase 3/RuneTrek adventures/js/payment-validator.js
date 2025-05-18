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
                
                // Supprime la classe d'erreur
                input.classList.remove('input-error');
                
                // Vérifie si le champ est requis et vide
                if (input.hasAttribute('required') && !input.value.trim()) {
                    isValid = false;
                    showError(input, 'Ce champ est requis');
                    return;
                }
                
                // Validation emails
                if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        isValid = false;
                        showError(input, 'Format d\'email invalide');
                        return;
                    }
                }
                
                // Validation dates
                if (input.type === 'date' && input.value.trim()) {
                    const date = new Date(input.value);
                    const today = new Date();
                    
                    if (isNaN(date.getTime())) {
                        isValid = false;
                        showError(input, 'Date invalide');
                        return;
                    }
                    
                    // vérifie qu'elle n'est pas dans le futur
                    if (input.id === 'birth_date' && date > today) {
                        isValid = false;
                        showError(input, 'La date de naissance ne peut pas être dans le futur');
                        return;
                    }
                }
                
                // Validation des mots de passe avec complexité
                if (input.type === 'password' && input.value.trim()) {
                    if (input.value.length < 7) {
                        isValid = false;
                        showError(input, 'Le mot de passe doit contenir au moins 7 caractères');
                        return;
                    }
                    
                    // Vérifier la complexité si c'est un formulaire d'inscription
                    if (form.id === 'register-form' && input.id === 'password') {
                        const hasNumber = /\d/.test(input.value);
                        const hasUpperCase = /[A-Z]/.test(input.value);
                        const hasLowerCase = /[a-z]/.test(input.value);
                        
                        if (!(hasNumber && hasUpperCase && hasLowerCase)) {
                            isValid = false;
                            showError(input, 'Le mot de passe doit contenir au moins un chiffre, une majuscule et une minuscule');
                            return;
                        }
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
                
                // Validation de la longueur min pour les identifiants
                if (input.id === 'login' && input.value.trim()) {
                    if (input.value.length < 7) {
                        isValid = false;
                        showError(input, 'L\'identifiant doit contenir au moins 7 caractères');
                        return;
                    }
                }
                
                // Validation de la longueur min pour les autres champs
                if ((input.id === 'name' || input.id === 'nickname') && input.value.trim()) {
                    if (input.value.length < 3) {
                        isValid = false;
                        showError(input, 'Ce champ doit contenir au moins 3 caractères');
                        return;
                    }
                }
                
                // Validation adresse
                if (input.id === 'address' && input.value.trim()) {
                    const hasNumber = /\d/.test(input.value);
                    const hasText = /[a-z]/i.test(input.value);
                    
                    if (!hasNumber || !hasText) {
                        isValid = false;
                        showError(input, 'L\'adresse doit contenir au moins un numéro et un nom de rue');
                        return;
                    }
                }
                
                // Validation de carte bancaire pour la page de paiement
                if (input.id === 'card_number' && input.value.trim()) {
                    // Vérifier que ce sont bien 16 chiffres
                    if (!/^\d{16}$/.test(input.value.replace(/\s/g, ''))) {
                        isValid = false;
                        showError(input, 'Le numéro de carte doit contenir 16 chiffres');
                        return;
                    }
                }
                
                // Validation du CVV
                if (input.id === 'cvv' && input.value.trim()) {
                    if (!/^\d{3}$/.test(input.value)) {
                        isValid = false;
                        showError(input, 'Le CVV doit contenir 3 chiffres');
                        return;
                    }
                }
                
                // Validation de la date d'exp
                if (input.id === 'expiry_date' && input.value.trim()) {
                    const expiryRegex = /^(0[1-9]|1[0-2])\/[0-9]{2}$/;
                    if (!expiryRegex.test(input.value)) {
                        isValid = false;
                        showError(input, 'Le format doit être MM/AA');
                        return;
                    }
                    
                    // Vérifier que la date n'est pas exp
                    const parts = input.value.split('/');
                    const month = parseInt(parts[0], 10);
                    const year = 2000 + parseInt(parts[1], 10);
                    const today = new Date();
                    const expiryDate = new Date(year, month - 1, 1);
                    
                    if (expiryDate < today) {
                        isValid = false;
                        showError(input, 'La carte est expirée');
                        return;
                    }
                }
            });
            
            // soumettre le formulaire si valider
            if (isValid) {
                console.log("Formulaire valide, envoi en cours...");
                
                // Animation de chargement
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    const originalText = submitButton.textContent;
                    submitButton.innerHTML = '<span class="loading-spinner"></span> Traitement...';
                    submitButton.disabled = true;
                    
                    // Ajout du style pour le spinner
                    const spinnerStyle = document.createElement('style');
                    spinnerStyle.textContent = `
                        .loading-spinner {
                            display: inline-block;
                            width: 16px;
                            height: 16px;
                            border: 2px solid rgba(255,255,255,.3);
                            border-radius: 50%;
                            border-top-color: white;
                            animation: spin 1s linear infinite;
                            margin-right: 8px;
                        }
                        @keyframes spin {
                            to { transform: rotate(360deg); }
                        }
                    `;
                    document.head.appendChild(spinnerStyle);
                    
                    // Soumet avec animation
                    setTimeout(() => {
                        form.submit();
                    }, 800);
                } else {
                    form.submit();
                }
            } else {
                console.log("Formulaire invalide, veuillez corriger les erreurs.");
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
            
            // Maj le compteur lors de la saisie
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
            
            // clic sur le bouton
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
                // Supprime l'erreur quand l'utilisateur corrige
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
                
                // Format de carte bancaire automatique avec espaces tous les 4 chiffres
                if (input.id === 'card_number') {
                    const val = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    const formatted = val.match(/.{1,4}/g)?.join(' ') || val;
                    input.value = formatted;
                }
                
                // Format de date d'expiration automatique (MM/AA)
                if (input.id === 'expiry_date') {
                    const val = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    if (val.length > 2) {
                        input.value = val.slice(0, 2) + '/' + val.slice(2, 4);
                    }
                }
            });
        });
    });
    
    // Fonction pour afficher une erreur sous un champ
    function showError(input, message, shouldAnimate = true) {
        // Supprime tout message d'erreur existant
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Créer et ajoutr le nouveau message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
        
        // Mettre en surbrillance le champ
        input.classList.add('input-error');    
        if (!shouldAnimate) {
            errorDiv.style.animation = 'none';
        }
    }
})
