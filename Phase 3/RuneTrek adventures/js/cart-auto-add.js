document.addEventListener('DOMContentLoaded', function() {
    // check page de détails d'un voyage
    const tripDetailsPage = document.querySelector('.trip-details-page');
    if (!tripDetailsPage) return;

    // prend l'ID du voyage
    const tripId = window.location.search.match(/id=(\d+)/)?.[1];
    if (!tripId) return;
    
    // Vérifie si le mode lecture seule est activé (profil utilisateur)
    const isReadOnly = window.location.search.includes('readonly=1');
    if (isReadOnly) return;
    
    // Récupére tous les sélecteurs d'options
    const optionSelectors = tripDetailsPage.querySelectorAll('select');
    if (optionSelectors.length === 0) return;
    
    // Variable pour suivre si une modification a été faite
    let optionsModified = false;
    
    // Ajoute un champ caché au formulaire pour indiquer si le panier doit être mis à jour
    const form = tripDetailsPage.querySelector('form');
    if (form) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'add_to_cart';
        hiddenInput.value = '0';
        hiddenInput.id = 'add_to_cart_field';
        form.appendChild(hiddenInput);
        
        // Ajoute un élément pour afficher le statut du panier
        const cartStatus = document.createElement('div');
        cartStatus.className = 'cart-status';
        cartStatus.innerHTML = '<p>Modifiez les options pour ajouter ce voyage au panier</p>';
        cartStatus.style.padding = '10px';
        cartStatus.style.margin = '15px 0';
        cartStatus.style.backgroundColor = '#f8f9fa';
        cartStatus.style.borderRadius = '4px';
        cartStatus.style.textAlign = 'center';
        cartStatus.style.color = '#666';
        form.prepend(cartStatus);
    }
    
    // Suit les valeurs originales des sélecteurs
    const originalValues = {};
    optionSelectors.forEach((selector, index) => {
        originalValues[selector.name] = selector.value;
        
        // Ajoute un gestionnaire d'événement pour détecter les changements
        selector.addEventListener('change', function() {
            if (originalValues[selector.name] !== selector.value) {
                // Marque que des modifications ont été faites
                optionsModified = true;
                
                // Maj le champ caché
                const hiddenField = document.getElementById('add_to_cart_field');
                if (hiddenField) {
                    hiddenField.value = '1';
                }
                
                // Maj le statut du panier
                updateCartStatus(true);
                
                // Marque visuellement que cette option a été modifiée
                const formGroup = selector.closest('.form-group');
                if (formGroup) {
                    formGroup.classList.add('option-modified');
                }
            }
        });
    });
    
    // Ajoute des styles pour les options modifiées
    const style = document.createElement('style');
    style.textContent = `
        .option-modified {
            background-color: rgba(255, 215, 0, 0.1);
            border-radius: 4px;
            padding: 5px;
            transition: background-color 0.3s;
        }
        
        .cart-status {
            transition: all 0.3s;
        }
        
        .cart-status.active {
            background-color: rgba(76, 175, 80, 0.1) !important;
            color: #4CAF50 !important;
            border-left: 4px solid #4CAF50;
        }
        
        /* Dark mode */
        .dark-mode .option-modified {
            background-color: rgba(255, 215, 0, 0.05);
        }
        
        .dark-mode .cart-status {
            background-color: #2a2a2a !important;
            color: #aaa !important;
        }
        
        .dark-mode .cart-status.active {
            background-color: rgba(76, 175, 80, 0.05) !important;
            color: #81C784 !important;
        }
    `;
    document.head.appendChild(style);
    
    // Fonction pour maj le statut du panier
    function updateCartStatus(active) {
        const cartStatus = document.querySelector('.cart-status');
        if (!cartStatus) return;
        
        if (active) {
            cartStatus.classList.add('active');
            cartStatus.innerHTML = '<p><strong>✓ Ce voyage sera ajouté à votre panier</strong></p>';
        } else {
            cartStatus.classList.remove('active');
            cartStatus.innerHTML = '<p>Modifiez les options pour ajouter ce voyage au panier</p>';
        }
    }
});
