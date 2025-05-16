document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page de détails d'un voyage
    const tripDetailsPage = document.querySelector('.trip-details-page');
    if (!tripDetailsPage) return;

    // Récupérer les informations du voyage
    const tripTitle = document.querySelector('.trip-details h1')?.textContent || '';
    const tripId = window.location.search.match(/id=(\d+)/)?.[1];
    const priceMatch = document.querySelector('.trip-info')?.textContent.match(/Prix de base:\s*(\d+)/);
    const tripPrice = priceMatch ? parseInt(priceMatch[1], 10) : 0;

    // Vérifier si le mode lecture seule est activé (profil utilisateur)
    const isReadOnly = window.location.search.includes('readonly=1');
    if (isReadOnly) return;

    // Récupérer tous les sélecteurs d'options
    const optionSelectors = tripDetailsPage.querySelectorAll('select');
    
    // Fonction pour ajouter au panier
    function addToCart() {
        // Vérifier si le panier existe dans localStorage
        let cart = JSON.parse(localStorage.getItem('runetrek_cart') || '[]');
        
        // Vérifier si le voyage est déjà dans le panier
        const existingItemIndex = cart.findIndex(item => item.id === tripId);
        if (existingItemIndex !== -1) {
            console.log('Ce voyage est déjà dans votre panier');
            return;
        }
        
        // Ajouter le voyage au panier
        cart.push({
            id: tripId,
            name: tripTitle,
            price: tripPrice,
            timestamp: Date.now()
        });
        
        // Sauvegarder le panier
        localStorage.setItem('runetrek_cart', JSON.stringify(cart));
        
        // Afficher une notification
        showNotification('Voyage ajouté automatiquement au panier', 'info');
        
        // Mettre à jour l'indicateur de panier
        updateCartIndicator();
    }
    
    // Fonction pour afficher une notification
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
        
        // Disparaître après 3 secondes
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s';
            
            // Supprimer après la transition
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 3000);
    }
    
    // Fonction pour mettre à jour l'indicateur de panier
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
    
    // Ajouter des écouteurs d'événements pour les sélecteurs d'options
    optionSelectors.forEach(selector => {
        let originalValue = selector.value;
        
        selector.addEventListener('change', function() {
            // Si c'est la première modification, ajouter au panier
            if (originalValue === selector.value) return;
            
            // Ajouter le voyage au panier automatiquement
            addToCart();
            
            // Mettre à jour la valeur originale pour ne pas déclencher à nouveau
            originalValue = null;
        });
    });
});