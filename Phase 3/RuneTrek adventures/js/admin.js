document.addEventListener('DOMContentLoaded', function() {
    // check page admin
    const adminPage = document.querySelector('.admin-page');
    if (!adminPage) return;
    
    // Ajout du style pour les éléments en cours de traitement
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
    
    // Intercept les formulaires pour simuler le délai
    const userManagementForms = document.querySelectorAll('.manage-users form');
    
    userManagementForms.forEach(form => {
        // Remplace le comportement par défaut
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupére les données du formulaire
            const formData = new FormData(form);
            const action = formData.get('action');
            
            // effet de chargement
            form.classList.add('processing');
            form.classList.add('processing-overlay');
            
            //  délai de traitement (2 secondes)
            setTimeout(() => {
                // Retire l'effet de chargement
                form.classList.remove('processing');
                form.classList.remove('processing-overlay');
                
                // Pour les changements de rôle, maj affichage
                if (action === 'change_role') {
                    const userId = formData.get('user_id');
                    const newRole = formData.get('role');
                    const userItem = form.closest('li');
                    
                    // maj texte affiché
                    const userText = userItem.textContent;
                    const rolePattern = /Rôle:\s*(admin|user)/i;
                    const updatedText = userText.replace(rolePattern, `Rôle: ${newRole}`);
                    
                    // Créer un élément temporaire pour extraire le texte
                    const tempElement = document.createElement('div');
                    tempElement.innerHTML = updatedText;
                    
                    // Simuler une maj réussie
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
