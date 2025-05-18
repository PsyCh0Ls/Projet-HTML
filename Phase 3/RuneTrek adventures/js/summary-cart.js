/**
 * Script pour gérer l'ajout automatique au panier sur la page de récapitulatif
 */
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page de récapitulatif
    const summaryPage = document.querySelector('.summary-page');
    if (!summaryPage) return;
    
    // Vérifier si la notification d'ajout au panier est présente
    const notification = document.getElementById('cart-notification');
    if (notification) {
        // Afficher la notification avec une animation
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        
        setTimeout(function() {
            notification.style.transition = 'all 0.3s ease';
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
            
            // Cacher la notification après 5 secondes
            setTimeout(function() {
                notification.style.opacity = '0';
                
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 300);
            }, 5000);
        }, 300);
    }
    
    // Mettre à jour visuellement le compteur du panier
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        // Ajouter une animation au compteur
        cartCount.classList.add('pulse');
        
        setTimeout(function() {
            cartCount.classList.remove('pulse');
        }, 500);
    }
    
    // Ajouter une animation d'entrée pour les éléments du récapitulatif
    const summaryItems = document.querySelectorAll('.summary-container > *');
    summaryItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, 100 + index * 50);
    });
    
    // Ajouter une animation au bouton d'ajout au panier
    const addToCartButton = document.querySelector('.add-cart-button');
    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            this.innerHTML = '✓ Ajout en cours...';
            this.style.backgroundColor = '#4CAF50';
            this.style.color = 'white';
        });
    }
});