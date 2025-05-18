document.addEventListener('DOMContentLoaded', function() {
    // Check si l'utilisateur est connecté (rechercher le lien de déconnexion)
    const logoutLink = document.querySelector('a[href="logout.php"]');
    if (!logoutLink) return; // Ne pas afficher l'indicateur de panier pour les utilisateurs non connectés
    
    // Créer et ajoute un indicateur de panier dans le menu de navigation
    createCartIndicator();
    
    // Pour rendre le panier cliquable
    setupCartButtons();
    
    /**
     * Crée et ajoute l'indicateur de panier dans la navigation
     */
    function createCartIndicator() {
        // Check si l'indicateur existe déjà
        let cartIndicator = document.getElementById('cart-indicator');
        if (cartIndicator) return;
        
        // Créer l'élément
        cartIndicator = document.createElement('li');
        cartIndicator.id = 'cart-indicator';
        cartIndicator.innerHTML = `
            <a href="cart.php" id="cart-link">
                🛒 Panier <span id="cart-count">0</span>
            </a>
        `;
        
        // Ajouter les styles
        const style = document.createElement('style');
        style.textContent = `
            #cart-indicator {
                position: relative;
            }
            
            #cart-link {
                display: flex;
                align-items: center;
                padding: 6px 12px;
                margin-right: 10px;
                border-radius: 20px;
                background-color: rgba(255, 215, 0, 0.2);
                transition: all 0.3s ease;
                font-weight: bold;
            }
            
            #cart-link:hover {
                background-color: rgba(255, 215, 0, 0.4);
                transform: translateY(-2px);
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
            
            /* Notification */
            .cart-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 20px;
                background-color: #4CAF50;
                color: white;
                border-radius: 4px;
                font-weight: bold;
                z-index: 1000;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                transform: translateX(100%);
                opacity: 0;
                animation: slideIn 0.3s forwards, fadeOut 0.5s 2.5s forwards;
            }
            
            @keyframes slideIn {
                to { transform: translateX(0); opacity: 1; }
            }
            
            @keyframes fadeOut {
                to { opacity: 0; }
            }
            
            /* Mode sombre */
            .dark-mode #cart-link {
                background-color: rgba(255, 215, 0, 0.15);
                color: #FFD700;
            }
            
            .dark-mode #cart-link:hover {
                background-color: rgba(255, 215, 0, 0.25);
            }
            
            .dark-mode #cart-count {
                background-color: #FFD700;
                color: #121212;
            }
        `;
        document.head.appendChild(style);
        
        // Insére l'indicateur avant le lien de déconnexion
        const navList = logoutLink.parentNode.parentNode;
        navList.insertBefore(cartIndicator, logoutLink.parentNode);
        
        // charge le nombre d'éléments dans le panier depuis le serveur
        updateCartCount();
    }
    
    /**
     * Configure les boutons d'ajout au panier sur la page
     */
    function setupCartButtons() {
        // Trouve tous les boutons d'ajout au panier
        const addButtons = document.querySelectorAll('.add-to-cart, .add-to-cart-button');
        
        addButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Récupére l'ID du voyage
                const tripId = button.dataset.tripId || getUrlParam('id');
                if (!tripId) return;
                
                // simi d'ajout au panier
                showCartNotification('Voyage ajouté au panier');
                updateCartCount(1);
                
                // Redirection après un délai
                if (button.href) {
                    setTimeout(() => {
                        window.location.href = button.href;
                    }, 500);
                }
            });
        });
    }
    
    /**
     * Récupère un paramètre de l'URL
     * @param {string} name Nom du paramètre
     * @return {string|null} Valeur du paramètre ou null
     */
    function getUrlParam(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }
    
    /**
     * Maj compteur du panier
     * @param {number} increment Nombre à ajouter (optionnel)
     */
    function updateCartCount(increment = 0) {
        const countElement = document.getElementById('cart-count');
        if (!countElement) return;
        
        // simu comptage
        let count = parseInt(countElement.textContent) || 0;
        
        if (increment > 0) {
            count += increment;
            countElement.textContent = count;
            
            // Anime le compteur
            countElement.classList.add('pulse');
            setTimeout(() => {
                countElement.classList.remove('pulse');
            }, 500);
        } else {
            // Récupére le nombre d'éléments depuis le panier en session
            try {
                // récupère depuis localStorage (si défini par d'autres scripts)
                const cart = JSON.parse(localStorage.getItem('runetrek_cart') || '[]');
                count = cart.length;
            } catch (e) {
                // reset à une valeur par défaut
                count = Math.floor(Math.random() * 3);
            }
            
            countElement.textContent = count;
        }
    }
    
    /**
     * Affiche une notif d'ajout au panier
     * @param {string} message Message à afficher
     */
    function showCartNotification(message) {
        // Supprime toute notification existante
        const existingNotification = document.querySelector('.cart-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.textContent = message;
        
        // Ajoute au DOM
        document.body.appendChild(notification);
        
        // Supprime après 3 secondes (Oumar verif vite fait stp....)
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
