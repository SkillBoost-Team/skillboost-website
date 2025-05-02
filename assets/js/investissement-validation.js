// Fonction pour valider le formulaire d'investissement
function validateInvestissementForm(form) {
    let isValid = true;
    clearErrors();

    // Validation du montant
    const montantInput = form.querySelector('[name="montant"]');
    const montantValue = parseFloat(montantInput.value);
    const montantMax = parseFloat(montantInput.getAttribute('data-max'));
    
    if (!montantValue || isNaN(montantValue)) {
        showError(montantInput, 'Veuillez entrer un montant valide');
        isValid = false;
    } else if (montantValue <= 0) {
        showError(montantInput, 'Le montant doit être supérieur à 0');
        isValid = false;
    } else if (montantValue > montantMax) {
        showError(montantInput, `Le montant ne peut pas dépasser ${montantMax} DT`);
        isValid = false;
    }

    // Validation du pourcentage
    const pourcentageInput = form.querySelector('[name="pourcentage"]');
    const pourcentageValue = parseFloat(pourcentageInput.value);
    
    if (!pourcentageValue || isNaN(pourcentageValue)) {
        showError(pourcentageInput, 'Veuillez entrer un pourcentage valide');
        isValid = false;
    } else if (pourcentageValue <= 0 || pourcentageValue > 100) {
        showError(pourcentageInput, 'Le pourcentage doit être compris entre 1 et 100');
        isValid = false;
    }

    return isValid;
}

// Fonction pour afficher les messages d'erreur
function showError(input, message) {
    const formGroup = input.closest('.mb-3');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block';
    errorDiv.textContent = message;
    formGroup.appendChild(errorDiv);
    input.classList.add('is-invalid');
}

// Fonction pour effacer tous les messages d'erreur
function clearErrors() {
    const errorMessages = document.querySelectorAll('.invalid-feedback');
    const invalidInputs = document.querySelectorAll('.is-invalid');
    
    errorMessages.forEach(error => error.remove());
    invalidInputs.forEach(input => input.classList.remove('is-invalid'));
}

// Initialisation des écouteurs d'événements
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-type="investissement"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateInvestissementForm(this)) {
                e.preventDefault();
            }
        });

        // Validation en temps réel
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearErrors();
                validateInvestissementForm(form);
            });
        });
    });
}); 