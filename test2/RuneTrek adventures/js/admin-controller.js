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
                    const roleSelect = form.querySelector('select[name="role"]');
                    
                    // Mettre à jour le texte affiché
                    const userText = userItem.textContent;
                    const rolePattern = /Rôle:\s*(admin|user|normal)/i;
                    const updatedText = userText.replace(rolePattern, `Rôle: ${newRole}`);
                    
                    // Afficher une notification
                    showNotification(`Rôle mis à jour avec succès pour l'utilisateur #${userId}`);
                    
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
                    showNotification(`Utilisateur #${userId} supprimé avec succès`);
                    
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
    
    // Amélioration pour les rôles (transformer en toggles)
    const roleForms = document.querySelectorAll('.manage-users form select[name="role"]');
    roleForms.forEach(select => {
        // Créer un container pour le toggle
        const toggleContainer = document.createElement('div');
        toggleContainer.className = 'role-toggle-container';
        toggleContainer.style.display = 'inline-block';
        
        // Récupérer la valeur actuelle
        const isAdmin = select.value === 'admin';
        
        // Créer le toggle
        toggleContainer.innerHTML = `
            <label class="switch">
                <input type="checkbox" class="role-toggle" ${isAdmin ? 'checked' : ''}>
                <span class="slider round"></span>
            </label>
            <span class="role-label">${isAdmin ? 'Admin' : 'Normal'}</span>
        `;
        
        // Ajouter du style pour le toggle
        const toggleStyle = document.createElement('style');
        toggleStyle.textContent = `
            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 30px;
                margin-right: 10px;
            }
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
            }
            .slider:before {
                position: absolute;
                content: "";
                height: 22px;
                width: 22px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
            }
            input:checked + .slider {
                background-color: #1E88E5;
            }
            input:checked + .slider:before {
                transform: translateX(30px);
            }
            .slider.round {
                border-radius: 34px;
            }
            .slider.round:before {
                border-radius: 50%;
            }
            .role-label {
                font-size: 14px;
                vertical-align: middle;
                display: inline-block;
                margin-left: 5px;
            }
            
            /* Mode sombre */
            .dark-mode .slider {
                background-color: #555;
            }
            .dark-mode input:checked + .slider {
                background-color: #1976D2;
            }
        `;
        document.head.appendChild(toggleStyle);
        
        // Masquer le select original
        select.style.display = 'none';
        
        // Insérer le toggle après le select
        select.parentNode.insertBefore(toggleContainer, select.nextSibling);
        
        // Récupérer le formulaire parent
        const form = select.closest('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Masquer le bouton de soumission
        if (submitButton) {
            submitButton.style.display = 'none';
        }
        
        // Écouter les changements sur le toggle
        const toggle = toggleContainer.querySelector('.role-toggle');
        const roleLabel = toggleContainer.querySelector('.role-label');
        
        toggle.addEventListener('change', function() {
            // Récupérer la nouvelle valeur
            const isChecked = this.checked;
            
            // Mettre à jour le select
            select.value = isChecked ? 'admin' : 'normal';
            
            // Désactiver le toggle pendant le traitement
            this.disabled = true;
            toggleContainer.classList.add('processing');
            
            // Simuler un délai de traitement
            setTimeout(() => {
                // Réactiver le toggle
                this.disabled = false;
                toggleContainer.classList.remove('processing');
                
                // Mettre à jour le label
                roleLabel.textContent = isChecked ? 'Admin' : 'Normal';
                
                // Afficher une notification
                const userId = form.querySelector('input[name="user_id"]').value;
                showNotification(`Rôle mis à jour avec succès pour l'utilisateur #${userId}`);
                
                // Soumettre le formulaire
                form.submit();
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
});