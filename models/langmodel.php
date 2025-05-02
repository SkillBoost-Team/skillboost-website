<?php
class LangModel {
    private $translations = [
        'fr' => [
            'title' => 'Réclamations - SkillBoost',
            'form_title' => 'Remplissez le formulaire',
            'form_subtitle' => 'Nous traiterons votre demande dans les plus brefs délais',
            'full_name' => 'Nom Complet *',
            'email' => 'Email *',
            'subject' => 'Sujet *',
            'type' => 'Type *',
            'priority' => 'Priorité *',
            'description' => 'Description *',
            'submit' => 'Envoyer la Réclamation',
            'check_responses' => 'Vérifier les réponses',
            'search_placeholder' => 'Entrez votre numéro de réclamation',
            'search_button' => 'Rechercher',
            'claim_details' => 'Détails de la Réclamation',
            'admin_responses' => 'Réponses de l\'administration',
            'no_response' => 'Aucune réponse n\'a encore été apportée à cette réclamation.',
            'no_claim' => 'Aucune réclamation trouvée avec cet ID.',
            'claim_number' => 'Réclamation #',
            'date' => 'Date',
            'status' => 'Statut',
            'response_date' => 'Réponse du',
            'chatbot_title' => 'Assistant Virtuel',
            'chatbot_placeholder' => 'Posez votre question ici...',
            'faq_title' => 'Questions Fréquentes',
            'success_message' => 'Votre réclamation a été envoyée avec succès! Votre numéro de réclamation est: #',
            'faq_questions' => [
                'Comment suivre ma réclamation?' => 'Vous pouvez suivre votre réclamation en entrant son numéro dans la section "Vérifier les réponses".',
                'Quel est le délai de traitement?' => 'Nous traitons les réclamations sous 2-5 jours ouvrables selon la priorité.',
                'Puis-je modifier ma réclamation?' => 'Une fois soumise, la réclamation ne peut plus être modifiée. Contactez-nous pour toute modification.'
            ],
            'complaint_page_title' => 'Déposer une Réclamation',
            'complaint_page_description' => 'Service de gestion des réclamations'
        ],
        'en' => [
            'title' => 'Complaints - SkillBoost',
            'form_title' => 'Fill out the form',
            'form_subtitle' => 'We will process your request as soon as possible',
            'full_name' => 'Full Name *',
            'email' => 'Email *',
            'subject' => 'Subject *',
            'type' => 'Type *',
            'priority' => 'Priority *',
            'description' => 'Description *',
            'submit' => 'Submit Complaint',
            'check_responses' => 'Check Responses',
            'search_placeholder' => 'Enter your complaint number',
            'search_button' => 'Search',
            'claim_details' => 'Complaint Details',
            'admin_responses' => 'Administration Responses',
            'no_response' => 'No response has been provided to this complaint yet.',
            'no_claim' => 'No complaint found with this ID.',
            'claim_number' => 'Complaint #',
            'date' => 'Date',
            'status' => 'Status',
            'response_date' => 'Response from',
            'chatbot_title' => 'Virtual Assistant',
            'chatbot_placeholder' => 'Ask your question here...',
            'faq_title' => 'Frequently Asked Questions',
            'success_message' => 'Your complaint has been submitted successfully! Your complaint number is: #',
            'faq_questions' => [
                'How to track my complaint?' => 'You can track your complaint by entering its number in the "Check Responses" section.',
                'What is the processing time?' => 'We process complaints within 2-5 business days depending on priority.',
                'Can I modify my complaint?' => 'Once submitted, the complaint cannot be modified. Contact us for any changes.'
            ],
            'complaint_page_title' => 'File a Complaint',
            'complaint_page_description' => 'Complaint management service'
        ],
        'es' => [
            'title' => 'Reclamaciones - SkillBoost',
            'form_title' => 'Complete el formulario',
            'form_subtitle' => 'Procesaremos su solicitud lo antes posible',
            'full_name' => 'Nombre Completo *',
            'email' => 'Correo Electrónico *',
            'subject' => 'Asunto *',
            'type' => 'Tipo *',
            'priority' => 'Prioridad *',
            'description' => 'Descripción *',
            'submit' => 'Enviar Reclamación',
            'check_responses' => 'Verificar Respuestas',
            'search_placeholder' => 'Ingrese su número de reclamación',
            'search_button' => 'Buscar',
            'claim_details' => 'Detalles de la Reclamación',
            'admin_responses' => 'Respuestas de la Administración',
            'no_response' => 'Aún no se ha proporcionado respuesta a esta reclamación.',
            'no_claim' => 'No se encontró ninguna reclamación con este ID.',
            'claim_number' => 'Reclamación #',
            'date' => 'Fecha',
            'status' => 'Estado',
            'response_date' => 'Respuesta del',
            'chatbot_title' => 'Asistente Virtual',
            'chatbot_placeholder' => 'Haga su pregunta aquí...',
            'faq_title' => 'Preguntas Frecuentes',
            'success_message' => 'Su reclamación ha sido enviada con éxito! Su número de reclamación es: #',
            'faq_questions' => [
                '¿Cómo seguir mi reclamación?' => 'Puede seguir su reclamación ingresando su número en la sección "Verificar Respuestas".',
                '¿Cuál es el tiempo de procesamiento?' => 'Procesamos las reclamaciones en 2-5 días hábiles según la prioridad.',
                '¿Puedo modificar mi reclamación?' => 'Una vez enviada, la reclamación no puede modificarse. Contáctenos para cualquier cambio.'
            ],
            'complaint_page_title' => 'Presentar una Reclamación',
            'complaint_page_description' => 'Servicio de gestión de reclamaciones'
        ],
        'ar' => [
            'title' => 'شكاوى - SkillBoost',
            'form_title' => 'املأ النموذج',
            'form_subtitle' => 'سنعالج طلبك في أقرب وقت ممكن',
            'full_name' => 'الاسم الكامل *',
            'email' => 'البريد الإلكتروني *',
            'subject' => 'الموضوع *',
            'type' => 'النوع *',
            'priority' => 'الأولوية *',
            'description' => 'الوصف *',
            'submit' => 'إرسال الشكوى',
            'check_responses' => 'التحقق من الردود',
            'search_placeholder' => 'أدخل رقم الشكوى الخاص بك',
            'search_button' => 'بحث',
            'claim_details' => 'تفاصيل الشكوى',
            'admin_responses' => 'ردود الإدارة',
            'no_response' => 'لم يتم تقديم أي رد على هذه الشكوى حتى الآن.',
            'no_claim' => 'لم يتم العثور على أي شكوى بهذا الرقم.',
            'claim_number' => 'شكوى #',
            'date' => 'التاريخ',
            'status' => 'الحالة',
            'response_date' => 'رد بتاريخ',
            'chatbot_title' => 'المساعد الافتراضي',
            'chatbot_placeholder' => 'اطرح سؤالك هنا...',
            'faq_title' => 'أسئلة متكررة',
            'success_message' => 'تم إرسال شكواك بنجاح! رقم شكواك هو: #',
            'faq_questions' => [
                'كيف أتابع شكواي؟' => 'يمكنك متابعة شكواك بإدخال رقمها في قسم "التحقق من الردود".',
                'ما هي مدة المعالجة؟' => 'نحن نعالج الشكاوى خلال 2-5 أيام عمل حسب الأولوية.',
                'هل يمكنني تعديل شكواي؟' => 'بعد الإرسال، لا يمكن تعديل الشكوى. اتصل بنا لأي تغييرات.'
            ],
            'complaint_page_title' => 'تقديم شكوى',
            'complaint_page_description' => 'خدمة إدارة الشكاوى'
        ]
    ];

