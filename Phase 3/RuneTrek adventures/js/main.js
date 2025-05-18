document.addEventListener('DOMContentLoaded', function() {
    // Fonction utiliser 
    
    // Fonction qui détecte la page actuel
    function getCurrentPage() {
        const path = window.location.pathname;
        return path.substring(path.lastIndexOf('/') + 1);
    }
    
    // Fonction pour ajouter une notif
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        // Ajout du style pour les notif
        const style = document.createElement('style');
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 4px;
                color: white;
                font-weight: bold;
                z-index: 1000;
                animation: slideIn 0.3s ease-out, fadeOut 0.5s ease-in 2.5s forwards;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }
            .notification.info {
                background-color: #1E88E5;
            }
            .notification.success {
                background-color: #4CAF50;
            }
            .notification.error {
                background-color: #F44336;
            }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; visibility: hidden; }
            }
        `;
        document.head.appendChild(style);
        
        document.body.appendChild(notification);
        
        // Supprime après 3 secondes
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Ajoute des styles spécifiques pour le panier, y compris en mode sombre
    const cartStyle = document.createElement('style');
    cartStyle.textContent = `
        /* Styles pour l'indicateur du panier */
        #cart-indicator {
            position: relative;
        }
        
        #cart-link {
            display: flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-weight: bold;
            background-color: rgba(255, 215, 0, 0.2);
        }
        
        #cart-link:hover {
            background-color: rgba(255, 215, 0, 0.4);
        }
        
        #cart-count {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #FFD700;
            color: #333;
            font-size: 0.8rem;
            margin-left: 8px;
            padding: 0 4px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        /* Animation pour l'ajout au panier */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 0.5s ease;
        }
        
        /* Styles pour la modal du panier */
        .cart-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .cart-modal-content {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            width: 600px;
            max-width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
        }
        
        .close-modal:hover {
            color: #333;
        }
        
        .cart-modal h2 {
            font-family: 'Beaufort for LOL', sans-serif;
            color: #1E88E5;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .cart-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .cart-items li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item-info {
            flex-grow: 1;
        }
        
        .cart-item-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .cart-item-price {
            color: #1E88E5;
            font-weight: bold;
        }
        
        .remove-item {
            background-color: #F44336;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .remove-item:hover {
            background-color: #D32F2F;
        }
        
        .cart-total {
            margin: 20px 0;
            text-align: right;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
            font-size: 1.2rem;
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        #clear-cart {
            background-color: #9E9E9E;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        #clear-cart:hover {
            background-color: #757575;
        }
        
        #checkout-cart {
            background-color: #1E88E5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        #checkout-cart:hover {
            background-color: #1565C0;
        }
        
        .cart-empty {
            text-align: center;
            padding: 30px 0;
            color: #757575;
        }
        
        .cart-empty-icon {
            font-size: 3rem;
            color: #9E9E9E;
            margin-bottom: 15px;
        }
        
        /* Mode sombre pour le panier */
        .dark-mode #cart-link {
            background-color: rgba(255, 215, 0, 0.3);
            color: #FFD700;
        }
        
        .dark-mode #cart-link:hover {
            background-color: rgba(255, 215, 0, 0.5);
        }
        
        .dark-mode #cart-count {
            background-color: #FFD700;
            color: #121212;
        }
        
        .dark-mode .cart-modal-content {
            background-color: #1e1e1e;
            color: #e0e0e0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
        }
        
        .dark-mode .cart-modal h2 {
            color: #64B5F6;
            border-bottom-color: #333;
        }
        
        .dark-mode .close-modal {
            color: #aaa;
        }
        
        .dark-mode .close-modal:hover {
            color: #fff;
        }
        
        .dark-mode .cart-items li {
            border-bottom-color: #333;
        }
        
        .dark-mode .cart-item-title {
            color: #e0e0e0;
        }
        
        .dark-mode .cart-item-price {
            color: #64B5F6;
        }
        
        .dark-mode .cart-total {
            background-color: #2a2a2a;
            color: #e0e0e0;
        }
        
        .dark-mode #clear-cart {
            background-color: #424242;
        }
        
        .dark-mode #clear-cart:hover {
            background-color: #616161;
        }
        
        .dark-mode #checkout-cart {
            background-color: #1976D2;
        }
        
        .dark-mode #checkout-cart:hover {
            background-color: #1565C0;
        }
        
        .dark-mode .cart-empty {
            color: #aaa;
        }
        
        .dark-mode .cart-empty-icon {
            color: #666;
        }
        
        /* Styles pour le bouton d'ajout au panier */
        .add-to-cart, 
        .add-to-cart-button {
            background-color: #FFD700;
            color: #2F3136;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .add-to-cart:hover, 
        .add-to-cart-button:hover {
            background-color: #FFC107;
            transform: translateY(-2px);
        }
        
        .dark-mode .add-to-cart, 
        .dark-mode .add-to-cart-button {
            background-color: #FFD700;
            color: #121212;
        }
        
        .dark-mode .add-to-cart:hover, 
        .dark-mode .add-to-cart-button:hover {
            background-color: #FFC107;
        }
    `;
    document.head.appendChild(cartStyle);
    
    // Initialisation du panier côté client (pour compléter avec le serveur)
    class Cart {
        constructor() {
            this.items = this.loadCart();
            this.updateCartIndicator();
        }
        
        // Charge le panier depuis le stockage local
        loadCart() {
            const savedCart = localStorage.getItem('runetrek_cart');
            return savedCart ? JSON.parse(savedCart) : [];
        }
        
        // Sauvegarde le panier dans le stockage local
        saveCart() {
            localStorage.setItem('runetrek_cart', JSON.stringify(this.items));
            this.updateCartIndicator();
        }
        
        // Ajoute un voyage au panier
        addTrip(tripId, tripName, tripPrice) {
            // Vérifie si le voyage est déjà dans le panier
            const existingItem = this.items.find(item => item.id === tripId);
            
            if (existingItem) {
                showNotification('Ce voyage est déjà dans votre panier', 'info');
                return;
            }
            
            // Ajoute le voyage
            this.items.push({
                id: tripId,
                name: tripName,
                price: tripPrice,
                timestamp: Date.now()
            });
            
            // Sauvegarde le panier
            this.saveCart();
            
            // Anime l'indicateur du panier
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.classList.add('pulse');
                setTimeout(() => {
                    countElement.classList.remove('pulse');
                }, 500);
            }
            
            showNotification('Voyage ajouté au panier', 'success');
        }
        
        // Supprime un voyage du panier
        removeTrip(tripId) {
            const index = this.items.findIndex(item => item.id === tripId);
            
            if (index !== -1) {
                this.items.splice(index, 1);
                this.saveCart();
                showNotification('Voyage retiré du panier', 'info');
                return true;
            }
            
            return false;
        }
        
        // Vide le panier
        clearCart() {
            this.items = [];
            this.saveCart();
            showNotification('Panier vidé', 'info');
        }
        
        // Maj l'indicateur de panier dans l'interface
        updateCartIndicator() {
            // Vérifie si l'indicateur existe
            let cartIndicator = document.getElementById('cart-indicator');
            
            // Créer l'indicateur s'il n'existe pas
            if (!cartIndicator) {
                // Vérifie si l'utilisateur est connecté
                const logoutLink = document.querySelector('a[href="logout.php"]');
                if (!logoutLink) return; // Ne pas afficher le panier pour les utilisateurs non connectés
                
                // Créer l'élément
                cartIndicator = document.createElement('li');
                cartIndicator.innerHTML = `
                    <a href="#" id="cart-link">
                        🛒 Panier <span id="cart-count">0</span>
                    </a>
                `;
                cartIndicator.id = 'cart-indicator';
                
                // Insére avant le lien de déconnexion
                const navList = logoutLink.parentNode.parentNode;
                navList.insertBefore(cartIndicator, logoutLink.parentNode);
                
                // Gestionnaire d'événements pour afficher le panier
                document.getElementById('cart-link').addEventListener('click', (e) => {
                    e.preventDefault();
                    this.showCartModal();
                });
            }
            
            // Maj nombre d'articles
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.textContent = this.items.length;
                
                // Ajoute une animation si le nombre a changé
                countElement.classList.add('pulse');
                setTimeout(() => {
                    countElement.classList.remove('pulse');
                }, 500);
            }
        }
        
        // Affiche une modal avec le contenu du panier
        showCartModal() {
            // Créer la modal
            const modal = document.createElement('div');
            modal.className = 'cart-modal';
            
            // Créer le contenu
            let modalContent = `
                <div class="cart-modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>Votre panier</h2>
            `;
            
            // Ajout les voyages
            if (this.items.length === 0) {
                modalContent += `
                    <div class="cart-empty">
                        <div class="cart-empty-icon">🛒</div>
                        <p>Votre panier est vide.</p>
                        <p>Explorez nos voyages pour vivre des aventures inoubliables !</p>
                    </div>
                `;
            } else {
                modalContent += `<ul class="cart-items">`;
                
                this.items.forEach(item => {
                    modalContent += `
                        <li>
                            <div class="cart-item-info">
                                <div class="cart-item-title">${item.name}</div>
                                <div class="cart-item-price">${item.price} PO</div>
                            </div>
                            <button class="remove-item" data-id="${item.id}">Supprimer</button>
                        </li>
                    `;
                });
                
                modalContent += `</ul>`;
                
                // Calculer le tt
                const total = this.items.reduce((sum, item) => sum + parseInt(item.price), 0);
                
                modalContent += `
                    <div class="cart-total">
                        <strong>Total:</strong> ${total} PO
                    </div>
                    <div class="cart-actions">
                        <button id="clear-cart">Vider le panier</button>
                        <button id="checkout-cart">Passer au paiement</button>
                    </div>
                `;
            }
            
            modalContent += `</div>`;
            modal.innerHTML = modalContent;
            
            document.body.appendChild(modal);
            
            // Gestionnaires d'événements
            modal.querySelector('.close-modal').addEventListener('click', () => {
                modal.remove();
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
            
            // Gére la suppression d'articles
            const removeButtons = modal.querySelectorAll('.remove-item');
            removeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tripId = button.getAttribute('data-id');
                    if (this.removeTrip(tripId)) {
                        button.closest('li').remove();
                        
                        // Maj tt;
                        const total = this.items.reduce((sum, item) => sum + parseInt(item.price), 0);
                        modal.querySelector('.cart-total').innerHTML = `<strong>Total:</strong> ${total} PO`;
                        
                        // Affich un msg si le panier est vide
                        if (this.items.length === 0) {
                            const cartItems = modal.querySelector('.cart-items');
                            const cartTotal = modal.querySelector('.cart-total');
                            const cartActions = modal.querySelector('.cart-actions');
                            
                            // Remplace par le message de panier vide
                            cartItems.innerHTML = `
                                <div class="cart-empty">
                                    <div class="cart-empty-icon">🛒</div>
                                    <p>Votre panier est vide.</p>
                                    <p>Explorez nos voyages pour vivre des aventures inoubliables !</p>
                                </div>
                            `;
                            
                            // Masque les éléments usless
                            cartTotal.style.display = 'none';
                            cartActions.style.display = 'none';
                        }
                    }
                });
            });
            
            // Gére le vidage du panier
            const clearButton = modal.querySelector('#clear-cart');
            if (clearButton) {
                clearButton.addEventListener('click', () => {
                    this.clearCart();
                    modal.remove();
                });
            }
            
            // Gére le paiement
            const checkoutButton = modal.querySelector('#checkout-cart');
            if (checkoutButton) {
                checkoutButton.addEventListener('click', () => {
                    // Redirige vers la page de récapitulatif
                    window.location.href = 'trip_summary.php';
                });
            }
        }
    }
    
    // Initialise le panier
    const cart = new Cart();
    
    // Ajoute des boutons "Ajouter au panier" sur la page de recherche
    if (getCurrentPage() === 'search.php') {
        const tripCards = document.querySelectorAll('.trip-card');
        
        tripCards.forEach(card => {
            const tripFooter = card.querySelector('.trip-footer');
            const detailsLink = card.querySelector('.view-details');
            
            if (tripFooter && detailsLink) {
                const tripId = detailsLink.href.match(/id=(\d+)/)[1];
                const tripName = card.querySelector('h3').textContent;
                const tripPrice = parseInt(card.querySelector('.price').textContent.match(/\d+/)[0], 10);
                
                const addToCartButton = document.createElement('button');
                addToCartButton.className = 'add-to-cart';
                addToCartButton.innerHTML = '🛒 Ajouter';
                addToCartButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    cart.addTrip(tripId, tripName, tripPrice);
                    
                    // Ajouter une animation au bouton
                    addToCartButton.classList.add('pulse');
                    setTimeout(() => {
                        addToCartButton.classList.remove('pulse');
                    }, 500);
                });
                
                tripFooter.appendChild(addToCartButton);
            }
        });
    }
    
    // Ajout un bouton "Ajouter au panier" sur la page détail du voyage
    if (getCurrentPage().startsWith('trip_details.php')) {
        const tripContent = document.querySelector('.trip-content');
        const bookNowButton = document.querySelector('.book-now');
        
        if (tripContent && bookNowButton) {
            // Recupere les informations du voyage
            const tripTitle = document.querySelector('.trip-details h1').textContent;
            const tripId = window.location.search.match(/id=(\d+)/)[1];
            const priceMatch = document.querySelector('.trip-info').textContent.match(/Prix de base:\s*(\d+)/);
            const tripPrice = priceMatch ? parseInt(priceMatch[1], 10) : 0;
            
            const addToCartButton = document.createElement('a');
            addToCartButton.className = 'add-to-cart-button';
            addToCartButton.href = '#';
            addToCartButton.innerHTML = '🛒 Ajouter au panier';
            addToCartButton.addEventListener('click', (e) => {
                e.preventDefault();
                cart.addTrip(tripId, tripTitle, tripPrice);
                
                // Ajoute une animation au bouton
                addToCartButton.classList.add('pulse');
                setTimeout(() => {
                    addToCartButton.classList.remove('pulse');
                }, 500);
            });
            
            // Insére avant le bouton "Voir le récapitulatif"
            bookNowButton.parentNode.insertBefore(addToCartButton, bookNowButton);
        }
    }
});
