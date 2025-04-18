// validation.js

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reclamationForm');
    if (!form) return;

    // Validation en temps réel
    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function () {
            validateField(this);
        });
        field.addEventListener('blur', function () {
            validateField(this);
        });
    });

    // Validation à la soumission
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let isValid = true;
        const fieldsToValidate = ['full_name', 'email', 'subject', 'type', 'priority', 'description'];
        fieldsToValidate.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (isValid) {
            // Animation avant soumission
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Envoi en cours...';
            submitBtn.disabled = true;

            // Soumission après un léger délai pour l'animation
            setTimeout(() => {
                form.submit();
            }, 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Formulaire incomplet',
                text: 'Veuillez corriger les erreurs indiquées',
                confirmButtonColor: '#061429'
            });

            // Scroll vers le premier champ invalide
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    /**
     * Valide un champ spécifique
     * @param {HTMLElement} field - Le champ à valider
     * @returns {boolean} - True si le champ est valide, sinon false
     */
    function validateField(field) {
        const errorElement = document.getElementById(`${field.id}_error`);

        // Réinitialisation
        field.classList.remove('is-invalid', 'is-valid');
        if (errorElement) errorElement.textContent = '';

        let isValid = true;
        let errorMessage = '';

        if (field.required && !field.value.trim()) {
            isValid = false;
            errorMessage = 'Ce champ est obligatoire';
        } else if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
            isValid = false;
            errorMessage = 'Veuillez entrer un email valide';
        } else if (field.id === 'description' && field.value.trim().length < 20) {
            isValid = false;
            errorMessage = 'La description doit contenir au moins 20 caractères';
        } else if ((field.id === 'type' || field.id === 'priority') && field.value === '') {
            isValid = false;
            errorMessage = 'Veuillez faire une sélection';
        }

        if (!isValid) {
            field.classList.add('is-invalid');
            if (errorElement) errorElement.textContent = errorMessage;
        } else {
            field.classList.add('is-valid');
        }

        return isValid;
    }
});