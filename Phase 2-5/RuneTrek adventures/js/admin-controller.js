document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page admin
    const adminPage = document.querySelector('.admin-page');
    if (!adminPage) return;
    
    // Ajouter du style pour les éléments en cours de traitement
    const style = document.createElement('style');
    style.textContent = `
        .processing {
            opacity: 0.6;
            pointer-events: none;
        }
        .processing-overlay {
            position: relative;
        }
        .processing-overlay::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 24px;
            height: 24px;
            border: 3px solid rgba(30, 136, 229, 0.3);
            border-radius: 50%;
            border-top-color: #1E88E5;
            animation: spinner 1s infinite linear;
        }
        @keyframes spinner {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .admin-notification {
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
        .admin-notification.success {
            background-color: #4CAF50;
        }
        .admin-notification.error {
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
    
    // Intercepter les formulaires pour simuler le délai
    const userManagementForms = document.querySelectorAll('.manage-users form');
    
    userManagementForms.forEach(form => {
        // Remplacer le comportement par défaut
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer les données du formulaire
            const formData = new FormData(form);
            const action = formData.get('action');
            
            // Ajouter un effet de chargement
            form.classList.add('processing');
            form.classList.add('processing-overlay');
            
            // Simuler un délai de traitement (2 secondes)
            setTimeout(() => {
                // Retirer l'effet de chargement
                form.classList.remove('processing');
                form.classList.remove('processing-overlay');
                
                // Pour les changements de rôle, mettre à jour l'affichage
                if (action === 'change_role') {
                    const userId = formData.get('user_id');
                    const newRole = formData.get('role');
                    const userItem = form.closest('li');
                    
                    // Mettre à jour le texte affiché
                    const userText = userItem.textContent;
                    const rolePattern = /Rôle:\s*(admin|user|normal)/i;
                    const updatedText = userText.replace(rolePattern, `Rôle: ${newRole}`);
                    
                    // Créer un élément temporaire pour extraire le texte
                    const tempElement = document.createElement('div');
                    tempElement.innerHTML = updatedText;
                    
                    // Afficher une notification
                    showNotification(`Rôle mis à jour avec succès pour l'utilisateur #${userId}`, 'success');
                    
                    // Soumettre réellement le formulaire après le délai
                    form.submit();
                }
                // Pour les suppressions, simuler la suppression
                else if (action === 'delete_user') {
                    const userId = formData.get('user_id');
                    const userItem = form.closest('li');
                    
                    // Animer la suppression
                    userItem.style.transition = 'all 0.5s ease-out';
                    userItem.style.opacity = '0';
                    userItem.style.height = '0';
                    userItem.style.overflow = 'hidden';
                    
                    // Afficher une notification
                    showNotification(`Utilisateur #${userId} supprimé avec succès`, 'success');
                    
                    // Soumettre réellement le formulaire après un autre délai
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                }
                // Pour tout autre type d'action
                else {
                    // Soumettre réellement le formulaire après le délai
                    form.submit();
                }
            }, 2000);
        });
    });
    
    // Fonction pour afficher une notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `admin-notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Amélioration pour les formulaires de gestion des voyages
    const tripForms = document.querySelectorAll('.manage-trips form');
    
    tripForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Ne pas intercepter s'il s'agit d'un formulaire d'édition (affichage de l'interface d'édition)
            if (form.querySelector('input[name="action"][value="edit_trip"]') && 
                !form.classList.contains('editing')) {
                return;
            }
            
            e.preventDefault();
            
            // Récupérer les données du formulaire
            const formData = new FormData(form);
            const action = formData.get('action');
            
            // Validation côté client
            let isValid = true;
            const requiredInputs = form.querySelectorAll('input[required], textarea[required]');
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('input-error');
                    
                    // Créer un message d'erreur s'il n'existe pas déjà
                    if (!input.parentNode.querySelector('.error-message')) {
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        errorMessage.textContent = 'Ce champ est requis';
                        input.parentNode.appendChild(errorMessage);
                    }
                } else {
                    input.classList.remove('input-error');
                    const errorMessage = input.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });
            
            if (!isValid) {
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
                return;
            }
            
            // Ajouter un effet de chargement
            form.classList.add('processing');
            form.classList.add('processing-overlay');
            
            // Simuler un délai de traitement (1.5 secondes)
            setTimeout(() => {
                // Retirer l'effet de chargement
                form.classList.remove('processing');
                form.classList.remove('processing-overlay');
                
                if (action === 'add_trip') {
                    showNotification('Voyage ajouté avec succès', 'success');
                } else if (action === 'edit_trip') {
                    showNotification('Voyage modifié avec succès', 'success');
                } else if (action === 'delete_trip') {
                    const tripId = formData.get('trip_id');
                    const tripItem = form.closest('li');
                    
                    // Animer la suppression
                    if (tripItem) {
                        tripItem.style.transition = 'all 0.5s ease-out';
                        tripItem.style.opacity = '0';
                        tripItem.style.height = '0';
                        tripItem.style.overflow = 'hidden';
                        
                        setTimeout(() => {
                            tripItem.remove();
                        }, 500);
                    }
                    
                    showNotification(`Voyage #${tripId} supprimé avec succès`, 'success');
                    return; // Ne pas soumettre le formulaire, la suppression est simulée
                }
                
                // Soumettre réellement le formulaire après le délai
                form.submit();
            }, 1500);
        });
    });
});