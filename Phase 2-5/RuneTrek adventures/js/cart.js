document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si l'utilisateur est connecté (si le lien de déconnexion existe)
    const logoutLink = document.querySelector('a[href="logout.php"]');
    if (!logoutLink) return; // L'utilisateur n'est pas connecté
    
    // Vérifier si l'indicateur de panier existe déjà
    let cartIndicator = document.getElementById('cart-indicator');
    
    // Fonction pour créer l'indicateur du panier
    function createCartIndicator() {
        // Créer l'élément du panier
        cartIndicator = document.createElement('li');
        cartIndicator.id = 'cart-indicator';
        cartIndicator.innerHTML = `
            <a href="cart.php" id="cart-link">
                🛒 Panier <span id="cart-count">0</span>
            </a>
        `;
        
        // Ajouter à la navigation
        const navList = logoutLink.parentNode.parentNode;
        navList.insertBefore(cartIndicator, logoutLink.parentNode);
    }
    
    // Créer l'indicateur s'il n'existe pas
    if (!cartIndicator) {
        createCartIndicator();
    }
    
    // Ajouter des styles spécifiques pour l'indicateur du panier
    const style = document.createElement('style');
    style.textContent = `
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
        
        .cart-badge-animation {
            animation: pulse 0.5s ease-out;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        
        /* Mode sombre */
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
    `;
    document.head.appendChild(style);
    
    // Fonction pour mettre à jour le nombre d'articles dans le panier
    function updateCartCount(count) {
        const countElement = document.getElementById('cart-count');
        if (countElement) {
            countElement.textContent = count;
            
            // Ajouter une animation
            countElement.classList.add('cart-badge-animation');
            setTimeout(() => {
                countElement.classList.remove('cart-badge-animation');
            }, 500);
        }
    }
    
    // Fonction pour obtenir le nombre d'articles dans le panier
    function fetchCartCount() {
        fetch('cart_count.php')
            .then(response => response.json())
            .then(data => {
                if (data.count !== undefined) {
                    updateCartCount(data.count);
                }
            })
            .catch(error => console.error('Erreur lors de la récupération du panier:', error));
    }
    
    // Récupérer le compte initial
    fetchCartCount();
    
    // Pour les boutons "Ajouter au panier" sur la page de détails du voyage
    const addToCartButton = document.querySelector('button[name="add_to_cart"]');
    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            // L'animation sera gérée après la redirection vers la page du panier
        });
    }
});