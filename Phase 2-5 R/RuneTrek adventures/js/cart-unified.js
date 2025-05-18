document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si l'utilisateur est connecté (si le lien de déconnexion existe)
    const logoutLink = document.querySelector('a[href="logout.php"]');
    if (!logoutLink) return; // L'utilisateur n'est pas connecté
    
    // Configurer les boutons d'ajout au panier sur les pages de voyage
    setupAddToCartButtons();
    
    // Configurer les boutons de suppression dans la page panier
    setupRemoveItemButtons();
    
    // Configurer le bouton de vidage du panier
    setupClearCartButton();
    
    // Ajouter une animation pour les modifications d'options sur la page de détails
    setupOptionChanges();
    
    /**
     * Configure les boutons d'ajout au panier
     */
    function setupAddToCartButtons() {
        // Trouver tous les boutons d'ajout au panier
        const addToCartButtons = document.querySelectorAll('.add-to-cart, .add-to-cart-button');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Si c'est sur la page de détails, utiliser le formulaire pour capturer les options
                const tripDetailsPage = document.querySelector('.trip-details-page');
                if (tripDetailsPage) {
                    const form = tripDetailsPage.querySelector('form');
                    const addToCartField = document.getElementById('add_to_cart_field');
                    
                    if (form && addToCartField) {
                        // Définir que le voyage doit être ajouté au panier
                        addToCartField.value = '1';
                        
                        // Ajouter une animation au bouton
                        button.classList.add('button-pulse');
                        
                        // Soumettre le formulaire après un court délai pour l'animation
                        setTimeout(() => {
                            form.submit();
                        }, 300);
                        
                        return;
                    }
                }
                
                // Pour les autres pages, utiliser la méthode standard
                // Récupérer l'ID du voyage
                const tripId = button.getAttribute('data-id') || window.location.search.match(/id=(\d+)/)?.[1];
                if (!tripId) return;
                
                // Créer un formulaire pour soumettre l'ajout au panier
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'add_to_cart.php';
                form.style.display = 'none';
                
                // Ajouter l'ID du voyage
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'trip_id';
                idInput.value = tripId;
                
                form.appendChild(idInput);
                document.body.appendChild(form);
                
                // Ajouter une animation au bouton
                button.classList.add('button-pulse');
                setTimeout(() => {
                    button.classList.remove('button-pulse');
                }, 500);
                
                // Afficher une notification
                showNotification('Voyage ajouté au panier', 'success');
                
                // Soumettre le formulaire après un court délai
                setTimeout(() => {
                    form.submit();
                }, 300);
            });
        });
    }
    
    /**
     * Configure les boutons de suppression d'articles dans la page panier
     */
    function setupRemoveItemButtons() {
        const removeButtons = document.querySelectorAll('.remove-item');
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('form');
                const cartItem = this.closest('.cart-item');
                
                // Animer la suppression
                if (cartItem) {
                    cartItem.style.transition = 'all 0.3s ease-out';
                    cartItem.style.opacity = '0';
                    cartItem.style.height = '0';
                    cartItem.style.overflow = 'hidden';
                    
                    // Soumettre après l'animation
                    setTimeout(() => {
                        form.submit();
                    }, 300);
                } else {
                    form.submit();
                }
            });
        });
    }
    
    /**
     * Configure le bouton pour vider le panier
     */
    function setupClearCartButton() {
        const clearButton = document.querySelector('.clear-cart');
        
        if (clearButton) {
            clearButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Demander confirmation
                if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
                    // Animer les éléments du panier
                    const cartItems = document.querySelectorAll('.cart-item');
                    
                    cartItems.forEach(item => {
                        item.style.transition = 'all 0.3s ease-out';
                        item.style.opacity = '0';
                        item.style.height = '0';
                        item.style.overflow = 'hidden';
                    });
                    
                    // Soumettre le formulaire après animation
                    setTimeout(() => {
                        this.closest('form').submit();
                    }, 300);
                }
            });
        }
    }
    
    /**
     * Configure les changements d'options sur la page de détails
     */
    function setupOptionChanges() {
        // Vérifier si nous sommes sur la page de détails d'un voyage
        const tripDetailsPage = document.querySelector('.trip-details-page');
        if (!tripDetailsPage) return;
        
        // Récupérer tous les sélecteurs d'options
        const optionSelectors = tripDetailsPage.querySelectorAll('select');
        const originalValues = {};
        
        // Stocker les valeurs originales des sélecteurs
        optionSelectors.forEach(selector => {
            originalValues[selector.name] = selector.value;
            
            // Ajouter un gestionnaire pour les changements
            selector.addEventListener('change', function() {
                if (originalValues[selector.name] !== selector.value) {
                    // Marquer visuellement que cette option a été modifiée
                    const formGroup = selector.closest('.form-group');
                    if (formGroup) {
                        formGroup.classList.add('option-modified');
                    }
                    
                    // Indiquer que le voyage sera ajouté au panier lors de la soumission
                    const cartStatus = document.querySelector('.cart-status');
                    if (cartStatus) {
                        cartStatus.classList.add('active');
                        cartStatus.innerHTML = '<p><strong>✓ Ce voyage sera ajouté à votre panier</strong></p>';
                    }
                    
                    // Ajouter un champ caché pour indiquer l'ajout au panier
                    let hiddenField = document.getElementById('add_to_cart_field');
                    if (!hiddenField) {
                        hiddenField = document.createElement('input');
                        hiddenField.type = 'hidden';
                        hiddenField.name = 'add_to_cart';
                        hiddenField.value = '1';
                        hiddenField.id = 'add_to_cart_field';
                        selector.form.appendChild(hiddenField);
                    } else {
                        hiddenField.value = '1';
                    }
                }
            });
        });
    }
    
    /**
     * Affiche une notification à l'utilisateur
     * @param {string} message Message à afficher
     * @param {string} type Type de notification (success, error, info)
     */
    function showNotification(message, type = 'info') {
        // Supprimer toute notification existante
        const existingNotification = document.querySelector('.cart-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification ' + type;
        notification.textContent = message;
        
        // Styles
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '15px 20px';
        notification.style.borderRadius = '4px';
        notification.style.zIndex = '1000';
        notification.style.fontWeight = 'bold';
        notification.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        
        // Couleur selon le type
        if (type === 'success') {
            notification.style.backgroundColor = '#4CAF50';
            notification.style.color = 'white';
        } else if (type === 'error') {
            notification.style.backgroundColor = '#F44336';
            notification.style.color = 'white';
        } else {
            notification.style.backgroundColor = '#1E88E5';
            notification.style.color = 'white';
        }
        
        // Animation
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        notification.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
        
        // Ajouter au DOM
        document.body.appendChild(notification);
        
        // Déclencher l'animation
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 10);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            
            // Retirer du DOM après la transition
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
});