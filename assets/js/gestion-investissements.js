document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons de mise à jour de statut
    const updateButtons = document.querySelectorAll('.update-status-btn');
    
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const investId = this.getAttribute('data-invest-id');
            const newStatus = this.getAttribute('data-status');
            
            console.log('Click sur le bouton:', {
                investId: investId,
                newStatus: newStatus
            });
            
            // Désactiver le bouton pendant la requête
            this.disabled = true;
            
            // Appel AJAX pour mettre à jour le statut
            updateInvestmentStatus(investId, newStatus, this);
        });
    });
});

function updateInvestmentStatus(investId, newStatus, button) {
    console.log('Début de la mise à jour du statut:', {
        investId: investId,
        newStatus: newStatus
    });

    const formData = new FormData();
    formData.append('invest_id', investId);
    formData.append('status', newStatus);

    // Log des données envoyées
    console.log('Données du formulaire:', {
        invest_id: formData.get('invest_id'),
        status: formData.get('status')
    });

    fetch('../controllers/update_investment_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Réponse reçue:', {
            status: response.status,
            statusText: response.statusText
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Données reçues:', data);
        
        if (data.success) {
            // Mettre à jour l'affichage du statut
            const statusCell = document.querySelector(`[data-invest-status="${investId}"]`);
            if (statusCell) {
                statusCell.textContent = newStatus;
                console.log('Statut mis à jour dans le DOM');
            } else {
                console.warn('Cellule de statut non trouvée dans le DOM');
            }
            
            // Masquer les boutons pour cet investissement
            const buttonContainer = document.querySelector(`[data-invest-actions="${investId}"]`);
            if (buttonContainer) {
                buttonContainer.style.display = 'none';
                console.log('Boutons masqués');
            } else {
                console.warn('Conteneur de boutons non trouvé dans le DOM');
            }
            
            // Afficher le message de succès
            showSuccessMessage(data.message);
        } else {
            // Réactiver le bouton en cas d'erreur
            if (button) button.disabled = false;
            
            console.error('Erreur retournée par le serveur:', data.message);
            showErrorMessage(data.message || 'Une erreur est survenue lors de la mise à jour du statut.');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la requête:', error);
        // Réactiver le bouton en cas d'erreur
        if (button) button.disabled = false;
        showErrorMessage('Une erreur est survenue lors de la communication avec le serveur.');
    });
}

function showSuccessMessage(message) {
    console.log('Affichage du message de succès:', message);
    showMessage(message, 'success-message');
}

function showErrorMessage(message) {
    console.error('Affichage du message d\'erreur:', message);
    showMessage(message, 'error-message');
}

function showMessage(message, className) {
    const messageDiv = document.createElement('div');
    messageDiv.className = className;
    messageDiv.textContent = message;
    
    // Insérer le message au début du conteneur principal
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        
        // Afficher le message avec une animation
        messageDiv.style.display = 'block';
        
        // Masquer le message après 3 secondes
        setTimeout(() => {
            messageDiv.style.opacity = '0';
            setTimeout(() => {
                messageDiv.remove();
            }, 300);
        }, 3000);
    } else {
        console.error('Conteneur principal non trouvé dans le DOM');
    }
} 