document.addEventListener('DOMContentLoaded', function() {
    // gérer tt les fonctionnalités du panier
    class Cart {
        constructor() {
            this.items = this.loadCart();
            this.updateCartIndicator();
            this.setupEventListeners();
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
                this.showNotification('Ce voyage est déjà dans votre panier', 'info');
                return;
            }
            
            // Ajoute le voyage
            this.items.push({
                id: tripId,
                name: tripName,
                price: tripPrice,
                timestamp: Date.now()
            });
            
            // Sauvegarde le panier @oumar verif ca 
            this.saveCart();
            
            // Anime l'indicateur du panier
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.classList.add('pulse');
                setTimeout(() => {
                    countElement.classList.remove('pulse');
                }, 500);
            }
            
            this.showNotification('Voyage ajouté au panier', 'success');
        }
        
        // Suppr un voyage du panier
        removeTrip(tripId) {
            const index = this.items.findIndex(item => item.id === tripId);
            
            if (index !== -1) {
                this.items.splice(index, 1);
                this.saveCart();
                this.showNotification('Voyage retiré du panier', 'info');
                return true;
            }
            
            return false;
        }
        
        // Vider le panier
        clearCart() {
            this.items = [];
            this.saveCart();
            this.showNotification('Panier vidé', 'info');
        }
        
        // Maj l'indicateur de panier dans l'interface
        updateCartIndicator() {
            // Vérifie si l'indicateur existe
            let cartIndicator = document.getElementById('cart-indicator');
            
            // Créer l'indicateur s'il n'existe pas
            if (!cartIndicator) {
                // Vérifier si l'utilisateur est connecté
                const logoutLink = document.querySelector('a[href="logout.php"]');
                if (!logoutLink) return; // Ne pas afficher le panier pour les utilisateurs non connectés
                
                // Créer l'élément
                cartIndicator = document.createElement('li');
                cartIndicator.innerHTML = `
                    <a href="cart.php" id="cart-link">
                        🛒 Panier <span id="cart-count">0</span>
                    </a>
                `;
                cartIndicator.id = 'cart-indicator';
                
                // Insére avant le lien de déconnexion
                const navList = logoutLink.parentNode.parentNode;
                navList.insertBefore(cartIndicator, logoutLink.parentNode);
                
                // Gestionnaire d'événements pour afficher le panier
                document.getElementById('cart-link').addEventListener('click', (e) => {
                    if (e.ctrlKey || e.metaKey) return; // Permet l'ouverture dans un nouvel onglet  
                    e.preventDefault();
                    this.showCartModal();
                });
            }
            
            // Maj le nombre d'articles
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.textContent = this.items.length;
                
                // Add animation si le nombre a changé
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
            
            // Ajout voyages
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
                
                // Calcule tt
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
            
            // Gère les event ( verifier cette patie svp )
            modal.querySelector('.close-modal').addEventListener('click', () => {
                modal.remove();
            });
            
            // Pour fermer en cliquant a l'ext
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
                        
                        // Maj le total
                        const total = this.items.reduce((sum, item) => sum + parseInt(item.price), 0);
                        modal.querySelector('.cart-total').innerHTML = `<strong>Total:</strong> ${total} PO`;
                        
                        // Affiche un msgh si le panier est vide
                        if (this.items.length === 0) {
                            const cartItems = modal.querySelector('.cart-items');
                            const cartTotal = modal.querySelector('.cart-total');
                            const cartActions = modal.querySelector('.cart-actions');
                            
                            // Remplace par le msg panier vide
                            cartItems.innerHTML = `
                                <div class="cart-empty">
                                    <div class="cart-empty-icon">🛒</div>
                                    <p>Votre panier est vide.</p>
                                    <p>Explorez nos voyages pour vivre des aventures inoubliables !</p>
                                </div>
                            `;
                            
                            // Masque les éléments non nécessaires
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
                    // Redirig vers la page de récapitulatif
                    window.location.href = 'cart.php';
                });
            }
        }
        
        // Configure les gestionnaires d'événements pour les boutons d'ajout au panier
        setupEventListeners() {
            // Vérifie on est  sur la page de détails d'un voyage
            const tripDetailsPage = document.querySelector('.trip-details-page');
            
            if (tripDetailsPage) {
                // Récupére les info du voyage
                const tripTitle = document.querySelector('.trip-details h1')?.textContent || '';
                const tripId = window.location.search.match(/id=(\d+)/)?.[1];
                const priceMatch = document.querySelector('.trip-info')?.textContent.match(/Prix de base:\s*(\d+)/);
                const tripPrice = priceMatch ? parseInt(priceMatch[1], 10) : 0;
                
                // Ajoute un bouton d'ajout au panier
                if (tripId && !window.location.search.includes('readonly=1')) {
                    const bookNowButton = document.querySelector('.book-now');
                    
                    if (bookNowButton && !document.querySelector('.add-to-cart-button')) {
                        // Créer le bouton
                        const addToCartButton = document.createElement('a');
                        addToCartButton.className = 'add-to-cart-button';
                        addToCartButton.href = '#';
                        addToCartButton.innerHTML = '🛒 Ajouter au panier';
                        addToCartButton.dataset.tripId = tripId;
                        
                        // Ajoute le gestionnaire d'événements
                        addToCartButton.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.addTrip(tripId, tripTitle, tripPrice);
                            
                            // Ajoute une animation au bouton
                            addToCartButton.classList.add('pulse');
                            setTimeout(() => {
                                addToCartButton.classList.remove('pulse');
                            }, 500);
                        });
                        
                        // Insére avant le bouton "Voir le récapitulatif"
                        bookNowButton.parentNode.insertBefore(addToCartButton, bookNowButton);
                    }
                    
                    // Ajout au panier lorsqu'une option est modifiée
                    const optionSelectors = tripDetailsPage.querySelectorAll('select');
                    let isFirstChange = true;
                    
                    optionSelectors.forEach(selector => {
                        selector.addEventListener('change', () => {
                            if (isFirstChange) {
                                isFirstChange = false;
                                this.addTrip(tripId, tripTitle, tripPrice);
                                
                                // Affiche un message pour indiquer que le voyage a été automatiquement ajouté au panier
                                const cartStatus = document.createElement('div');
                                cartStatus.className = 'cart-status';
                                cartStatus.innerHTML = '<p>✓ Ce voyage a été ajouté à votre panier</p>';
                                cartStatus.style.padding = '10px';
                                cartStatus.style.margin = '15px 0';
                                cartStatus.style.backgroundColor = 'rgba(76, 175, 80, 0.1)';
                                cartStatus.style.borderRadius = '4px';
                                cartStatus.style.textAlign = 'center';
                                cartStatus.style.color = '#4CAF50';
                                cartStatus.style.borderLeft = '4px solid #4CAF50';
                                
                                const form = tripDetailsPage.querySelector('form');
                                if (form) {
                                    form.prepend(cartStatus);
                                }
                            }
                        });
                    });
                }
            }
            
            // boutons "Ajouter au panier" sur la page de recherche
            const searchPage = document.querySelector('.search-page');
            if (searchPage) {
                const tripCards = document.querySelectorAll('.trip-card');
                
                tripCards.forEach(card => {
                    const tripFooter = card.querySelector('.trip-footer');
                    const detailsLink = card.querySelector('.view-details');
                    
                    if (tripFooter && detailsLink && !tripFooter.querySelector('.add-to-cart')) {
                        const tripId = detailsLink.href.match(/id=(\d+)/)[1];
                        const tripName = card.querySelector('h3').textContent;
                        const tripPrice = parseInt(card.querySelector('.price').textContent.match(/\d+/)[0], 10);
                        
                        const addToCartButton = document.createElement('button');
                        addToCartButton.className = 'add-to-cart';
                        addToCartButton.innerHTML = '🛒 Ajouter';
                        addToCartButton.dataset.tripId = tripId;
                        
                        addToCartButton.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.addTrip(tripId, tripName, tripPrice);
                            
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
        }
        
        // Affiche une notification
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            if (!document.querySelector('style#cart-notification-style')) {
                const style = document.createElement('style');
                style.id = 'cart-notification-style';
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
            }
            
            document.body.appendChild(notification);
            
            // Suppr après 3 secondes
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
    
    // Initialise le panier
    window.cart = new Cart();
});
