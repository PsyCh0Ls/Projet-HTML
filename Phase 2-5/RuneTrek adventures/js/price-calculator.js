document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page détails d'un voyage
    const tripDetails = document.querySelector('.trip-details-page');
    if (!tripDetails) return;
    
    // Récupérer le prix de base
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
    
    // Ajouter du style pour le prix dynamique
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
    
    // Insérer le conteneur de prix
    const tripContent = tripDetails.querySelector('.trip-content');
    tripContent.appendChild(dynamicPriceContainer);
    
    // Récupérer tous les sélecteurs d'options
    const optionSelectors = tripDetails.querySelectorAll('select');
    
    // Calculer le prix total initial
    let totalPrice = basePrice;
    optionSelectors.forEach(selector => {
        const selectedOption = selector.options[selector.selectedIndex];
        const optionPrice = parseInt(selectedOption.getAttribute('data-price') || 0, 10);
        totalPrice += optionPrice;
    });
    
    // Mettre à jour l'affichage du prix total
    updateTotalPrice(totalPrice);
    
    // Écouter les changements d'options
    optionSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            // Recalculer le prix total à chaque changement
            recalculatePrice();
            
            // Mettre en évidence l'option sélectionnée
            const parentGroup = this.closest('.form-group');
            document.querySelectorAll('.option-selected').forEach(el => {
                el.classList.remove('option-selected');
            });
            parentGroup.classList.add('option-selected');
            
            // Mettre à jour le champ caché pour ajouter au panier
            const addToCartField = document.getElementById('add_to_cart_field');
            if (addToCartField) {
                addToCartField.value = '1';
            }
            
            // Mettre à jour le statut du panier
            const cartStatus = document.querySelector('.cart-status');
            if (cartStatus) {
                cartStatus.classList.add('active');
                cartStatus.innerHTML = '<p><strong>✓ Ce voyage sera ajouté à votre panier</strong></p>';
            }
        });
    });
    
    // Fonction pour recalculer le prix total
    function recalculatePrice() {
        let newTotalPrice = basePrice;
        
        // Parcourir tous les sélecteurs d'options
        optionSelectors.forEach(selector => {
            const selectedOption = selector.options[selector.selectedIndex];
            const optionPrice = parseInt(selectedOption.getAttribute('data-price') || 0, 10);
            
            // Ajouter le prix de cette option au total
            newTotalPrice += optionPrice;
        });
        
        // Mettre à jour l'affichage uniquement si le prix a changé
        if (newTotalPrice !== totalPrice) {
            totalPrice = newTotalPrice;
            updateTotalPrice(totalPrice);
        }
    }
    
    // Fonction pour mettre à jour l'affichage du prix
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
    
    // Ajouter un gestionnaire pour le bouton "Ajouter au panier"
    const addToCartButton = tripDetails.querySelector('.add-to-cart-button');
    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            // Définir que le voyage doit être ajouté au panier
            const addToCartField = document.getElementById('add_to_cart_field');
            if (addToCartField) {
                addToCartField.value = '1';
            }
            
            // Soumettre le formulaire
            const form = tripDetails.querySelector('form');
            if (form) {
                form.submit();
            }
        });
    }
});