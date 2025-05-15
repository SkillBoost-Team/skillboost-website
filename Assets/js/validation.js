/**
 * Gestion complète de la validation du formulaire de réclamation
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reclamationForm');
    const lang = document.documentElement.lang || 'fr';
    
    // Dictionnaire des messages d'erreur par langue
    const errorMessages = {
        fr: {
            full_name: 'Le nom complet doit contenir au moins 3 caractères',
            email: 'Veuillez entrer une adresse email valide',
            subject: 'Le sujet doit contenir au moins 3 caractères',
            type: 'Veuillez sélectionner un type',
            priority: 'Veuillez sélectionner une priorité',
            description: 'La description doit faire au moins 20 caractères'
        },
        en: {
            full_name: 'Full name must be at least 3 characters',
            email: 'Please enter a valid email address',
            subject: 'Subject must be at least 3 characters',
            type: 'Please select a type',
            priority: 'Please select a priority',
            description: 'Description must be at least 20 characters'
        },
        es: {
            full_name: 'El nombre debe tener al menos 3 caracteres',
            email: 'Por favor ingrese un correo electrónico válido',
            subject: 'El asunto debe tener al menos 3 caracteres',
            type: 'Por favor seleccione un tipo',
            priority: 'Por favor seleccione una prioridad',
            description: 'La descripción debe tener al menos 20 caracteres'
        },
        ar: {
            full_name: 'يجب أن يكون الاسم الكامل 3 أحرف على الأقل',
            email: 'الرجاء إدخال عنوان بريد إلكتروني صالح',
            subject: 'يجب أن يكون الموضوع 3 أحرف على الأقل',
            type: 'الرجاء تحديد نوع',
            priority: 'الرجاء تحديد أولوية',
            description: 'يجب أن يكون الوصف 20 حرفًا على الأقل'
        }
    };

    // Initialisation de la validation
    initValidation();

    function initValidation() {
        if (!form) return;

        // Écouteurs d'événements
        form.addEventListener('submit', handleFormSubmit);
        
        // Validation en temps réel
        form.querySelectorAll('[required]').forEach(input => {
            input.addEventListener('input', () => clearError(input));
        });

        // Validation spécifique pour les selects
        ['type', 'priority'].forEach(id => {
            const select = document.getElementById(id);
            if (select) {
                select.addEventListener('change', () => validateSelect(select));
            }
        });
    }

    /**
     * Gère la soumission du formulaire
     */
    function handleFormSubmit(e) {
        e.preventDefault();
        
        const isValid = validateAllFields();
        if (isValid) {
            form.submit();
        } else {
            scrollToFirstError();
        }
    }

    /**
     * Valide tous les champs du formulaire
     */
    function validateAllFields() {
        let isValid = true;
        
        // Validation du nom complet
        const fullName = document.getElementById('full_name');
        if (!validateText(fullName, 3)) {
            showError(fullName, errorMessages[lang].full_name);
            isValid = false;
        }

        // Validation de l'email
        const email = document.getElementById('email');
        if (!validateEmail(email)) {
            showError(email, errorMessages[lang].email);
            isValid = false;
        }

        // Validation du sujet
        const subject = document.getElementById('subject');
        if (!validateText(subject, 3)) {
            showError(subject, errorMessages[lang].subject);
            isValid = false;
        }

        // Validation du type
        const type = document.getElementById('type');
        if (!validateSelect(type)) {
            showError(type, errorMessages[lang].type);
            isValid = false;
        }

        // Validation de la priorité
        const priority = document.getElementById('priority');
        if (!validateSelect(priority)) {
            showError(priority, errorMessages[lang].priority);
            isValid = false;
        }

        // Validation de la description
        const description = document.getElementById('description');
        if (!validateText(description, 20)) {
            showError(description, errorMessages[lang].description);
            isValid = false;
        }

        return isValid;
    }

    /**
     * Valide un champ texte
     */
    function validateText(input, minLength) {
        return input.value.trim().length >= minLength;
    }

    /**
     * Valide un email
     */
    function validateEmail(input) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(input.value.trim());
    }

    /**
     * Valide un select
     */
    function validateSelect(select) {
        return select.value !== '';
    }

    /**
     * Affiche un message d'erreur
     */
    function showError(input, message) {
        input.classList.add('is-invalid');
        const errorElement = document.getElementById(input.id + '_error');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    /**
     * Efface une erreur
     */
    function clearError(input) {
        input.classList.remove('is-invalid');
        const errorElement = document.getElementById(input.id + '_error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }

    /**
     * Fait défiler jusqu'au premier champ invalide
     */
    function scrollToFirstError() {
        const firstError = form.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }
});