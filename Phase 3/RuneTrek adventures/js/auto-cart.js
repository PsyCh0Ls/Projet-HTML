document.addEventListener('DOMContentLoaded', function() {
    // check page de détails d'un voyage
    const tripDetailsPage = document.querySelector('.trip-details-page');
    if (!tripDetailsPage) return;

    // Récupére les info du voyage
    const tripTitle = document.querySelector('.trip-details h1')?.textContent || '';
    const tripId = window.location.search.match(/id=(\d+)/)?.[1];
    const priceMatch = document.querySelector('.trip-info')?.textContent.match(/Prix de base:\s*(\d+)/);
    const tripPrice = priceMatch ? parseInt(priceMatch[1], 10) : 0;

    // Vérifie si le mode lecture seule est activé (profil utilisateur)
    const isReadOnly = window.location.search.includes('readonly=1');
    if (isReadOnly) return;

    // Récupérer tt les sélecteurs d'options
    const optionSelectors = tripDetailsPage.querySelectorAll('select');
    
    // Fonction pour ajouter au panier
    function addToCart() {
        // Vérif si le panier existe dans localStorage
        let cart = JSON.parse(localStorage.getItem('runetrek_cart') || '[]');
        
        // Vérif si le voyage est déjà dans le panier
        const existingItemIndex = cart.findIndex(item => item.id === tripId);
        if (existingItemIndex !== -1) {
            console.log('Ce voyage est déjà dans votre panier');
            return;
        }
        
        // Ajout voyage au panier
        cart.push({
            id: tripId,
            name: tripTitle,
            price: tripPrice,
            timestamp: Date.now()
        });
        
        // Save panier
        localStorage.setItem('runetrek_cart', JSON.stringify(cart));
        
        // Affiche notif
        showNotification('Voyage ajouté automatiquement au panier', 'info');
        
        // Maj indicateur de panier
        updateCartIndicator();
    }
    
    // Fonction pour afficher notif
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '15px 20px';
        notification.style.backgroundColor = type === 'info' ? '#1E88E5' : '#4CAF50';
        notification.style.color = 'white';
        notification.style.borderRadius = '4px';
        notification.style.zIndex = '1000';
        notification.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s';
            
            // Supprime ap transition
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 3000);
    }
    
    // Fonction maj l'indicateur de panier
    function updateCartIndicator() {
        const cart = JSON.parse(localStorage.getItem('runetrek_cart') || '[]');
        const cartCount = cart.length;
        
        // Trouver ou créer l'élément d'indicateur de panier
        let cartIndicator = document.getElementById('cart-count');
        if (cartIndicator) {
            cartIndicator.textContent = cartCount;
            cartIndicator.classList.add('pulse');
            setTimeout(() => {
                cartIndicator.classList.remove('pulse');
            }, 500);
        }
    }
    
    // Ajoute des écouteurs d'événements pour les sélecteurs d'options
    optionSelectors.forEach(selector => {
        let originalValue = selector.value;
        
        selector.addEventListener('change', function() {
            // Si c'est la première modification, ajouter au panier
            if (originalValue === selector.value) return;
            
            // Ajoute le voyage au panier automatiquement
            addToCart();
            
            // Maj la valeur originale pour ne pas déclencher à nouveau
            originalValue = null;
        });
    });
});
