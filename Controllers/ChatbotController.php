<?php
require_once __DIR__.'/../models/ChatbotModel.php';
require_once __DIR__.'/../models/LangModel.php';

class ChatbotController {
    private $chatbotModel;
    private $langModel;

    public function __construct() {
        $this->chatbotModel = new ChatbotModel();
        $this->langModel = new LangModel();
    }

    /**
     * Traite la requête du chatbot
     */
    public function handleRequest() {
        header('Content-Type: application/json');
        
        try {
            // Récupère les données JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validation des données
            if (empty($input['message']) || empty($input['lang'])) {
                throw new Exception('Invalid request data');
            }

            $message = trim($input['message']);
            $lang = $this->langModel->validateLanguage($input['lang']) ? $input['lang'] : 'fr';

            // Traitement du message
            $response = $this->generateResponse($message, $lang);

            // Envoi de la réponse
            echo json_encode([
                'status' => 'success',
                'response' => $response,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Génère une réponse intelligente
     */
    private function generateResponse($message, $lang) {
        // 1. Vérifie les questions fréquentes
        $faqResponse = $this->checkFaq($message, $lang);
        if ($faqResponse) return $faqResponse;

        // 2. Vérifie les intentions spécifiques
        $intentResponse = $this->checkIntent($message, $lang);
        if ($intentResponse) return $intentResponse;

        // 3. Réponse par défaut
        return $this->getDefaultResponse($lang);
    }

    /**
     * Vérifie les questions fréquentes
     */
    private function checkFaq($message, $lang) {
        $faq = $this->langModel->getFaqQuestions($lang);
        $messageLower = mb_strtolower($message);

        foreach ($faq as $question => $answer) {
            if (strpos($messageLower, mb_strtolower($question)) !== false) {
                return $answer;
            }
        }

        return null;
    }

    /**
     * Vérifie les intentions spécifiques
     */
    private function checkIntent($message, $lang) {
        $intents = [
            'reclamation' => ['réclamation', 'complaint', 'reclamación', 'شكوى'],
            'status' => ['statut', 'status', 'estado', 'حالة'],
            'urgence' => ['urgent', 'urgence', 'urgente', 'عاجل']
        ];

        $messageLower = mb_strtolower($message);

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($messageLower, $keyword) !== false) {
                    return $this->getIntentResponse($intent, $lang);
                }
            }
        }

        return null;
    }

    /**
     * Réponse pour une intention spécifique
     */
    private function getIntentResponse($intent, $lang) {
        $responses = [
            'reclamation' => [
                'fr' => 'Pour créer une réclamation, veuillez remplir le formulaire dédié.',
                'en' => 'To create a complaint, please fill out the dedicated form.',
                'es' => 'Para crear una reclamación, complete el formulario dedicado.',
                'ar' => 'لإنشاء شكوى ، يرجى ملء النموذج المخصص.'
            ],
            'status' => [
                'fr' => 'Vous pouvez vérifier le statut de votre réclamation dans la section "Vérifier les réponses".',
                'en' => 'You can check your complaint status in the "Check Responses" section.',
                'es' => 'Puede verificar el estado de su reclamación en la sección "Verificar respuestas".',
                'ar' => 'يمكنك التحقق من حالة شكواك في قسم "التحقق من الردود".'
            ],
            'urgence' => [
                'fr' => 'Pour les demandes urgentes, veuillez nous contacter par WhatsApp au +216 90 044 054.',
                'en' => 'For urgent requests, please contact us on WhatsApp at +216 90 044 054.',
                'es' => 'Para solicitudes urgentes, contáctenos por WhatsApp al +216 90 044 054.',
                'ar' => 'للطلبات العاجلة ، يرجى الاتصال بنا على واتساب على +216 90 044 054.'
            ]
        ];

        return $responses[$intent][$lang] ?? $this->getDefaultResponse($lang);
    }

    /**
     * Réponse par défaut
     */
    private function getDefaultResponse($lang) {
        $defaults = [
            'fr' => 'Je n\'ai pas compris votre demande. Pouvez-vous reformuler ?',
            'en' => 'I didn\'t understand your request. Could you rephrase it?',
            'es' => 'No entendí tu solicitud. ¿Podrías reformularlo?',
            'ar' => 'لم أفهم طلبك. هل يمكنك إعادة صياغته؟'
        ];

        return $defaults[$lang];
    }
}

// Point d'entrée
$controller = new ChatbotController();
$controller->handleRequest();
?>