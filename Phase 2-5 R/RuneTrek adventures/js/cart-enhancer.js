document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page panier
    const cartPage = document.querySelector('.cart-page');
    if (!cartPage) return;
    
    // Fonction pour mettre à jour le prix total
    function updateTotalPrice() {
        const itemPrices = document.querySelectorAll('.cart-item .item-price');
        let total = 0;
        
        itemPrices.forEach(priceElement => {
            const priceText = priceElement.textContent;
            const price = parseFloat(priceText.match(/\d+(\.\d+)?/)[0]);
            total += price;
        });
        
        const totalElement = document.querySelector('.cart-total');
        if (totalElement) {
            totalElement.innerHTML = `<strong>Total:</strong> ${total.toFixed(2)} PO`;
            
            // Ajouter une animation
            totalElement.classList.add('price-updated');
            setTimeout(() => {
                totalElement.classList.remove('price-updated');
            }, 500);
        }
        
        return total;
    }
    
    // Fonction pour supprimer un article du panier
    function setupRemoveItemHandlers() {
        const removeButtons = document.querySelectorAll('.remove-item');
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('form');
                const itemElement = this.closest('.cart-item');
                
                // Animer la suppression
                itemElement.style.transition = 'all 0.3s ease-out';
                itemElement.style.opacity = '0';
                itemElement.style.height = '0';
                itemElement.style.overflow = 'hidden';
                
                // Attendre la fin de l'animation avant de soumettre le formulaire
                setTimeout(() => {
                    form.submit();
                }, 300);
            });
        });
    }
    
    // Fonction pour mettre à jour la quantité d'un article
    function setupQuantityControls() {
        const quantityInputs = document.querySelectorAll('.item-quantity input');
        
        quantityInputs.forEach(input => {
            const minusButton = input.previousElementSibling;
            const plusButton = input.nextElementSibling;
            
            // Gestionnaire pour le bouton moins
            if (minusButton && minusButton.classList.contains('quantity-minus')) {
                minusButton.addEventListener('click', function() {
                    if (input.value > 1) {
                        input.value = parseInt(input.value) - 1;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
            
            // Gestionnaire pour le bouton plus
            if (plusButton && plusButton.classList.contains('quantity-plus')) {
                plusButton.addEventListener('click', function() {
                    if (input.value < 10) { // Limite maximale
                        input.value = parseInt(input.value) + 1;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
            
            // Mise à jour du prix lors du changement de quantité
            input.addEventListener('change', function() {
                const itemElement = this.closest('.cart-item');
                const priceElement = itemElement.querySelector('.item-price');
                const basePrice = parseFloat(priceElement.getAttribute('data-price'));
                const newPrice = basePrice * parseInt(this.value);
                
                priceElement.textContent = `${newPrice.toFixed(2)} PO`;
                priceElement.classList.add('price-updated');
                
                setTimeout(() => {
                    priceElement.classList.remove('price-updated');
                }, 500);
                
                // Mettre à jour le total
                updateTotalPrice();
                
                // Envoyer une requête AJAX pour mettre à jour le panier côté serveur
                // Dans la phase 3, on simule simplement cette opération
                const updateForm = document.createElement('form');
                updateForm.method = 'POST';
                updateForm.action = 'update_cart.php';
                updateForm.style.display = 'none';
                
                const tripIdInput = document.createElement('input');
                tripIdInput.type = 'hidden';
                tripIdInput.name = 'trip_id';
                tripIdInput.value = itemElement.getAttribute('data-id');
                
                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = 'quantity';
                quantityInput.value = this.value;
                
                updateForm.appendChild(tripIdInput);
                updateForm.appendChild(quantityInput);
                
                // Au lieu d'envoyer réellement le formulaire,
                // nous simulons la mise à jour pour la phase 3
                console.log('Simulaton de mise à jour du panier:', {
                    trip_id: tripIdInput.value,
                    quantity: quantityInput.value
                });
            });
        });
    }
    
    // Configuration des boutons d'action du panier
    function setupCartActions() {
        const clearButton = document.querySelector('.clear-cart');
        const checkoutButton = document.querySelector('.checkout');
        
        if (clearButton) {
            clearButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Demander confirmation
                if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
                    // Animer tous les éléments du panier
                    const cartItems = document.querySelectorAll('.cart-item');
                    
                    cartItems.forEach(item => {
                        item.style.transition = 'all 0.3s ease-out';
                        item.style.opacity = '0';
                        item.style.height = '0';
                        item.style.overflow = 'hidden';
                    });
                    
                    // Attendre la fin de l'animation avant de soumettre le formulaire
                    setTimeout(() => {
                        this.closest('form').submit();
                    }, 300);
                }
            });
        }
        
        if (checkoutButton) {
            checkoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Animer le bouton
                this.innerHTML = 'Préparation du paiement...';
                this.style.backgroundColor = '#90CAF9';
                
                // Montrer une animation de chargement
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'loading-overlay active';
                loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
                document.body.appendChild(loadingOverlay);
                
                // Rediriger vers la page de paiement après un court délai
                setTimeout(() => {
                    this.closest('form').submit();
                }, 800);
            });
        }
    }
    
    // Ajouter des styles CSS pour les animations
    const style = document.createElement('style');
    style.textContent = `
        .price-updated {
            animation: price-highlight 0.5s ease-out;
        }
        
        @keyframes price-highlight {
            0% { color: #1E88E5; transform: scale(1); }
            50% { color: #FFD700; transform: scale(1.1); }
            100% { color: #1E88E5; transform: scale(1); }
        }
        
        .cart-item {
            transition: all 0.3s ease-out;
        }
        
        .item-quantity {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-minus, .quantity-plus {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #f0f0f0;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.2s;
        }
        
        .quantity-minus:hover, .quantity-plus:hover {
            background-color: #e0e0e0;
        }
        
        .quantity-input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px;
        }
        
        /* Mode sombre */
        .dark-mode .quantity-minus, .dark-mode .quantity-plus {
            background-color: #333;
            color: #e0e0e0;
        }
        
        .dark-mode .quantity-minus:hover, .dark-mode .quantity-plus:hover {
            background-color: #444;
        }
        
        .dark-mode .quantity-input {
            background-color: #2a2a2a;
            color: #e0e0e0;
            border-color: #444;
        }
    `;
    document.head.appendChild(style);
    
    // Initialiser les gestionnaires d'événements
    setupRemoveItemHandlers();
    setupQuantityControls();
    setupCartActions();
    
    // Calculer le prix total initial
    updateTotalPrice();
});