document.addEventListener('DOMContentLoaded', function() {
    // Fonctions utilitaires globales
    
    // Fonction pour détecter la page courante
    function getCurrentPage() {
        const path = window.location.pathname;
        return path.substring(path.lastIndexOf('/') + 1);
    }
    
    // Fonction pour ajouter une notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        // Ajouter du style pour les notifications
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
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Initialisation du panier côté client (pour compléter avec le serveur)
    class Cart {
        constructor() {
            this.items = this.loadCart();
            this.updateCartIndicator();
        }
        
        // Charger le panier depuis le stockage local
        loadCart() {
            const savedCart = localStorage.getItem('runetrek_cart');
            return savedCart ? JSON.parse(savedCart) : [];
        }
        
        // Sauvegarder le panier dans le stockage local
        saveCart() {
            localStorage.setItem('runetrek_cart', JSON.stringify(this.items));
            this.updateCartIndicator();
        }
        
        // Ajouter un voyage au panier
        addTrip(tripId, tripName, tripPrice) {
            // Vérifier si le voyage est déjà dans le panier
            const existingItem = this.items.find(item => item.id === tripId);
            
            if (existingItem) {
                showNotification('Ce voyage est déjà dans votre panier', 'info');
                return;
            }
            
            // Ajouter le voyage
            this.items.push({
                id: tripId,
                name: tripName,
                price: tripPrice,
                timestamp: Date.now()
            });
            
            // Sauvegarder le panier
            this.saveCart();
            showNotification('Voyage ajouté au panier', 'success');
        }
        
        // Supprimer un voyage du panier
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
        
        // Vider le panier
        clearCart() {
            this.items = [];
            this.saveCart();
            showNotification('Panier vidé', 'info');
        }
        
        // Mettre à jour l'indicateur de panier dans l'interface
        updateCartIndicator() {
            // Vérifier si l'indicateur existe
            let cartIndicator = document.getElementById('cart-indicator');
            
            // Créer l'indicateur s'il n'existe pas
            if (!cartIndicator) {
                // Vérifier si l'utilisateur est connecté
                const logoutLink = document.querySelector('a[href="logout.php"]');
                if (!logoutLink) return; // Ne pas afficher le panier pour les utilisateurs non connectés
                
                // Créer l'élément
                cartIndicator = document.createElement('li');
                cartIndicator.innerHTML = `
                    <a href="#" id="cart-link">
                        Panier <span id="cart-count">0</span>
                    </a>
                `;
                cartIndicator.id = 'cart-indicator';
                
                // Insérer avant le lien de déconnexion
                const navList = logoutLink.parentNode.parentNode;
                navList.insertBefore(cartIndicator, logoutLink.parentNode);
                
                // Gestionnaire d'événements pour afficher le panier
                document.getElementById('cart-link').addEventListener('click', (e) => {
                    e.preventDefault();
                    this.showCartModal();
                });
            }
            
            // Mettre à jour le nombre d'articles
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.textContent = this.items.length;
            }
        }
        
        // Afficher une modal avec le contenu du panier
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
            
            // Ajouter les voyages
            if (this.items.length === 0) {
                modalContent += `<p>Votre panier est vide.</p>`;
            } else {
                modalContent += `<ul class="cart-items">`;
                
                this.items.forEach(item => {
                    modalContent += `
                        <li>
                            <span>${item.name}</span>
                            <span>${item.price} PO</span>
                            <button class="remove-item" data-id="${item.id}">Supprimer</button>
                        </li>
                    `;
                });
                
                modalContent += `</ul>`;
                
                // Calculer le total
                const total = this.items.reduce((sum, item) => sum + item.price, 0);
                
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
            
            // Ajouter du style pour la modal
            const style = document.createElement('style');
            style.textContent = `
                .cart-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                }
                .cart-modal-content {
                    background-color: white;
                    padding: 20px;
                    border-radius: 8px;
                    width: 500px;
                    max-width: 90%;
                    max-height: 80vh;
                    overflow-y: auto;
                    position: relative;
                }
                .close-modal {
                    position: absolute;
                    top: 10px;
                    right: 15px;
                    font-size: 24px;
                    cursor: pointer;
                }
                .cart-items {
                    list-style: none;
                    padding: 0;
                }
                .cart-items li {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px 0;
                    border-bottom: 1px solid #eee;
                }
                .remove-item {
                    background-color: #F44336;
                    color: white;
                    border: none;
                    padding: 5px 10px;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .cart-total {
                    margin: 20px 0;
                    text-align: right;
                    font-size: 1.2rem;
                }
                .cart-actions {
                    display: flex;
                    justify-content: space-between;
                }
                .cart-actions button {
                    padding: 10px 15px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                #clear-cart {
                    background-color: #9E9E9E;
                    color: white;
                }
                #checkout-cart {
                    background-color: #1E88E5;
                    color: white;
                }
            `;
            document.head.appendChild(style);
            
            // Ajouter la modal au corps de la page
            document.body.appendChild(modal);
            
            // Gestionnaires d'événements
            modal.querySelector('.close-modal').addEventListener('click', () => {
                modal.remove();
            });
            
            // Fermer en cliquant à l'extérieur
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
            
            // Gérer la suppression d'articles
            const removeButtons = modal.querySelectorAll('.remove-item');
            removeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tripId = button.getAttribute('data-id');
                    if (this.removeTrip(tripId)) {
                        button.closest('li').remove();
                        
                        // Mettre à jour le total
                        const total = this.items.reduce((sum, item) => sum + item.price, 0);
                        modal.querySelector('.cart-total').innerHTML = `<strong>Total:</strong> ${total} PO`;
                        
                        // Afficher un message si le panier est vide
                        if (this.items.length === 0) {
                            modal.querySelector('.cart-items').innerHTML = `<p>Votre panier est vide.</p>`;
                            modal.querySelector('.cart-total').style.display = 'none';
                            modal.querySelector('.cart-actions').style.display = 'none';
                        }
                    }
                });
            });
            
            // Gérer le vidage du panier
            const clearButton = modal.querySelector('#clear-cart');
            if (clearButton) {
                clearButton.addEventListener('click', () => {
                    this.clearCart();
                    modal.remove();
                });
            }
            
            // Gérer le paiement
            const checkoutButton = modal.querySelector('#checkout-cart');
            if (checkoutButton) {
                checkoutButton.addEventListener('click', () => {
                    // Rediriger vers la page de récapitulatif
                    window.location.href = 'trip_summary.php';
                });
            }
        }
    }
    
    // Initialiser le panier
    const cart = new Cart();
    
    // Ajouter des boutons "Ajouter au panier" sur la page de recherche
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
                addToCartButton.textContent = 'Ajouter au panier';
                addToCartButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    cart.addTrip(tripId, tripName, tripPrice);
                });
                
                // Ajouter du style pour le bouton
                const style = document.createElement('style');
                style.textContent = `
                    .add-to-cart {
                        background-color: #FFD700;
                        color: #2F3136;
                        border: none;
                        padding: 5px 10px;
                        border-radius: 4px;
                        cursor: pointer;
                        margin-left: 10px;
                    }
                    .add-to-cart:hover {
                        background-color: #FFC107;
                    }
                `;
                document.head.appendChild(style);
                
                tripFooter.appendChild(addToCartButton);
            }
        });
    }
    
    // Ajouter un bouton "Ajouter au panier" sur la page détail du voyage
    if (getCurrentPage().startsWith('trip_details.php')) {
        const tripContent = document.querySelector('.trip-content');
        const bookNowButton = document.querySelector('.book-now');
        
        if (tripContent && bookNowButton) {
            // Récupérer les informations du voyage
            const tripTitle = document.querySelector('.trip-details h1').textContent;
            const tripId = window.location.search.match(/id=(\d+)/)[1];
            const priceMatch = document.querySelector('.trip-info').textContent.match(/Prix de base:\s*(\d+)/);
            const tripPrice = priceMatch ? parseInt(priceMatch[1], 10) : 0;
            
            // Créer le bouton
            const addToCartButton = document.createElement('a');
            addToCartButton.className = 'add-to-cart-button';
            addToCartButton.href = '#';
            addToCartButton.textContent = 'Ajouter au panier';
            addToCartButton.addEventListener('click', (e) => {
                e.preventDefault();
                cart.addTrip(tripId, tripTitle, tripPrice);
            });
            
            // Ajouter du style pour le bouton
            const style = document.createElement('style');
            style.textContent = `
                .add-to-cart-button {
                    display: inline-block;
                    background-color: #FFD700;
                    color: #2F3136;
                    padding: 0.8rem 2rem;
                    border-radius: 4px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-right: 10px;
                    margin-top: 1rem;
                }
                .add-to-cart-button:hover {
                    background-color: #FFC107;
                    text-decoration: none;
                }
            `;
            document.head.appendChild(style);
            
            // Insérer avant le bouton "Voir le récapitulatif"
            bookNowButton.parentNode.insertBefore(addToCartButton, bookNowButton);
        }
    }
});