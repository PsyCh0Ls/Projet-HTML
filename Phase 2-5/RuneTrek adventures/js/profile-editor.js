document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page de profil
    const profileInfo = document.querySelector('.profile-info');
    if (!profileInfo) return;
    
    // Récupérer tous les champs d'information du profil
    const profileFields = profileInfo.querySelectorAll('p');
    
    // Ajouter du style pour l'édition
    const style = document.createElement('style');
    style.textContent = `
        .profile-field {
            position: relative;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .profile-field:hover {
            background-color: rgba(0, 0, 0, 0.05);
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
            display: none;
        }
        .profile-field:hover .edit-button {
            display: block;
        }
        .profile-field.editing {
            background-color: rgba(30, 136, 229, 0.1);
        }
        .field-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .save-button, .cancel-button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .save-button {
            background-color: #1E88E5;
            color: white;
        }
        .cancel-button {
            background-color: #f5f5f5;
            color: #333;
        }
        .submit-changes {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #1E88E5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: none;
        }
    `;
    document.head.appendChild(style);
    
    // Variable pour suivre si des modifications ont été validées
    let hasChanges = false;
    const originalValues = {};
    
    // Transformer chaque paragraphe en champ éditable
    profileFields.forEach((field, index) => {
        // Ignorer le bouton "Modifier le profil" existant
        if (field.querySelector('a.edit-profile')) return;
        
        // Récupérer le texte et la clé du champ
        const fieldText = field.textContent;
        const [fieldName, fieldValue] = fieldText.split(':').map(str => str.trim());
        
        // Stocker la valeur originale
        originalValues[fieldName] = fieldValue;
        
        // Créer un conteneur pour le champ
        const fieldContainer = document.createElement('div');
        fieldContainer.className = 'profile-field';
        fieldContainer.innerHTML = `<strong>${fieldName}:</strong> <span class="field-value">${fieldValue}</span>`;
        
        // Créer le bouton d'édition
        const editButton = document.createElement('button');
        editButton.className = 'edit-button';
        editButton.innerHTML = '✏️';
        editButton.title = 'Modifier';
        fieldContainer.appendChild(editButton);
        
        // Remplacer le paragraphe original par notre conteneur
        field.parentNode.replaceChild(fieldContainer, field);
        
        // Gérer le clic sur le bouton d'édition
        editButton.addEventListener('click', function() {
            // Empêcher l'édition multiple
            const alreadyEditing = document.querySelector('.profile-field.editing');
            if (alreadyEditing && alreadyEditing !== fieldContainer) return;
            
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
                inputField.value = currentValue;
            } else {
                inputField = document.createElement('input');
                inputField.type = 'text';
                inputField.value = currentValue;
            }
            inputField.className = 'edit-input';
            inputField.style.width = '100%';
            inputField.style.padding = '5px';
            
            // Créer les boutons d'action
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'field-actions';
            
            const saveButton = document.createElement('button');
            saveButton.className = 'save-button';
            saveButton.textContent = 'Valider';
            
            const cancelButton = document.createElement('button');
            cancelButton.className = 'cancel-button';
            cancelButton.textContent = 'Annuler';
            
            actionsDiv.appendChild(saveButton);
            actionsDiv.appendChild(cancelButton);
            
            // Ajouter les éléments au DOM
            fieldContainer.appendChild(inputField);
            fieldContainer.appendChild(actionsDiv);
            
            // Focus sur le champ
            inputField.focus();
            
            // Gérer la validation
            saveButton.addEventListener('click', function() {
                valueSpan.textContent = inputField.value;
                valueSpan.style.display = '';
                fieldContainer.classList.remove('editing');
                
                // Supprimer les éléments d'édition
                inputField.remove();
                actionsDiv.remove();
                editButton.style.display = '';
                
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
                // Ici, on simulerait l'envoi des modifications au serveur
                alert('Les modifications ont été enregistrées.');
                
                // Réinitialiser l'état
                hasChanges = false;
                submitButton.style.display = 'none';
            });
        }
        
        submitButton.style.display = 'block';
    }
});