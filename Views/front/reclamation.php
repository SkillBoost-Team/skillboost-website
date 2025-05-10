<?php
// Initialize $message and $alert_class with default values
$message = '';
$alert_class = '';
$reclamation_data = null;
$reponses = [];

// Connexion à la base de données
try {
    $db = new PDO('mysql:host=localhost;dbname=skillboost', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Gestion de la langue
$lang = 'fr'; // Par défaut
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en', 'es', 'ar'])) {
    $lang = $_GET['lang'];
    setcookie('lang', $lang, time() + (86400 * 30), "/"); // Cookie valide 30 jours
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
}

// Textes multilingues
$translations = [
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
        'search_placeholder' => 'Entrez votre nom complet',
        'search_button' => 'Rechercher',
        'claim_details' => 'Détails de la Réclamation',
        'admin_responses' => 'Réponses de l\'administration',
        'no_response' => 'Aucune réponse n\'a encore été apportée à cette réclamation.',
        'no_claim' => 'Aucune réclamation trouvée avec ce nom.',
        'claim_number' => 'Réclamation #',
        'date' => 'Date',
        'status' => 'Statut',
        'response_date' => 'Réponse du',
        'chatbot_title' => 'Assistant Virtuel',
        'chatbot_placeholder' => 'Posez votre question ici...',
        'faq_title' => 'Questions Fréquentes',
        'success_message' => 'Votre réclamation a été envoyée avec succès! Votre numéro de réclamation est: #',
        'faq_questions' => [
            'Comment suivre ma réclamation?' => 'Vous pouvez suivre votre réclamation en entrant votre nom complet dans la section "Vérifier les réponses".',
            'Quel est le délai de traitement?' => 'Nous traitons les réclamations sous 2-5 jours ouvrables selon la priorité.',
            'Puis-je modifier ma réclamation?' => 'Une fois soumise, la réclamation ne peut plus être modifiée. Contactez-nous pour toute modification.'
        ],
        'complaint_page_title' => 'Déposer une Réclamation',
        'complaint_page_description' => 'Service de gestion des réclamations',
        'voice_input' => 'Saisie vocale',
        'start_voice' => 'Commencer la saisie vocale',
        'stop_voice' => 'Arrêter la saisie vocale',
        'voice_not_supported' => 'La saisie vocale n\'est pas supportée par votre navigateur',
        'like' => 'J\'aime',
        'dislike' => 'Je n\'aime pas',
        'points_earned' => 'Points gagnés',
        'thank_you_feedback' => 'Merci pour votre feedback!',
        'reward_message' => 'Vous avez gagné 5 points pour votre feedback!'
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
        'search_placeholder' => 'Enter your full name',
        'search_button' => 'Search',
        'claim_details' => 'Complaint Details',
        'admin_responses' => 'Administration Responses',
        'no_response' => 'No response has been provided to this complaint yet.',
        'no_claim' => 'No complaint found with this name.',
        'claim_number' => 'Complaint #',
        'date' => 'Date',
        'status' => 'Status',
        'response_date' => 'Response from',
        'chatbot_title' => 'Virtual Assistant',
        'chatbot_placeholder' => 'Ask your question here...',
        'faq_title' => 'Frequently Asked Questions',
        'success_message' => 'Your complaint has been submitted successfully! Your complaint number is: #',
        'faq_questions' => [
            'How to track my complaint?' => 'You can track your complaint by entering your full name in the "Check Responses" section.',
            'What is the processing time?' => 'We process complaints within 2-5 business days depending on priority.',
            'Can I modify my complaint?' => 'Once submitted, the complaint cannot be modified. Contact us for any changes.'
        ],
        'complaint_page_title' => 'File a Complaint',
        'complaint_page_description' => 'Complaint management service',
        'voice_input' => 'Voice input',
        'start_voice' => 'Start voice input',
        'stop_voice' => 'Stop voice input',
        'voice_not_supported' => 'Voice input not supported by your browser',
        'like' => 'Like',
        'dislike' => 'Dislike',
        'points_earned' => 'Points earned',
        'thank_you_feedback' => 'Thank you for your feedback!',
        'reward_message' => 'You earned 5 points for your feedback!'
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
        'search_placeholder' => 'Ingrese su nombre completo',
        'search_button' => 'Buscar',
        'claim_details' => 'Detalles de la Reclamación',
        'admin_responses' => 'Respuestas de la Administración',
        'no_response' => 'Aún no se ha proporcionado respuesta a esta reclamación.',
        'no_claim' => 'No se encontró ninguna reclamación con este nombre.',
        'claim_number' => 'Reclamación #',
        'date' => 'Fecha',
        'status' => 'Estado',
        'response_date' => 'Respuesta del',
        'chatbot_title' => 'Asistente Virtual',
        'chatbot_placeholder' => 'Haga su pregunta aquí...',
        'faq_title' => 'Preguntas Frecuentes',
        'success_message' => 'Su reclamación ha sido enviada con éxito! Su número de reclamación es: #',
        'faq_questions' => [
            '¿Cómo seguir mi reclamación?' => 'Puede seguir su reclamación ingresando su nombre completo en la sección "Verificar Respuestas".',
            '¿Cuál es el tiempo de procesamiento?' => 'Procesamos las reclamaciones en 2-5 días hábiles según la prioridad.',
            '¿Puedo modificar mi reclamación?' => 'Una vez enviada, la reclamación no puede modificarse. Contáctenos para cualquier cambio.'
        ],
        'complaint_page_title' => 'Presentar una Reclamación',
        'complaint_page_description' => 'Servicio de gestión de reclamaciones',
        'voice_input' => 'Entrada de voz',
        'start_voice' => 'Iniciar entrada de voz',
        'stop_voice' => 'Detener entrada de voz',
        'voice_not_supported' => 'Entrada de voz no compatible con su navegador',
        'like' => 'Me gusta',
        'dislike' => 'No me gusta',
        'points_earned' => 'Puntos ganados',
        'thank_you_feedback' => '¡Gracias por tus comentarios!',
        'reward_message' => '¡Has ganado 5 puntos por tus comentarios!'
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
        'search_placeholder' => 'أدخل اسمك الكامل',
        'search_button' => 'بحث',
        'claim_details' => 'تفاصيل الشكوى',
        'admin_responses' => 'ردود الإدارة',
        'no_response' => 'لم يتم تقديم أي رد على هذه الشكوى حتى الآن.',
        'no_claim' => 'لم يتم العثور على أي شكوى بهذا الاسم.',
        'claim_number' => 'شكوى #',
        'date' => 'التاريخ',
        'status' => 'الحالة',
        'response_date' => 'رد بتاريخ',
        'chatbot_title' => 'المساعد الافتراضي',
        'chatbot_placeholder' => 'اطرح سؤالك هنا...',
        'faq_title' => 'أسئلة متكررة',
        'success_message' => 'تم إرسال شكواك بنجاح! رقم شكواك هو: #',
        'faq_questions' => [
            'كيف أتابع شكواي؟' => 'يمكنك متابعة شكواك بإدخال اسمك الكامل في قسم "التحقق من الردود".',
            'ما هي مدة المعالجة؟' => 'نحن نعالج الشكاوى خلال 2-5 أيام عمل حسب الأولوية.',
            'هل يمكنني تعديل شكواي؟' => 'بعد الإرسال، لا يمكن تعديل الشكوى. اتصل بنا لأي تغييرات.'
        ],
        'complaint_page_title' => 'تقديم شكوى',
        'complaint_page_description' => 'خدمة إدارة الشكاوى',
        'voice_input' => 'إدخال صوتي',
        'start_voice' => 'بدء الإدخال الصوتي',
        'stop_voice' => 'إيقاف الإدخال الصوتي',
        'voice_not_supported' => 'الإدخال الصوتي غير مدعوم من متصفحك',
        'like' => 'أعجبني',
        'dislike' => 'لم يعجبني',
        'points_earned' => 'النقاط المكتسبة',
        'thank_you_feedback' => 'شكرًا على ملاحظاتك!',
        'reward_message' => 'لقد ربحت 5 نقاط لملاحظاتك!'
    ]
];

