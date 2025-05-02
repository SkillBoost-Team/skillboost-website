/**
 * Gestion du chatbot avec reconnaissance multilingue
 */
class Chatbot {
    constructor() {
        this.lang = document.documentElement.lang || 'fr';
        this.isOpen = false;
        this.initElements();
        this.initEvents();
        this.initTranslations();
    }

    /**
     * Initialise les éléments DOM
     */
    initElements() {
        this.container = document.querySelector('.chatbot-container');
        this.toggler = document.querySelector('.chatbot-toggler');
        this.header = document.querySelector('.chatbot-header');
        this.body = document.querySelector('.chatbot-body');
        this.input = document.getElementById('chatbot-input');
        this.sendBtn = document.getElementById('chatbot-send');
        this.closeBtn = document.querySelector('.close-chatbot');
    }

    /**
     * Initialise les traductions
     */
    initTranslations() {
        this.translations = {
            fr: {
                placeholder: 'Posez votre question ici...',
                typing: 'Le chatbot écrit...',
                error: 'Erreur de connexion avec le chatbot'
            },
            en: {
                placeholder: 'Ask your question here...',
                typing: 'Chatbot is typing...',
                error: 'Chatbot connection error'
            },
            es: {
                placeholder: 'Haga su pregunta aquí...',
                typing: 'El chatbot está escribiendo...',
                error: 'Error de conexión con el chatbot'
            },
            ar: {
                placeholder: 'اطرح سؤالك هنا...',
                typing: 'الروبوت يكتب...',
                error: 'خطأ في الاتصال بالروبوت'
            }
        };
    }

    /**
     * Initialise les événements
     */
    initEvents() {
        // Ouverture/fermeture
        this.toggler?.addEventListener('click', () => this.toggle());
        this.closeBtn?.addEventListener('click', () => this.close());

        // Envoi de message
        this.sendBtn?.addEventListener('click', () => this.sendMessage());
        this.input?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });

        // Reconnaissance vocale si disponible
        if ('webkitSpeechRecognition' in window) {
            this.initVoiceRecognition();
        }
    }

    /**
     * Initialise la reconnaissance vocale
     */
    initVoiceRecognition() {
        const voiceBtn = document.createElement('button');
        voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
        voiceBtn.className = 'voice-btn';
        voiceBtn.title = this.translate('voice_input');
        
        this.input.parentNode.insertBefore(voiceBtn, this.input.nextSibling);
        
        const recognition = new webkitSpeechRecognition();
        recognition.lang = `${this.lang}-${this.lang.toUpperCase()}`;
        recognition.interimResults = false;

        voiceBtn.addEventListener('click', () => {
            if (voiceBtn.classList.contains('listening')) {
                recognition.stop();
                voiceBtn.classList.remove('listening');
            } else {
                recognition.start();
                voiceBtn.classList.add('listening');
            }
        });

        recognition.onresult = (e) => {
            const transcript = e.results[0][0].transcript;
            this.input.value = transcript;
            voiceBtn.classList.remove('listening');
        };

        recognition.onerror = (e) => {
            console.error('Voice recognition error', e.error);
            voiceBtn.classList.remove('listening');
        };
    }

    /**
     * Ouvre/ferme le chatbot
     */
    toggle() {
        this.isOpen = !this.isOpen;
        this.container.style.display = this.isOpen ? 'block' : 'none';
        
        if (this.isOpen) {
            this.scrollToBottom();
            this.input.focus();
        }
    }

    /**
     * Ferme le chatbot
     */
    close() {
        this.isOpen = false;
        this.container.style.display = 'none';
    }

    /**
     * Envoie un message
     */
    sendMessage() {
        const message = this.input.value.trim();
        if (!message) return;

        this.addMessage(message, 'user');
        this.input.value = '';
        this.showTypingIndicator();
        this.scrollToBottom();

        // Envoi AJAX au serveur
        this.sendToBackend(message);
    }

    /**
     * Envoie le message au backend
     */
    sendToBackend(message) {
        fetch('controllers/ChatbotController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message: message,
                lang: this.lang
            })
        })
        .then(response => response.json())
        .then(data => {
            this.removeTypingIndicator();
            this.addMessage(data.response, 'bot');
            this.scrollToBottom();
        })
        .catch(error => {
            console.error('Error:', error);
            this.removeTypingIndicator();
            this.addMessage(this.translate('error'), 'bot');
        });
    }

    /**
     * Ajoute un message dans le chat
     */
    addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender}-message`;
        messageDiv.textContent = text;
        this.body.appendChild(messageDiv);
    }

    /**
     * Affiche l'indicateur de saisie
     */
    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-message bot-message typing-indicator';
        typingDiv.innerHTML = `
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
        `;
        typingDiv.dataset.typing = 'true';
        this.body.appendChild(typingDiv);
        this.scrollToBottom();
    }

    /**
     * Supprime l'indicateur de saisie
     */
    removeTypingIndicator() {
        const typingIndicator = this.body.querySelector('[data-typing="true"]');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    /**
     * Fait défiler vers le bas
     */
    scrollToBottom() {
        this.body.scrollTop = this.body.scrollHeight;
    }

    /**
     * Traduit une clé
     */
    translate(key) {
        return this.translations[this.lang]?.[key] || key;
    }
}

// Initialisation lorsque le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    new Chatbot();
});