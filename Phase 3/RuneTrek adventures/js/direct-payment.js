/**
 * Script pour gérer l'animation du paiement direct et des actions sur le panier
 */
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page panier
    const cartPage = document.querySelector('.cart-page');
    if (!cartPage) return;
    
    // Configurer les animations pour les boutons de paiement direct
    setupDirectPaymentButtons();
    
    // Configurer les animations pour les suppressions d'articles
    setupRemoveItemButtons();
    
    // Configurer l'animation pour le vidage du panier
    setupClearCartButton();
    
    /**
     * Configure les animations pour les boutons de paiement direct
     */
    function setupDirectPaymentButtons() {
        const directCheckoutButtons = document.querySelectorAll('.direct-checkout');
        
        directCheckoutButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Ne pas empêcher la soumission par défaut ici, mais ajouter une animation
                this.innerHTML = '<span class="loading-dots">Paiement</span>';
                this.disabled = true;
                this.style.backgroundColor = '#FFB700';
                
                // Trouver l'élément parent (cart-item)
                const cartItem = this.closest('.cart-item');
                if (cartItem) {
                    cartItem.style.transition = 'all 0.3s ease';
                    cartItem.style.boxShadow = '0 0 0 2px #FFD700';
                    
                    // Ajouter une pulsation douce
                    cartItem.animate([
                        { boxShadow: '0 0 0 2px #FFD700' },
                        { boxShadow: '0 0 0 4px #FFD700' },
                        { boxShadow: '0 0 0 2px #FFD700' }
                    ], {
                        duration: 1000,
                        iterations: 2
                    });
                }
            });
        });
    }
    
    /**
     * Configure les animations pour les boutons de suppression d'articles
     */
    function setupRemoveItemButtons() {
        const removeButtons = document.querySelectorAll('.remove-item');
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Trouver l'élément parent (cart-item)
                const cartItem = this.closest('.cart-item');
                const form = this.closest('form');
                
                if (cartItem) {
                    // Animer la disparition de l'élément
                    cartItem.style.transition = 'all 0.3s ease';
                    cartItem.style.opacity = '0';
                    cartItem.style.height = '0';
                    cartItem.style.overflow = 'hidden';
                    cartItem.style.margin = '0';
                    cartItem.style.padding = '0';
                    
                    // Soumettre le formulaire après l'animation
                    setTimeout(() => {
                        form.submit();
                    }, 300);
                } else {
                    // Si on ne trouve pas l'élément parent, soumettre directement
                    form.submit();
                }
            });
        });
    }
    
    /**
     * Configure l'animation pour le bouton de vidage du panier
     */
    function setupClearCartButton() {
        const clearButton = document.querySelector('.clear-cart');
        
        if (clearButton) {
            clearButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Demander confirmation
                if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
                    // Animer la disparition de tous les articles
                    const cartItems = document.querySelectorAll('.cart-item');
                    
                    cartItems.forEach((item, index) => {
                        // Ajouter un délai progressif pour un effet cascade
                        setTimeout(() => {
                            item.style.transition = 'all 0.3s ease';
                            item.style.opacity = '0';
                            item.style.height = '0';
                            item.style.overflow = 'hidden';
                            item.style.margin = '0';
                            item.style.padding = '0';
                        }, index * 50);
                    });
                    
                    // Mettre à jour le total
                    const totalElement = document.querySelector('.cart-total');
                    if (totalElement) {
                        setTimeout(() => {
                            totalElement.innerHTML = '<strong>Total:</strong> 0 PO';
                            totalElement.style.transition = 'all 0.3s ease';
                            totalElement.style.color = '#1E88E5';
                            
                            // Animation du total
                            totalElement.animate([
                                { transform: 'scale(1)' },
                                { transform: 'scale(1.1)' },
                                { transform: 'scale(1)' }
                            ], {
                                duration: 500,
                                iterations: 1
                            });
                        }, cartItems.length * 50 + 100);
                    }
                    
                    // Soumettre le formulaire après l'animation
                    setTimeout(() => {
                        this.closest('form').submit();
                    }, cartItems.length * 50 + 300);
                }
            });
        }
    }
    
    // Ajouter des styles pour les animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes loading {
            0% { content: ""; }
            25% { content: "."; }
            50% { content: ".."; }
            75% { content: "..."; }
            100% { content: ""; }
        }
        
        .loading-dots::after {
            content: "";
            animation: loading 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 0.5s;
        }
    `;
    document.head.appendChild(style);
    
    // Animer les éléments du panier lors du chargement initial
    animateCartItems();
    
    /**
     * Anime les éléments du panier lors du chargement
     */
    function animateCartItems() {
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach((item, index) => {
            // Cacher initialement
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            
            // Afficher avec un délai progressif
            setTimeout(() => {
                item.style.transition = 'all 0.5s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });
    }
});