$t = $translations[$lang];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestion des likes/dislikes
    if (isset($_POST['feedback_type'])) {
        try {
            $reponse_id = intval($_POST['reponse_id']);
            $feedback_type = $_POST['feedback_type'] === 'like' ? 1 : 0;
            $client_ip = $_SERVER['REMOTE_ADDR'];
            
            // Vérifier si l'utilisateur a déjà voté
            $stmt = $db->prepare("SELECT * FROM reponses_feedback WHERE reponse_id = ? AND client_ip = ?");
            $stmt->execute([$reponse_id, $client_ip]);
            
            if ($stmt->rowCount() === 0) {
                // Enregistrer le vote
                $stmt = $db->prepare("INSERT INTO reponses_feedback (reponse_id, feedback_type, client_ip) VALUES (?, ?, ?)");
                $stmt->execute([$reponse_id, $feedback_type, $client_ip]);
                
                // Mettre à jour le compteur de votes
                $field = $feedback_type ? 'likes' : 'dislikes';
                $stmt = $db->prepare("UPDATE reponses_reclamations SET $field = $field + 1 WHERE id = ?");
                $stmt->execute([$reponse_id]);
                
                // Attribuer des points si like
                if ($feedback_type) {
                    $message = $t['reward_message'];
                    $alert_class = "success";
                } else {
                    $message = $t['thank_you_feedback'];
                    $alert_class = "info";
                }
            } else {
                $message = $t['thank_you_feedback'];
                $alert_class = "info";
            }
        } catch(PDOException $e) {
            $message = "Erreur technique: " . $e->getMessage();
            $alert_class = "danger";
        }
    } elseif (isset($_POST['full_name'])) {
        // Nettoyage des données
        $data = [
            'full_name' => htmlspecialchars(trim($_POST['full_name'])),
            'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
            'subject' => htmlspecialchars(trim($_POST['subject'])),
            'type' => htmlspecialchars($_POST['type']),
            'priority' => htmlspecialchars($_POST['priority']),
            'description' => htmlspecialchars(trim($_POST['description'])),
            'status' => 'Nouveau',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Validation des données
        $errors = [];
        if (empty($data['full_name'])) $errors[] = $t['full_name'] . ' est requis';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (empty($data['subject'])) $errors[] = $t['subject'] . ' est requis';
        if (empty($data['type'])) $errors[] = $t['type'] . ' est requis';
        if (empty($data['priority'])) $errors[] = $t['priority'] . ' est requise';
        if (strlen($data['description']) < 20) $errors[] = $t['description'] . ' doit faire au moins 20 caractères';

        if (empty($errors)) {
            try {
                $stmt = $db->prepare("INSERT INTO reclamations (full_name, email, subject, type, priority, description, status, created_at) 
                                    VALUES (:full_name, :email, :subject, :type, :priority, :description, :status, :created_at)");
                
                if ($stmt->execute($data)) {
                    $reclamation_id = $db->lastInsertId();
                    $message = $t['success_message'] . $reclamation_id;
                    $alert_class = "success";
                    $_POST = []; // Réinitialisation
                }
            } catch(PDOException $e) {
                $message = "Erreur technique: " . $e->getMessage();
                $alert_class = "danger";
            }
        } else {
            $message = implode("<br>", $errors);
            $alert_class = "warning";
        }
    }
}

// Récupération des réclamations par nom
if (isset($_GET['name'])) {
    $full_name = trim($_GET['name']);
    
    if (!empty($full_name)) {
        try {
            // Récupérer les réclamations par nom
            $stmt = $db->prepare("SELECT * FROM reclamations WHERE full_name LIKE :full_name ORDER BY created_at DESC");
            $stmt->execute(['full_name' => '%'.$full_name.'%']);
            $reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($reclamations) {
                $reclamation_data = ['reclamations' => $reclamations];
                
                // Pour chaque réclamation, récupérer les réponses
                foreach ($reclamation_data['reclamations'] as &$reclamation) {
                    $stmt = $db->prepare("SELECT * FROM reponses_reclamations WHERE reclamation_id = :id ORDER BY date_reponse DESC");
                    $stmt->execute(['id' => $reclamation['id']]);
                    $reclamation['reponses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } else {
                $message = $t['no_claim'];
                $alert_class = "warning";
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de la récupération: " . $e->getMessage();
            $alert_class = "danger";
        }
    } else {
        $message = "Nom invalide";
        $alert_class = "danger";
    }
}

// Fonctions utilitaires
function getStatusClass($status) {
    $classes = [
        'Nouveau' => 'badge bg-primary',
        'En cours' => 'badge bg-warning text-dark',
        'Résolu' => 'badge bg-success',
        'Rejeté' => 'badge bg-danger'
    ];
    return $classes[$status] ?? 'badge bg-secondary';
}

function getStatusText($status) { return $status; }
function getTypeText($type) { return $type; }
function getPriorityText($priority) { return $priority; }
function formatDate($dateString) { return date('d/m/Y H:i', strtotime($dateString)); }
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <title><?= $t['title'] ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    
    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <!-- Chatbot CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chatbot@1.0.0/dist/chatbot.min.css">
    
    <style>
        /* Styles personnalisés */
        .reclamation-form {
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .reclamation-form:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Remplacer le style existant pour .voice-btn */
.voice-btn {
    position: absolute;
    right: 15px;  /* Ajusté de 10px à 15px */
    top: 50%;
    transform: translateY(-50%);
    background: #f8f9fa;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #061429;
    z-index: 2;
    padding: 0;
}

select + .voice-btn {
    right: 30px; /* Ajusté de 25px à 30px */
}
        
        .voice-btn.listening {
            color: #e74c3c;
            animation: pulse 1.5s infinite;
        }
        .voice-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }/* Ajoutez ceci dans votre section <style> */
.listening {
    background-color: #ffebee !important;
    box-shadow: 0 0 10px #f44336 !important;
}

.voice-input-wrapper {
    position: relative;
}

.voice-input-wrapper input,
.voice-input-wrapper select,
.voice-input-wrapper textarea {
    padding-right: 40px !important;
}

        /* Icônes pour la page de réclamation */
        .complaint-icon {
            font-size: 2.5rem;
            color: #061429;
            margin-bottom: 1rem;
        }
        .complaint-feature {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .complaint-feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .complaint-feature h4 {
            color: #061429;
            margin-top: 10px;
        }
        
        /* Chatbot styles */
        .chatbot-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            width: 350px;
            max-height: 500px;
            display: none;
        }
        .chatbot-header {
            background: #061429;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chatbot-body {
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
        }
        .chatbot-message {
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 18px;
            max-width: 80%;
        }
        .user-message {
            background: #e3f2fd;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }
        .bot-message {
            background: #f1f1f1;
            margin-right: auto;
            border-bottom-left-radius: 5px;
        }
        .chatbot-input {
            display: flex;
            padding: 10px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }
        .chatbot-input input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
        }
        .chatbot-input button {
            margin-left: 10px;
            background: #061429;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            cursor: pointer;
        }
        .chatbot-toggler {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            background: #061429;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1001;
        }
        .chatbot-toggler i {
            font-size: 1.5rem;
        }
        
        /* Langue selector */
        .lang-selector {
            position: fixed;
            top: 100px;
            left: 20px;
            z-index: 1000;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 5px;
        }
        .lang-selector a {
            display: block;
            padding: 5px 10px;
            text-decoration: none;
            color: #333;
        }
        .lang-selector a:hover {
            background: #f1f1f1;
        }
        .lang-selector a.active {
            background: #061429;
            color: white;
        }
        
        /* FAQ Section */
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        .faq-question {
            padding: 15px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-answer {
            padding: 15px;
            background: white;
            display: none;
        }
        .faq-question i {
            transition: transform 0.3s;
        }
        .faq-question.active i {
            transform: rotate(180deg);
        }
        
        /* WhatsApp Float Button */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .whatsapp-float:hover {
            background-color: #128C7E;
            color: #FFF;
            transform: scale(1.1);
        }
        
        .whatsapp-float-icon {
            margin-top: 5px;
        }
        
        /* Styles pour les boutons de feedback */
        .feedback-buttons {
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        /* Points display */
        .points-display {
            position: fixed;
            top: 150px;
            right: 20px;
            background: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .points-display i {
            color: gold;
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
                font-size: 25px;
            }
            
            .points-display {
                top: 120px;
                right: 10px;
                font-size: 14px;
            }
        }
        
        /* Navbar amélioré */
        .navbar-nav .nav-item {
            position: relative;
            margin: 0 5px;
        }
        .navbar-nav .nav-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #fff;
            transition: width 0.3s;
        }
        .navbar-nav .nav-item:hover::after {
            width: 100%;
        }
        .navbar-nav .nav-link {
            display: flex;
            align-items: center;
        }
        .navbar-nav .nav-link i {
            margin-right: 8px;
        }
        
        /* RTL support for Arabic */
        [dir="rtl"] .text-end {
            text-align: left !important;
        }
        [dir="rtl"] .me-2 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }
        [dir="rtl"] .ms-auto {
            margin-right: auto !important;
            margin-left: 0 !important;
        }
        [dir="rtl"] .float-end {
            float: left !important;
        }
    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->
    
    <!-- Points Display -->
    <div class="points-display">
        <i class="fas fa-coins"></i> 
        <span id="user-points">0</span> <?= $t['points_earned'] ?>
    </div>
    
    <!-- Language Selector -->
    <div class="lang-selector">
        <a href="?lang=fr" class="<?= $lang === 'fr' ? 'active' : '' ?>">Français</a>
        <a href="?lang=en" class="<?= $lang === 'en' ? 'active' : '' ?>">English</a>
        <a href="?lang=es" class="<?= $lang === 'es' ? 'active' : '' ?>">Español</a>
        <a href="?lang=ar" class="<?= $lang === 'ar' ? 'active' : '' ?>">العربية</a>
    </div>

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Bloc E, Esprit, Cite La Gazelle</small>
                    <small class="me-3 text-light"><i class="fab fa-whatsapp me-2"></i><a href="https://wa.me/21690044054" class="text-light" target="_blank" style="text-decoration:none;">+216 90 044 054</a></small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>SkillBoost@gmail.com</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->
    
    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>SkillBoost</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link">Accueil</a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="logout.php" class="nav-item nav-link">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-item nav-link">Connexion</a>
                    <?php endif; ?>
                    <a href="#" class="nav-item nav-link">Projets</a>
                    <a href="Formations.php" class="nav-item nav-link">Formations</a>
                    <a href="evenements.php" class="nav-item nav-link">Événements</a>
                    <a href="gestionInvestissement.php" class="nav-item nav-link">Investissements</a>
                    <a href="reclamation.php" class="nav-item nav-link active">Réclamations</a>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar End -->
    
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown"><?= $t['complaint_page_title'] ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php"><?= $lang === 'en' ? 'Home' : ($lang === 'es' ? 'Inicio' : ($lang === 'ar' ? 'الرئيسية' : 'Accueil')) ?></a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page"><?= $lang === 'en' ? 'Complaints' : ($lang === 'es' ? 'Reclamaciones' : ($lang === 'ar' ? 'الشكاوى' : 'Réclamations')) ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    
    <!-- Features Section -->
    <div class="container-xxl py-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-12 text-center mb-4">
                    <h2 class="mb-4"><?= $t['complaint_page_description'] ?></h2>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="complaint-feature">
                                <div class="complaint-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <h4><?= $lang === 'en' ? 'Report a Problem' : ($lang === 'es' ? 'Reportar Problema' : ($lang === 'ar' ? 'الإبلاغ عن مشكلة' : 'Signaler un Problème')) ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="complaint-feature">
                                <div class="complaint-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h4><?= $lang === 'en' ? 'Track Status' : ($lang === 'es' ? 'Seguimiento' : ($lang === 'ar' ? 'تتبع الحالة' : 'Suivi Statut')) ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="complaint-feature">
                                <div class="complaint-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h4><?= $lang === 'en' ? 'Get Answers' : ($lang === 'es' ? 'Obtener Respuestas' : ($lang === 'ar' ? 'الحصول على إجابات' : 'Obtenir Réponses')) ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="complaint-feature">
                                <div class="complaint-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <h4><?= $lang === 'en' ? '24/7 Support' : ($lang === 'es' ? 'Soporte 24/7' : ($lang === 'ar' ? 'الدعم على مدار الساعة' : 'Support 24/7')) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Formulaire de Réclamation Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.1s">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $alert_class ?> alert-dismissible fade show mb-4">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="reclamation-form">
                        <h2 class="mb-4"><?= $t['form_title'] ?></h2>
                        <p class="mb-5"><?= $t['form_subtitle'] ?></p>
                        
                        <form id="reclamationForm" method="POST" novalidate>
                            <!-- Nom Complet -->
                            <div class="mb-4">
                                <label for="full_name" class="form-label"><?= $t['full_name'] ?></label>
                                <div class="voice-input-wrapper">
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                                    <button type="button" class="voice-btn" data-field="full_name" title="<?= $t['start_voice'] ?>">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                                <div class="error-message" id="full_name_error"></div>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label"><?= $t['email'] ?></label>
                                <div class="voice-input-wrapper">
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    <button type="button" class="voice-btn" data-field="email" title="<?= $t['start_voice'] ?>">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                                <div class="error-message" id="email_error"></div>
                            </div>
                            
                            <!-- Sujet -->
                            <div class="mb-4">
                                <label for="subject" class="form-label"><?= $t['subject'] ?></label>
                                <div class="voice-input-wrapper">
                                    <input type="text" class="form-control" id="subject" name="subject"
                                           value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                                    <button type="button" class="voice-btn" data-field="subject" title="<?= $t['start_voice'] ?>">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                                <div class="error-message" id="subject_error"></div>
                            </div>
                            
                            <!-- Type et Priorité en ligne -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="type" class="form-label"><?= $t['type'] ?></label>
                                    <div class="voice-input-wrapper">
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="" disabled selected><?= $lang === 'en' ? 'Choose a type' : ($lang === 'es' ? 'Elija un tipo' : ($lang === 'ar' ? 'اختر نوعًا' : 'Choisissez un type')) ?></option>
                                            <option value="Technique" <?= ($_POST['type'] ?? '') === 'Technique' ? 'selected' : '' ?>><?= $lang === 'en' ? 'Technical' : ($lang === 'es' ? 'Técnico' : ($lang === 'ar' ? 'تقني' : 'Technique')) ?></option>
                                            <option value="Service" <?= ($_POST['type'] ?? '') === 'Service' ? 'selected' : '' ?>><?= $lang === 'en' ? 'Service' : ($lang === 'es' ? 'Servicio' : ($lang === 'ar' ? 'خدمة' : 'Service')) ?></option>
                                            <option value="Facturation" <?= ($_POST['type'] ?? '') === 'Facturation' ? 'selected' : '' ?>><?= $lang === 'en' ? 'Billing' : ($lang === 'es' ? 'Facturación' : ($lang === 'ar' ? 'فوترة' : 'Facturation')) ?></option>
                                            <option value="Autre" <?= ($_POST['type'] ?? '') === 'Autre' ? 'selected' : '' ?>><?= $lang === 'en' ? 'Other' : ($lang === 'es' ? 'Otro' : ($lang === 'ar' ? 'آخر' : 'Autre')) ?></option>
                                        </select>
                                        <button type="button" class="voice-btn" data-field="type" title="<?= $t['start_voice'] ?>">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    <div class="error-message" id="type_error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="priority" class="form-label"><?= $t['priority'] ?></label>
                                    <div class="voice-input-wrapper">
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="" disabled selected><?= $lang === 'en' ? 'Choose priority' : ($lang === 'es' ? 'Elija prioridad' : ($lang === 'ar' ? 'اختر الأولوية' : 'Choisissez une priorité')) ?></option>
                                            <option value="Haute" <?= ($_POST['priority'] ?? '') === 'Haute' ? 'selected' : '' ?>><?= $lang === 'en' ? 'High' : ($lang === 'es' ? 'Alta' : ($lang === 'ar' ? 'عالي' : 'Haute')) ?></option>
                                            <option value="Moyenne" <?= ($_POST['priority'] ?? '') === 'Moyenne' ? 'selected' : '' ?>><?= $lang === 'en' ? 'Medium' : ($lang === 'es' ? 'Media' : ($lang === 'ar' ? 'متوسط' : 'Moyenne')) ?></option>
                                            <option value="Basse" <?= ($_POST['priority'] ?? '') === 'Basse' ? 'selected' : '' ?>><?= $lang === 'en' ? 'Low' : ($lang === 'es' ? 'Baja' : ($lang === 'ar' ? 'منخفض' : 'Basse')) ?></option>
                                        </select>
                                        <button type="button" class="voice-btn" data-field="priority" title="<?= $t['start_voice'] ?>">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    <div class="error-message" id="priority_error"></div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label"><?= $t['description'] ?></label>
                                <div class="voice-input-wrapper">
                                    <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                    <button type="button" class="voice-btn" data-field="description" title="<?= $t['start_voice'] ?>">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                                <div class="error-message" id="description_error"></div>
                                <small class="text-muted"><?= $lang === 'en' ? 'Minimum 20 characters' : ($lang === 'es' ? 'Mínimo 20 caracteres' : ($lang === 'ar' ? '20 حرفًا على الأقل' : 'Minimum 20 caractères')) ?></small>
                            </div>
                            
                            <!-- Bouton Soumettre -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary py-3 px-5">
                                    <i class="fas fa-paper-plane me-2"></i> <?= $t['submit'] ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- FAQ Section -->
                    <div class="mt-5">
                        <h3 class="mb-4"><?= $t['faq_title'] ?></h3>
                        <div class="faq-container">
                            <?php foreach ($t['faq_questions'] as $question => $answer): ?>
                                <div class="faq-item">
                                    <div class="faq-question">
                                        <span><?= $question ?></span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p><?= $answer ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                   <!-- Section de recherche et affichage des réponses -->
                   <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="bg-light p-4 rounded-3 mb-4">
                        <h3 class="mb-4"><i class="fas fa-search me-2"></i><?= $t['check_responses'] ?></h3>
                        <form method="GET" action="" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="name" placeholder="<?= $t['search_placeholder'] ?>" 
                                       value="<?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search me-1"></i> <?= $t['search_button'] ?>
                                </button>
                            </div>
                        </form>
                        
                        <?php if ($reclamation_data && isset($reclamation_data['reclamations'])): ?>
                            <?php foreach ($reclamation_data['reclamations'] as $reclamation): ?>
                                <div class="reponse-container mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4><?= $t['claim_number'] ?><?= $reclamation['id'] ?></h4>
                                        <a href="https://wa.me/21690044054?text=<?= urlencode($lang === 'en' ? 'Hello, I\'m contacting about my complaint #' : ($lang === 'es' ? 'Hola, me contacto sobre mi reclamación #' : ($lang === 'ar' ? 'مرحبًا ، أنا أتصل بشأن شكواي رقم #' : 'Bonjour, je contacte au sujet de ma réclamation #'))) . $reclamation['id'] ?>"
                                           class="btn btn-success btn-sm"
                                           target="_blank">
                                            <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                        </a>
                                    </div>
                                    
                                    <p><strong><?= $lang === 'en' ? 'Subject:' : ($lang === 'es' ? 'Asunto:' : ($lang === 'ar' ? 'الموضوع:' : 'Sujet:')) ?></strong> 
                                    <?= isset($reclamation['subject']) ? htmlspecialchars($reclamation['subject']) : 'Non spécifié' ?></p>

                                    <p><strong><?= $lang === 'en' ? 'Type:' : ($lang === 'es' ? 'Tipo:' : ($lang === 'ar' ? 'النوع:' : 'Type:')) ?></strong> 
                                    <?= isset($reclamation['type']) ? getTypeText($reclamation['type']) : 'Non spécifié' ?></p>

                                    <p><strong><?= $t['status'] ?>:</strong> 
                                    <span class="<?= isset($reclamation['status']) ? getStatusClass($reclamation['status']) : 'badge bg-secondary' ?>">
                                    <?= isset($reclamation['status']) ? getStatusText($reclamation['status']) : 'Inconnu' ?>
                                    </span></p>
                                    
                                    <?php if (!empty($reclamation['reponses'])): ?>
                                        <h5 class="mb-3"><?= $t['admin_responses'] ?></h5>
                                        <?php foreach ($reclamation['reponses'] as $reponse): ?>
                                            <div class="card mb-3">
                                                <div class="card-header bg-secondary text-white">
                                                    <strong><?= $t['response_date'] ?> <?= formatDate($reponse['date_reponse']) ?></strong>
                                                </div>
                                                <div class="card-body">
                                                    <p><?= nl2br(htmlspecialchars($reponse['reponse'])) ?></p>
                                         <!-- Boutons Like/Dislike -->
                                         <div class="feedback-buttons mt-3">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="reponse_id" value="<?= $reponse['id'] ?>">
                                                            <input type="hidden" name="feedback_type" value="like">
                                                            <button type="submit" class="btn btn-sm btn-outline-success me-2">
                                                                <i class="fas fa-thumbs-up"></i> <?= $t['like'] ?> (<?= $reponse['likes'] ?? 0 ?>)
                                                            </button>
                                                        </form>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="reponse_id" value="<?= $reponse['id'] ?>">
                                                            <input type="hidden" name="feedback_type" value="dislike">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-thumbs-down"></i> <?= $t['dislike'] ?> (<?= $reponse['dislikes'] ?? 0 ?>)
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i><?= $t['no_response'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif (isset($_GET['name'])): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i><?= $t['no_claim'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Formulaire de Réclamation End -->
    
    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/21690044054" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp whatsapp-float-icon"></i>
    </a>
    
    <!-- Chatbot Toggler -->
    <div class="chatbot-toggler">
        <i class="fas fa-robot"></i>
    </div>
    
    <!-- Chatbot Container -->
    <div class="chatbot-container">
        <div class="chatbot-header">
            <h5><?= $t['chatbot_title'] ?></h5>
            <i class="fas fa-times close-chatbot"></i>
        </div>
        <div class="chatbot-body" id="chatbot-messages">
            <div class="bot-message">
                <?= $lang === 'en' ? 'Hello! How can I help you with your complaint today?' : 
                   ($lang === 'es' ? '¡Hola! ¿Cómo puedo ayudarte con tu reclamación hoy?' : 
                   ($lang === 'ar' ? 'مرحبًا! كيف يمكنني مساعدتك في شكواك اليوم؟' : 
                   'Bonjour ! Comment puis-je vous aider avec votre réclamation aujourd\'hui ?')) ?>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbot-input" placeholder="<?= $t['chatbot_placeholder'] ?>">
            <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    
    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">SkillBoost</h4>
                    <p>Plateforme complète pour l'entrepreneuriat et l'investissement.</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Liens rapides</h4>
                    <a class="btn btn-link" href="index.php">Accueil</a>
                    <a class="btn btn-link" href="Formations.php">Formations</a>
                    <a class="btn btn-link" href="evenements.php">Événements</a>
                    <a class="btn btn-link" href="reclamations.php">Réclamations</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p><i class="fa fa-map-marker-alt me-3"></i>Bloc E, Esprit, Cite La Gazelle</p>
                    <p><i class="fab fa-whatsapp me-3"></i><a href="https://wa.me/21690044054" class="text-light" target="_blank" style="text-decoration:none;">+216 90 044 054</a></p>
                    <p><i class="fa fa-envelope me-3"></i>SkillBoost@gmail.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Abonnez-vous à notre newsletter pour les dernières actualités.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Votre email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">S'inscrire</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">SkillBoost</a>, Tous droits réservés.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="#">Accueil</a>
                            <a href="#">Cookies</a>
                            <a href="#">Aide</a>
                            <a href="#">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <!-- Chatbot Script -->
    <script>
        $(document).ready(function() {
            // Toggle chatbot
            $('.chatbot-toggler').click(function() {
                $('.chatbot-container').toggle();
                // Scroll to bottom when opening
                if ($('.chatbot-container').is(':visible')) {
                    $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                }
            });

            $('.close-chatbot').click(function() {
                $('.chatbot-container').hide();
            });

            // Send message function
            function sendMessage() {
                const input = $('#chatbot-input');
                const message = input.val().trim();
                
                if (message) {
                    // Add user message
                    $('#chatbot-messages').append(`
                        <div class="chatbot-message user-message">
                            ${message}
                        </div>
                    `);
                    
                    // Clear input
                    input.val('');
                    
                    // Scroll to bottom
                    $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                    
                    // Show typing indicator
                    $('#chatbot-messages').append(`
                        <div class="chatbot-message bot-message typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    `);
                    $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                    
                    // Send to backend (AJAX call)
                    $.ajax({
                        url: 'chatbot_handler.php',
                        type: 'POST',
                        data: {
                            message: message,
                            lang: '<?= $lang ?>'
                        },
                        success: function(response) {
                            // Remove typing indicator
                            $('.typing-indicator').remove();
                            
                            // Add bot response
                            $('#chatbot-messages').append(`
                                <div class="chatbot-message bot-message">
                                    ${response}
                                </div>
                            `);
                            
                            // Scroll to bottom
                            $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                        },
                        error: function() {
                            $('.typing-indicator').remove();
                            $('#chatbot-messages').append(`
                                <div class="chatbot-message bot-message">
                                    <?= $lang === 'en' ? 'Sorry, I encountered an error. Please try again later.' : 
                                       ($lang === 'es' ? 'Lo siento, encontré un error. Por favor, inténtelo de nuevo más tarde.' : 
                                       ($lang === 'ar' ? 'عذرًا، واجهت خطأ. يرجى المحاولة مرة أخرى لاحقًا.' : 
                                       'Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.')) ?>
                                </div>
                            `);
                        }
                    });
                }
            }

            // Send message on button click or Enter key
            $('#chatbot-send').click(sendMessage);
            $('#chatbot-input').keypress(function(e) {
                if (e.which == 13) {
                    sendMessage();
                }
            });

            // FAQ toggle
            $('.faq-question').click(function() {
                $(this).toggleClass('active').next('.faq-answer').slideToggle();
            });
        });
    </script>
    
    <!-- Form Validation Script -->
    <script>
        $(document).ready(function() {
            $('#reclamationForm').on('submit', function(e) {
                let isValid = true;
                
                // Validate full name
                const fullName = $('#full_name').val().trim();
                if (fullName.length < 3 || /^\d+$/.test(fullName)) {
                    $('#full_name').addClass('is-invalid');
                    $('#full_name_error').text('<?= $lang === 'en' ? 'Full name must be at least 3 characters and not a number' : 
                                                  ($lang === 'es' ? 'El nombre debe tener al menos 3 caracteres y no ser un número' : 
                                                  ($lang === 'ar' ? 'يجب أن يكون الاسم الكامل 3 أحرف على الأقل وليس رقمًا' : 
                                                  'Le nom doit contenir au moins 3 caractères et ne peut pas être un nombre')) ?>');
                    isValid = false;
                } else {
                    $('#full_name').removeClass('is-invalid').addClass('is-valid');
                    $('#full_name_error').text('');
                }
                
                // Validate email
                const email = $('#email').val().trim();
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    $('#email').addClass('is-invalid');
                    $('#email_error').text('<?= $lang === 'en' ? 'Please enter a valid email address' : 
                                              ($lang === 'es' ? 'Por favor ingrese un correo electrónico válido' : 
                                              ($lang === 'ar' ? 'الرجاء إدخال عنوان بريد إلكتروني صالح' : 
                                              'Veuillez entrer une adresse email valide')) ?>');
                    isValid = false;
                } else {
                    $('#email').removeClass('is-invalid').addClass('is-valid');
                    $('#email_error').text('');
                }
                
                // Validate subject
                const subject = $('#subject').val().trim();
                if (subject.length < 3 || /^\d+$/.test(subject)) {
                    $('#subject').addClass('is-invalid');
                    $('#subject_error').text('<?= $lang === 'en' ? 'Subject must be at least 3 characters and not a number' : 
                                               ($lang === 'es' ? 'El asunto debe tener al menos 3 caracteres y no ser un número' : 
                                               ($lang === 'ar' ? 'يجب أن يكون الموضوع 3 أحرف على الأقل وليس رقمًا' : 
                                               'Le sujet doit contenir au moins 3 caractères et ne peut pas être un nombre')) ?>');
                    isValid = false;
                } else {
                    $('#subject').removeClass('is-invalid').addClass('is-valid');
                    $('#subject_error').text('');
                }
                
                // Validate type
                const type = $('#type').val();
                if (!type) {
                    $('#type').addClass('is-invalid');
                    $('#type_error').text('<?= $lang === 'en' ? 'Please select a type' : 
                                            ($lang === 'es' ? 'Por favor seleccione un tipo' : 
                                            ($lang === 'ar' ? 'الرجاء تحديد نوع' : 
                                            'Veuillez sélectionner un type')) ?>');
                    isValid = false;
                } else {
                    $('#type').removeClass('is-invalid').addClass('is-valid');
                    $('#type_error').text('');
                }
                
                // Validate priority
                const priority = $('#priority').val();
                if (!priority) {
                    $('#priority').addClass('is-invalid');
                    $('#priority_error').text('<?= $lang === 'en' ? 'Please select a priority' : 
                                               ($lang === 'es' ? 'Por favor seleccione una prioridad' : 
                                               ($lang === 'ar' ? 'الرجاء تحديد أولوية' : 
                                               'Veuillez sélectionner une priorité')) ?>');
                    isValid = false;
                } else {
                    $('#priority').removeClass('is-invalid').addClass('is-valid');
                    $('#priority_error').text('');
                }
                
                // Validate description
                const description = $('#description').val().trim();
                if (description.length < 20) {
                    $('#description').addClass('is-invalid');
                    $('#description_error').text('<?= $lang === 'en' ? 'Description must be at least 20 characters' : 
                                                    ($lang === 'es' ? 'La descripción debe tener al menos 20 caracteres' : 
                                                    ($lang === 'ar' ? 'يجب أن يكون الوصف 20 حرفًا على الأقل' : 
                                                    'La description doit faire au moins 20 caractères')) ?>');
                    isValid = false;
                } else {
                    $('#description').removeClass('is-invalid').addClass('is-valid');
                    $('#description_error').text('');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                }
            });
            
            // Real-time validation
            $('#full_name, #email, #subject, #description').on('input', function() {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.error-message').text('');
            });
            
            $('#type, #priority').on('change', function() {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.error-message').text('');
            });
        });
    </script>
    
   <!-- Voice Recognition Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const voiceButtons = document.querySelectorAll('.voice-btn');
    
    // Vérification de la compatibilité
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        voiceButtons.forEach(btn => {
            btn.disabled = true;
            btn.title = "<?= $t['voice_not_supported'] ?>";
            btn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
            btn.style.cursor = 'not-allowed';
        });
        console.error("Reconnaissance vocale non supportée par ce navigateur");
        return;
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.lang = '<?= 
        $lang === 'fr' ? 'fr-FR' : 
        ($lang === 'en' ? 'en-US' : 
        ($lang === 'es' ? 'es-ES' : 
        ($lang === 'ar' ? 'ar-SA' : 'fr-FR'))) ?>';
    
    let currentField = null;
    let isListening = false;

    // Vérifier les permissions avant de démarrer
    function checkMicrophonePermission() {
        return navigator.permissions.query({name: 'microphone'})
            .then(permissionStatus => {
                if (permissionStatus.state === 'denied') {
                    alert("<?= $lang === 'en' ? 'Microphone access is blocked. Please enable it in browser settings.' : 
                          'L\'accès au microphone est bloqué. Veuillez l\'autoriser dans les paramètres du navigateur.' ?>");
                    return false;
                }
                return true;
            })
            .catch(() => true); // Si l'API permissions n'est pas supportée, continuer
    }

    voiceButtons.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const fieldId = this.getAttribute('data-field');
            currentField = document.getElementById(fieldId);
            
            if (isListening) {
                recognition.stop();
                resetButton(this);
                isListening = false;
                return;
            }

            try {
                // Vérifier les permissions
                const hasPermission = await checkMicrophonePermission();
                if (!hasPermission) return;

                // Réinitialiser tous les boutons d'abord
                voiceButtons.forEach(resetButton);
                
                // Démarrer la reconnaissance
                recognition.start();
                this.classList.add('listening');
                this.innerHTML = '<i class="fas fa-microphone-slash"></i>';
                this.title = "<?= $t['stop_voice'] ?>";
                isListening = true;
                
                // Ajouter un indicateur visuel
                currentField.style.borderColor = '#4CAF50';
                currentField.style.boxShadow = '0 0 5px #4CAF50';
            } catch(error) {
                console.error("Erreur de reconnaissance vocale:", error);
                alert("<?= $lang === 'en' ? 'Error accessing microphone: ' : 
                      'Erreur d\'accès au microphone: ' ?>" + error.message);
                resetButton(this);
                isListening = false;
            }
        });
    });

    function resetButton(btn) {
        btn.classList.remove('listening');
        btn.innerHTML = '<i class="fas fa-microphone"></i>';
        btn.title = "<?= $t['start_voice'] ?>";
        if (currentField) {
            currentField.style.borderColor = '';
            currentField.style.boxShadow = '';
        }
    }

    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript.trim();
        if (currentField) {
            if (currentField.tagName === 'SELECT') {
                // Gestion spéciale pour les menus déroulants
                const options = Array.from(currentField.options);
                const matchedOption = options.find(opt => 
                    opt.text.toLowerCase().includes(transcript.toLowerCase())
                );
                if (matchedOption) {
                    currentField.value = matchedOption.value;
                    // Déclencher l'événement change
                    const event = new Event('change', { bubbles: true });
                    currentField.dispatchEvent(event);
                }
            } else {
                // Pour les champs texte/textarea
                currentField.value = transcript;
                // Déclencher l'événement input pour la validation
                const event = new Event('input', { bubbles: true });
                currentField.dispatchEvent(event);
            }
        }
    };

    recognition.onerror = function(event) {
        console.error("Erreur de reconnaissance:", event.error);
        voiceButtons.forEach(resetButton);
        isListening = false;
        
        if (event.error === 'not-allowed') {
            alert("<?= $lang === 'en' ? 'Microphone access denied. Please allow microphone access in browser settings.' : 
                  'Accès au microphone refusé. Veuillez autoriser l\'accès dans les paramètres du navigateur.' ?>");
        } else if (event.error === 'no-speech') {
            console.log("Aucune parole détectée");
        }
    };

    recognition.onend = function() {
        voiceButtons.forEach(resetButton);
        isListening = false;
    };
});
</script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer les points de l'utilisateur (exemple avec localStorage)
        // En production, vous devriez utiliser une requête AJAX vers votre backend
        let userPoints = localStorage.getItem('userPoints') || 0;
        document.getElementById('user-points').textContent = userPoints;
        
        // Si l'utilisateur a reçu un message de récompense
        <?php if ($message && $alert_class === 'success'): ?>
            // Ajouter les points
            userPoints = parseInt(userPoints) + 5;
            localStorage.setItem('userPoints', userPoints);
            document.getElementById('user-points').textContent = userPoints;
            
            // Afficher une notification
            const notif = document.createElement('div');
            notif.className = 'alert alert-success position-fixed top-0 end-0 m-3';
            notif.style.zIndex = '1100';
            notif.textContent = '<?= $t['reward_message'] ?>';
            document.body.appendChild(notif);
            
            // Supprimer après 3 secondes
            setTimeout(() => notif.remove(), 3000);
        <?php endif; ?>
        
        // Animation des points
        const pointsElement = document.getElementById('user-points');
        pointsElement.addEventListener('DOMSubtreeModified', function() {
            this.classList.add('animate__animated', 'animate__bounce');
            setTimeout(() => {
                this.classList.remove('animate__animated', 'animate__bounce');
            }, 1000);
        });
    });
    </script>
</body>
</html>