document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reclamationForm');
    
    if (!form) return;

    // Validation en temps réel
    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
    });

    // Validation à la soumission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        form.querySelectorAll('[required]').forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (isValid) {
            // Si tout est valide, on soumet le formulaire
            form.submit();
        } else {
            Swal.fire({
                title: 'Erreur',
                text: 'Veuillez corriger les erreurs dans le formulaire',
                icon: 'error'
            });
        }
    });

    function validateField(field) {
        // Réinitialisation
        field.classList.remove('is-invalid');
        
        // Validation
        if (field.required && !field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validation spécifique pour l'email
        if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Validation pour la description (min 20 caractères)
        if (field.id === 'description' && field.value.trim().length < 20) {
            field.classList.add('is-invalid');
            return false;
        }
        
        return true;
    }
});