    /**
     * Récupère les traductions pour une langue spécifique
     * @param string $lang Code de langue (fr, en, es, ar)
     * @return array Tableau des traductions
     */
    public function getTranslations($lang) {
        return $this->translations[$lang] ?? $this->translations['fr'];
    }

    /**
     * Vérifie si une langue est supportée
     * @param string $lang Code de langue
     * @return bool True si la langue est supportée
     */
    public function validateLanguage($lang) {
        return array_key_exists($lang, $this->translations);
    }

    /**
     * Récupère la liste des langues disponibles
     * @return array Tableau des codes de langue
     */
    public function getAvailableLanguages() {
        return array_keys($this->translations);
    }

    /**
     * Traduit une clé spécifique
     * @param string $key Clé de traduction
     * @param string $lang Langue cible
     * @return string Texte traduit ou clé si non trouvé
     */
    public function translate($key, $lang) {
        $translations = $this->getTranslations($lang);
        return $translations[$key] ?? $key;
    }

    /**
     * Récupère les questions/réponses FAQ pour une langue
     * @param string $lang Code de langue
     * @return array Tableau des questions/réponses
     */
    public function getFaqQuestions($lang) {
        $translations = $this->getTranslations($lang);
        return $translations['faq_questions'] ?? [];
    }
}
?>