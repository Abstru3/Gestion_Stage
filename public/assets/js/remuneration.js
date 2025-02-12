document.addEventListener('DOMContentLoaded', function() {
    const remunerationSelect = document.getElementById('remuneration');
    if (remunerationSelect) {
        toggleAutreMontant();
        
        // Si la valeur actuelle n'est pas dans les options prédéfinies, sélectionner "autre"
        const currentValue = remunerationSelect.value;
        const predefinedValues = ["417", "500", "600", "700", "800", "900", "1000"];
        
        if (!predefinedValues.includes(currentValue) && currentValue !== "") {
            remunerationSelect.value = "autre";
            const autreInput = document.getElementById('remuneration_autre');
            if (autreInput) {
                autreInput.value = currentValue;
                toggleAutreMontant();
            }
        }
    }
});

function toggleAutreMontant() {
    const select = document.getElementById('remuneration');
    const container = document.getElementById('autre_montant_container');
    const input = document.getElementById('remuneration_autre');
    const hiddenInput = document.getElementById('remuneration_hidden');
    
    if (!select || !container || !input) return;
    
    if (select.value === 'autre') {
        container.style.display = 'block';
        input.required = true;
        input.focus();
        // Mettre à jour la valeur cachée avec la valeur actuelle de l'input
        if (hiddenInput) {
            hiddenInput.value = input.value;
        }
    } else {
        container.style.display = 'none';
        input.required = false;
        // Mettre à jour la valeur cachée avec la valeur sélectionnée
        if (hiddenInput) {
            hiddenInput.value = select.value;
        }
    }
}

function updateRemuneration(value) {
    const input = document.getElementById('remuneration_autre');
    const hiddenInput = document.getElementById('remuneration_hidden');
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (!input || !submitButton) return;
    
    if (parseInt(value) < 417) {
        showWarning('La rémunération ne peut pas être inférieure au minimum légal (417€)');
        submitButton.disabled = true;
        input.style.borderColor = '#ff0000';
        if (hiddenInput) {
            hiddenInput.value = '';
        }
    } else {
        removeWarning();
        submitButton.disabled = false;
        input.style.borderColor = '#ddd';
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    }
}

function showWarning(message) {
    removeWarning(); // Supprimer l'ancien avertissement s'il existe
    
    const input = document.getElementById('remuneration_autre');
    if (!input) return;
    
    const warning = document.createElement('div');
    warning.id = 'remuneration_warning';
    warning.style.color = '#ff0000';
    warning.style.fontSize = '0.8em';
    warning.style.marginTop = '5px';
    warning.textContent = message;
    
    input.parentNode.appendChild(warning);
}

function removeWarning() {
    const warningDiv = document.getElementById('remuneration_warning');
    if (warningDiv) {
        warningDiv.remove();
    }
}