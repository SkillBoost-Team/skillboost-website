/**
 * Gestionnaire de saisie vocale pour les formulaires
 */
class VoiceInput {
    constructor(textareaId, buttonId, lang = 'fr-FR') {
        this.textarea = document.getElementById(textareaId);
        this.button = document.getElementById(buttonId);
        this.lang = lang;
        this.isListening = false;
        this.recognition = null;
        
        this.init();
    }

    init() {
        // Vérifie si la reconnaissance vocale est supportée
        if (!('webkitSpeechRecognition' in window)) {
            this.button.style.display = 'none';
            console.warn('La reconnaissance vocale n\'est pas supportée par ce navigateur');
            return;
        }

        // Initialise l'API de reconnaissance vocale
        this.recognition = new webkitSpeechRecognition();
        this.recognition.continuous = false;
        this.recognition.interimResults = false;
        this.recognition.lang = this.lang;

        // Configure les événements
        this.button.addEventListener('click', () => this.toggleListening());
        this.recognition.onresult = (event) => this.handleResult(event);
        this.recognition.onerror = (event) => this.handleError(event);
        this.recognition.onend = () => this.stopListening();
    }

    toggleListening() {
        if (this.isListening) {
            this.stopListening();
        } else {
            this.startListening();
        }
    }

    startListening() {
        try {
            this.recognition.start();
            this.isListening = true;
            this.button.innerHTML = '<i class="fas fa-microphone-slash"></i>';
            this.button.classList.add('listening');
            
            // Ajoute un indicateur visuel
            const indicator = document.createElement('div');
            indicator.className = 'voice-recording-indicator';
            indicator.innerHTML = '<span class="pulse"></span> Enregistrement...';
            this.button.parentNode.appendChild(indicator);
            
            // Supprime l'indicateur après 3 secondes (au cas où)
            setTimeout(() => {
                if (indicator.parentNode) {
                    indicator.parentNode.removeChild(indicator);
                }
            }, 3000);
            
        } catch (e) {
            console.error('Erreur lors du démarrage de la reconnaissance vocale:', e);
            this.stopListening();
        }
    }

    stopListening() {
        this.isListening = false;
        this.button.innerHTML = '<i class="fas fa-microphone"></i>';
        this.button.classList.remove('listening');
        
        // Essaye d'arrêter la reconnaissance si elle est en cours
        try {
            this.recognition.stop();
        } catch (e) {
            // Ignore les erreurs d'arrêt
        }
        
        // Supprime tous les indicateurs visuels
        const indicators = document.querySelectorAll('.voice-recording-indicator');
        indicators.forEach(ind => ind.parentNode?.removeChild(ind));
    }

    handleResult(event) {
        const transcript = event.results[0][0].transcript;
        const currentText = this.textarea.value;
        
        // Insère le texte à la position du curseur ou à la fin
        if (this.textarea.selectionStart || this.textarea.selectionStart === 0) {
            const startPos = this.textarea.selectionStart;
            const endPos = this.textarea.selectionEnd;
            this.textarea.value = currentText.substring(0, startPos) + transcript + currentText.substring(endPos);
            
            // Replace le curseur après le texte inséré
            this.textarea.selectionStart = startPos + transcript.length;
            this.textarea.selectionEnd = startPos + transcript.length;
        } else {
            this.textarea.value += ' ' + transcript;
        }
        
        // Déclenche l'événement input pour la validation
        const inputEvent = new Event('input', { bubbles: true });
        this.textarea.dispatchEvent(inputEvent);
    }

    handleError(event) {
        console.error('Erreur de reconnaissance vocale:', event.error);
        
        let errorMessage;
        switch(event.error) {
            case 'no-speech':
                errorMessage = 'Aucune parole détectée. Essayez à nouveau.';
                break;
            case 'audio-capture':
                errorMessage = 'Problème de capture audio. Vérifiez votre microphone.';
                break;
            case 'not-allowed':
                errorMessage = 'Permission non accordée pour utiliser le microphone.';
                break;
            default:
                errorMessage = 'Erreur lors de la reconnaissance vocale.';
        }
        
        // Affiche un message d'erreur temporaire
        const errorDiv = document.createElement('div');
        errorDiv.className = 'voice-error-message';
        errorDiv.textContent = errorMessage;
        this.button.parentNode.appendChild(errorDiv);
        
        // Supprime le message après 5 secondes
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 5000);
    }
}

// Initialisation pour le champ de description
document.addEventListener('DOMContentLoaded', () => {
    const voiceInput = new VoiceInput('description', 'voiceBtn', document.documentElement.lang || 'fr-FR');
});

// Styles dynamiques
const style = document.createElement('style');
style.textContent = `
    .voice-recording-indicator {
        position: absolute;
        top: -30px;
        right: 0;
        background: #e74c3c;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10;
    }
    .voice-recording-indicator .pulse {
        display: inline-block;
        width: 10px;
        height: 10px;
        background: white;
        border-radius: 50%;
        margin-right: 5px;
        animation: pulse 1.5s infinite;
    }
    .voice-error-message {
        position: absolute;
        top: -30px;
        right: 0;
        background: #e74c3c;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(0.95); opacity: 1; }
    }
    .voice-btn.listening {
        color: #e74c3c;
        animation: none;
    }
`;
document.head.appendChild(style);