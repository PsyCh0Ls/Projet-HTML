document.addEventListener('DOMContentLoaded', function() {
    // Vérifie  sur la page détails d'un voyage
    const tripDetails = document.querySelector('.trip-details-page');
    if (!tripDetails) return;
    
    // Récupére le prix de base
    const priceElement = tripDetails.querySelector('.trip-info');
    if (!priceElement) return;
    
    const priceMatch = priceElement.textContent.match(/Prix de base:\s*(\d+)/);
    if (!priceMatch) return;
    
    const basePrice = parseInt(priceMatch[1], 10);
    
    // Créer un élément pour afficher le prix total dynamique
    const dynamicPriceContainer = document.createElement('div');
    dynamicPriceContainer.className = 'dynamic-price';
    dynamicPriceContainer.innerHTML = `
        <h3>Prix total estimé</h3>
        <p class="total-price">${basePrice} PO</p>
    `;
    
    // Ajout du style pour le prix dynamique
    const style = document.createElement('style');
    style.textContent = `
        .dynamic-price {
            position: sticky;
            top: 20px;
            background-color: #1E88E5;
            color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            margin-top: 20px;
            text-align: center;
        }
        .dynamic-price h3 {
            margin: 0 0 10px 0;
            font-family: 'Beaufort for LOL', sans-serif;
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        .price-change {
            animation: pricePulse 0.5s;
        }
        @keyframes pricePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .option-selected {
            background-color: rgba(30, 136, 229, 0.1);
            border-radius: 4px;
        }
    `;
    document.head.appendChild(style);
    
    // Insére conteneur de prix
    const tripContent = tripDetails.querySelector('.trip-content');
    tripContent.appendChild(dynamicPriceContainer);
    
    // Récupére tous les sélecteurs d'options
    const optionSelectors = tripDetails.querySelectorAll('.option-select');
    
    // Récupére le sélecteur de nombre de personnes global
    const globalPersonsSelect = document.getElementById('global_persons');
    let globalPersons = globalPersonsSelect ? parseInt(globalPersonsSelect.value, 10) : 1;
    
    // Calcule le prix total initial
    let totalPrice = basePrice;
    optionSelectors.forEach(selector => {
        const selectedOption = selector.options[selector.selectedIndex];
        const optionPrice = parseInt(selectedOption.getAttribute('data-price') || 0, 10);
        totalPrice += optionPrice * globalPersons;
    });
    
    // Maj l'affichage du prix total
    updateTotalPrice(totalPrice);
    
    optionSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            // Recalcule le prix total à chaque changement
            recalculatePrice();
            
            // Met en évidence l'option sélectionnée
            const parentGroup = this.closest('.form-group');
            document.querySelectorAll('.option-selected').forEach(el => {
                el.classList.remove('option-selected');
            });
            parentGroup.classList.add('option-selected');
            
            // Maj le champ caché pour ajouter au panier
            const addToCartField = document.getElementById('add_to_cart_field');
            if (addToCartField) {
                addToCartField.value = '1';
            }
            
            // Maj le statut du panier
            const cartStatus = document.querySelector('.cart-status');
            if (cartStatus) {
                ccartStatus.classList.add('active');
                cartStatus.innerHTML = '<p><strong>✓ Ce voyage sera ajouté à votre panier</strong></p>';
            }
        });
    });
    
    if (globalPersonsSelect) {
        globalPersonsSelect.addEventListener('change', function() {
            globalPersons = parseInt(this.value, 10);
            
            // Maj tous les champs cachés de personnes
            const personsInputs = document.querySelectorAll('.persons-input');
            personsInputs.forEach(input => {
                input.value = globalPersons;
            });
            
            // Recalculer le prix
            recalculatePrice();
            
            // Indiquer que le voyage sera ajouté au panier
            const addToCartField = document.getElementById('add_to_cart_field');
            if (addToCartField) {
                addToCartField.value = '1';
            }
            
            // Maj le statut du panier
            const cartStatus = document.querySelector('.cart-status');
            if (cartStatus) {
                cartStatus.classList.add('active');
                cartStatus.innerHTML = '<p><strong>✓ Ce voyage sera ajouté à votre panier</strong></p>';
            }
        });
    }
    
    // Fonction pour recalculer le prix tot
    function recalculatePrice() {
        let newTotalPrice = basePrice;
        
        // Parcour tous les sélecteurs d'options
        optionSelectors.forEach(selector => {
            const selectedOption = selector.options[selector.selectedIndex];
            const optionPrice = parseInt(selectedOption.getAttribute('data-price') || 0, 10);
            
            // Ajoute le prix de cette option au total, multiplié par le nombre de personnes
            newTotalPrice += optionPrice * globalPersons;
        });
        
        // Maj l'affichage uniquement si le prix a changé
        if (newTotalPrice !== totalPrice) {
            totalPrice = newTotalPrice;
            updateTotalPrice(totalPrice);
        }
    }
    
    // Fonction pour maj l'affichage du prix
    function updateTotalPrice(price) {
        const priceElement = dynamicPriceContainer.querySelector('.total-price');
        priceElement.textContent = `${price} PO`;
        priceElement.classList.add('price-change');
        
        // Retirer l'animation après qu'elle soit terminée
        setTimeout(() => {
            priceElement.classList.remove('price-change');
        }, 500);
        
        // Mettre à jour également le champ caché pour le prix calculé
        const calculatedPriceField = document.getElementById('calculated-price');
        if (calculatedPriceField) {
            calculatedPriceField.value = price;
        }
    }
    
    // Amène la fonction recalculatePrice globalement afin qu'elle puisse être appelée depuis d'autres scripts
    window.recalculatePrice = recalculatePrice;
    
    // Ajoute un gestionnaire pour le bouton "Ajouter au panier"
    const addToCartButton = tripDetails.querySelector('.add-to-cart-button');
    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            // Définir que le voyage doit être ajouté au panier
            const addToCartField = document.getElementById('add_to_cart_field');
            if (addToCartField) {
                addToCartField.value = '1';
            }
            
            // Animatiopn le bouton
            this.classList.add('button-pulse');
            setTimeout(() => {
                this.classList.remove('button-pulse');
            }, 300);
            
            // Soumet le formulaire après un court délai pour voir l'animation
            const form = tripDetails.querySelector('form');
            if (form) {
                setTimeout(() => {
                    form.submit();
                }, 300);
            }
        });
    }
    
    // Style d'animation pour le bouton
    const animationStyle = document.createElement('style');
    animationStyle.textContent = `
        @keyframes button-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .button-pulse {
            animation: button-pulse 0.3s;
        }
    `;
    document.head.appendChild(animationStyle);
});
