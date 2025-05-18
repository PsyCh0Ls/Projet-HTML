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
            content: '⌛';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5rem;
            animation: rotate 1s infinite linear;
        }
        @keyframes rotate {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
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
                    const rolePattern = /Rôle:\s*(admin|user)/i;
                    const updatedText = userText.replace(rolePattern, `Rôle: ${newRole}`);
                    
                    // Créer un élément temporaire pour extraire le texte
                    const tempElement = document.createElement('div');
                    tempElement.innerHTML = updatedText;
                    
                    // Simuler une mise à jour réussie
                    alert(`Rôle mis à jour avec succès pour l'utilisateur #${userId}`);
                    
                    // Soumettre réellement le formulaire après le délai
                    form.submit();
                }
                // Pour les suppressions, simuler la suppression
                else if (action === 'delete_user') {
                    const userId = formData.get('user_id');
                    const userItem = form.closest('li');
                    
                    // Simuler une suppression réussie
                    alert(`Utilisateur #${userId} supprimé avec succès`);
                    
                    // Soumettre réellement le formulaire après le délai
                    form.submit();
                }
                // Pour tout autre type d'action
                else {
                    // Soumettre réellement le formulaire après le délai
                    form.submit();
                }
            }, 2000);
        });
    });
});