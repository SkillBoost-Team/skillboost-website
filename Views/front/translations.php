<?php
function translate($key, $lang = 'fr') {
    $translations = [
        'fr' => [
            'title' => 'Déposer une Réclamation',
            'fullname_label' => 'Nom Complet *',
            'email_label' => 'Email *',
            'subject_label' => 'Sujet *',
            'type_label' => 'Type *',
            'priority_label' => 'Priorité *',
            'description_label' => 'Description *',
            'submit_button' => 'Envoyer la Réclamation',
            'search_placeholder' => 'Entrez votre numéro de réclamation',
            'search_button' => 'Rechercher',
            'no_reclamation_found' => 'Aucune réclamation trouvée avec cet ID.',
            'reclamation_details' => 'Détails de la Réclamation',
            'responses_title' => 'Réponses de l\'administration',
            'no_response_yet' => 'Aucune réponse n\'a encore été apportée à cette réclamation.',
            'complaint_page' => 'Page de Réclamations',
            'chatbot_title' => 'Chatbot',
            'chatbot_welcome' => 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?',
            'chatbot_question1' => 'Comment puis-je suivre mon réclamation ?',
            'chatbot_question2' => 'Quels sont les types de réclamations acceptés ?',
            'chatbot_question3' => 'Combien de temps prend le traitement d\'une réclamation ?',
            'chatbot_send' => 'Envoyer',
        ],
        'en' => [
            'title' => 'Submit a Complaint',
            'fullname_label' => 'Full Name *',
            'email_label' => 'Email *',
            'subject_label' => 'Subject *',
            'type_label' => 'Type *',
            'priority_label' => 'Priority *',
            'description_label' => 'Description *',
            'submit_button' => 'Submit Complaint',
            'search_placeholder' => 'Enter your complaint number',
            'search_button' => 'Search',
            'no_reclamation_found' => 'No complaint found with this ID.',
            'reclamation_details' => 'Complaint Details',
            'responses_title' => 'Administration Responses',
            'no_response_yet' => 'No response has yet been provided for this complaint.',
            'complaint_page' => 'Complaint Page',
            'chatbot_title' => 'Chatbot',
            'chatbot_welcome' => 'Hello! How can I assist you today?',
            'chatbot_question1' => 'How can I track my complaint?',
            'chatbot_question2' => 'What types of complaints are accepted?',
            'chatbot_question3' => 'How long does it take to process a complaint?',
            'chatbot_send' => 'Send',
        ],
    ];

    return $translations[$lang][$key] ?? $key;
}
?>