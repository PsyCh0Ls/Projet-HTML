document.addEventListener('DOMContentLoaded', function() {
    // Vérif si nous sommes sur la page de profil
    const profileInfo = document.querySelector('.profile-info');
    if (!profileInfo) return;
    
    // Récupér tous les champs d'information du profil
    const profileFields = profileInfo.querySelectorAll('p');
    
    // Ajout du style pour l'édition
    const style = document.createElement('style');
    style.textContent = `
        .profile-field {
            position: relative;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            transition: all 0.3s;
            border: 1px solid transparent;
        }
        .profile-field:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 0, 0, 0.1);
        }
        .edit-button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0;
            transition: all 0.3s;
        }
        .profile-field:hover .edit-button {
            opacity: 1;
        }
        .profile-field.editing {
            background-color: rgba(30, 136, 229, 0.1);
            border-color: rgba(30, 136, 229, 0.2);
        }
        .field-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            justify-content: flex-end;
        }
        .save-button, .cancel-button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s;
        }
        .save-button {
            background-color: #1E88E5;
            color: white;
        }
        .save-button:hover {
            background-color: #1976D2;
            transform: translateY(-2px);
        }
        .cancel-button {
            background-color: #f5f5f5;
            color: #333;
        }
        .cancel-button:hover {
            background-color: #e0e0e0;
            transform: translateY(-2px);
        }
        .submit-changes {
            margin-top: 25px;
            padding: 12px 20px;
            background-color: #1E88E5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: none;
            font-weight: bold;
            animation: pulsate 2s infinite;
        }
        @keyframes pulsate {
            0% { box-shadow: 0 0 0 0 rgba(30, 136, 229, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(30, 136, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(30, 136, 229, 0); }
        }
        
        /* Mode sombre */
        .dark-mode .profile-field:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .dark-mode .profile-field.editing {
            background-color: rgba(30, 136, 229, 0.2);
            border-color: rgba(30, 136, 229, 0.3);
        }
        .dark-mode .cancel-button {
            background-color: #424242;
            color: white;
        }
        .dark-mode .cancel-button:hover {
            background-color: #616161;
        }
        .dark-mode .edit-input {
            background-color: #2a2a2a;
            color: #e0e0e0;
            border: 1px solid #444;
        }
    `;
    document.head.appendChild(style);
    
    // Variable pour suivre si des modifications ont été validées
    let hasChanges = false;
    const originalValues = {};
    const modifiedValues = {};
    
    // Transfor chaque paragraphe en champ éditable
    profileFields.forEach((field, index) => {
        // Ignore le bouton "Modifier le profil" existant
        if (field.querySelector('a.edit-profile')) return;
        
        // Récupére le texte et la clé du champ
        const fieldText = field.textContent;
        const [fieldName, fieldValue] = fieldText.split(':').map(str => str.trim());
        
        // Stocker la valeur originale
        originalValues[fieldName] = fieldValue;
        
        // Créer un conteneur pour le champ
        const fieldContainer = document.createElement('div');
        fieldContainer.className = 'profile-field';
        fieldContainer.dataset.fieldName = fieldName;
        fieldContainer.innerHTML = `<strong>${fieldName}:</strong> <span class="field-value">${fieldValue}</span>`;
        
        // Créer le bouton d'édition
        const editButton = document.createElement('button');
        editButton.className = 'edit-button';
        editButton.innerHTML = '✏️';
        editButton.title = 'Modifier';
        fieldContainer.appendChild(editButton);
        
        // Remplace le paragraphe original par notre conteneur
        field.parentNode.replaceChild(fieldContainer, field);
        
        // Gére le clic sur le bouton d'édition
        editButton.addEventListener('click', function() {
            // Empêche l'édition multiple
            const alreadyEditing = document.querySelector('.profile-field.editing');
            if (alreadyEditing && alreadyEditing !== fieldContainer) {
                // Annule l'édition en cours
                const editInput = alreadyEditing.querySelector('.edit-input');
                const actionsDiv = alreadyEditing.querySelector('.field-actions');
                const valueSpan = alreadyEditing.querySelector('.field-value');
                const editBtn = alreadyEditing.querySelector('.edit-button');
                
                valueSpan.style.display = '';
                alreadyEditing.classList.remove('editing');
                editInput.remove();
                actionsDiv.remove();
                editBtn.style.display = '';
            }
            
            // Marquer le champ comme en cours d'édition
            fieldContainer.classList.add('editing');
            
            // Récupérer la valeur actuelle
            const valueSpan = fieldContainer.querySelector('.field-value');
            const currentValue = valueSpan.textContent;
            
            // Remplacer par un champ de saisie
            valueSpan.style.display = 'none';
            editButton.style.display = 'none';
            
            // Créer un champ de saisie adapté au type de données
            let inputField;
            if (fieldName === 'Date de naissance') {
                inputField = document.createElement('input');
                inputField.type = 'date';
                // Convertir la date au format YYYY-MM-DD si possible
                const dateParts = currentValue.split('/');
                if (dateParts.length === 3) {
                    // Format supposé: DD/MM/YYYY
                    inputField.value = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
                } else {
                    inputField.value = currentValue;
                }
            } else if (fieldName === 'Adresse') {
                inputField = document.createElement('textarea');
                inputField.value = currentValue;
                inputField.rows = 2;
            } else {
                inputField = document.createElement('input');
                inputField.type = 'text';
                inputField.value = currentValue;
            }
            inputField.className = 'edit-input';
            inputField.style.width = '100%';
            inputField.style.padding = '8px';
            inputField.style.marginTop = '5px';
            inputField.style.borderRadius = '4px';
            inputField.style.border = '1px solid #ccc';
            
            // Créer les boutons d'action
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'field-actions';
            
            const saveButton = document.createElement('button');
            saveButton.className = 'save-button';
            saveButton.textContent = 'Valider';
            
            const cancelButton = document.createElement('button');
            cancelButton.className = 'cancel-button';
            cancelButton.textContent = 'Annuler';
            
            actionsDiv.appendChild(cancelButton);
            actionsDiv.appendChild(saveButton);
            
            // Ajouter les éléments au DOM
            fieldContainer.appendChild(inputField);
            fieldContainer.appendChild(actionsDiv);
            
            // Focus sur le champ
            inputField.focus();
            
            // Gérer la validation
            saveButton.addEventListener('click', function() {
                // Validation simple
                if (!inputField.value.trim()) {
                    inputField.style.borderColor = 'red';
                    
                    // Ajouter un message d'erreur s'il n'existe pas déjà
                    if (!fieldContainer.querySelector('.error-message')) {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Ce champ ne peut pas être vide';
                        errorMsg.style.color = 'red';
                        errorMsg.style.fontSize = '0.85rem';
                        errorMsg.style.marginTop = '5px';
                        fieldContainer.appendChild(errorMsg);
                    }
                    
                    return;
                }
                
                // Formater la date si nécessaire
                let displayValue = inputField.value;
                if (fieldName === 'Date de naissance' && inputField.type === 'date') {
                    const date = new Date(inputField.value);
                    displayValue = date.toLocaleDateString('fr-FR');
                }
                
                valueSpan.textContent = displayValue;
                valueSpan.style.display = '';
                fieldContainer.classList.remove('editing');
                
                // Supprimer les éléments d'édition
                inputField.remove();
                actionsDiv.remove();
                const errorMsg = fieldContainer.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
                editButton.style.display = '';
                
                // Stocker la valeur modifiée
                modifiedValues[fieldName] = inputField.value;
                
                // Marquer que des changements ont été faits
                hasChanges = true;
                showSubmitButton();
            });
            
            // Gérer l'annulation
            cancelButton.addEventListener('click', function() {
                valueSpan.style.display = '';
                fieldContainer.classList.remove('editing');
                
                // Supprimer les éléments d'édition
                inputField.remove();
                actionsDiv.remove();
                const errorMsg = fieldContainer.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
                editButton.style.display = '';
            });
        });
    });
    
    // Fonction pour afficher le bouton de soumission global
    function showSubmitButton() {
        // Vérifier si le bouton existe déjà
        let submitButton = document.querySelector('.submit-changes');
        
        if (!submitButton) {
            submitButton = document.createElement('button');
            submitButton.className = 'submit-changes';
            submitButton.textContent = 'Enregistrer les modifications';
            profileInfo.appendChild(submitButton);
            
            // Gérer le clic sur le bouton
            submitButton.addEventListener('click', function() {
                // Simuler un chargement
                submitButton.disabled = true;
                submitButton.textContent = 'Enregistrement en cours...';
                
                // Afficher un résumé des modifications
                let summary = 'Modifications à enregistrer:\n\n';
                for (const [field, value] of Object.entries(modifiedValues)) {
                    summary += `${field}: ${originalValues[field]} → ${value}\n`;
                }
                
                console.log(summary);
                
                // Simuler une requête AJAX
                setTimeout(() => {
                    // Ici, on simulerait l'envoi des modifications au serveur
                    showNotification('Les modifications ont été enregistrées avec succès.');
                    
                    // Réinitialise l'état
                    hasChanges = false;
                    for (const field in modifiedValues) {
                        originalValues[field] = modifiedValues[field];
                    }
                    Object.keys(modifiedValues).forEach(key => delete modifiedValues[key]);
                    
                    submitButton.disabled = false;
                    submitButton.textContent = 'Enregistrer les modifications';
                    submitButton.style.display = 'none';
                }, 1500);
            });
        }
        
        submitButton.style.display = 'block';
    }
    
    //afficher une notification
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.backgroundColor = '#4CAF50';
        notification.style.color = 'white';
        notification.style.padding = '15px 20px';
        notification.style.borderRadius = '4px';
        notification.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        notification.style.zIndex = '1000';
        notification.style.animation = 'fadeIn 0.3s, fadeOut 0.5s 2.5s forwards';
        notification.textContent = message;
        
        // Ajouter une animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
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
});
