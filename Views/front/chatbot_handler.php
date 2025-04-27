<?php
header('Content-Type: text/plain; charset=utf-8');

// Simuler un temps de réponse
sleep(1);

// Récupérer la langue et le message
$lang = $_POST['lang'] ?? 'fr';
$message = strtolower(trim($_POST['message']));

// Réponses possibles
$responses = [
    'fr' => [
        'salut' => 'Bonjour ! Comment puis-je vous aider avec votre réclamation aujourd\'hui ?',
        'status' => 'Vous pouvez vérifier l\'état de votre réclamation en entrant son numéro dans la section de recherche ci-dessus.',
        'temps' => 'Nous traitons généralement les réclamations en 2-5 jours ouvrables selon la priorité.',
        'modifier' => 'Une fois soumise, la réclamation ne peut plus être modifiée. Contactez-nous par WhatsApp pour toute modification.',
        'contact' => 'Vous pouvez nous contacter par WhatsApp au +216 90 044 054 ou par email à SkillBoost@gmail.com',
        'default' => 'Je comprends que vous avez une question. Pour une aide spécifique, contactez-nous via WhatsApp ou consultez notre FAQ.'
    ],
    'en' => [
        'hi' => 'Hello! How can I help you with your complaint today?',
        'status' => 'You can check your complaint status by entering its number in the search section above.',
        'time' => 'We typically process complaints within 2-5 business days depending on priority.',
        'modify' => 'Once submitted, the complaint cannot be modified. Contact us on WhatsApp for any changes.',
        'contact' => 'You can contact us on WhatsApp at +216 90 044 054 or by email at SkillBoost@gmail.com',
        'default' => 'I understand you have a question. For specific help, contact us via WhatsApp or check our FAQ.'
    ],
    'es' => [
        'hola' => '¡Hola! ¿Cómo puedo ayudarte con tu reclamación hoy?',
        'estado' => 'Puede verificar el estado de su reclamación ingresando su número en la sección de búsqueda anterior.',
        'tiempo' => 'Normalmente procesamos las reclamaciones en 2-5 días hábiles según la prioridad.',
        'modificar' => 'Una vez enviada, la reclamación no se puede modificar. Contáctenos por WhatsApp para cualquier cambio.',
        'contacto' => 'Puede contactarnos por WhatsApp al +216 90 044 054 o por correo electrónico a SkillBoost@gmail.com',
        'default' => 'Entiendo que tienes una pregunta. Para ayuda específica, contáctenos por WhatsApp o consulte nuestra FAQ.'
    ],
    'ar' => [
        'مرحبا' => 'مرحبًا! كيف يمكنني مساعدتك في شكواك اليوم؟',
        'حالة' => 'يمكنك التحقق من حالة شكواك بإدخال رقمها في قسم البحث أعلاه.',
        'وقت' => 'نحن نعالج الشكاوى عادة في غضون 2-5 أيام عمل حسب الأولوية.',
        'تعديل' => 'بعد الإرسال، لا يمكن تعديل الشكوى. اتصل بنا على الواتساب لأي تغييرات.',
        'اتصال' => 'يمكنك الاتصال بنا على الواتساب على الرقم +216 90 044 054 أو عبر البريد الإلكتروني SkillBoost@gmail.com',
        'default' => 'أفهم أن لديك سؤالاً. للحصول على مساعدة محددة، اتصل بنا عبر الواتساب أو تحقق من الأسئلة الشائعة.'
    ]
];

// Trouver la réponse appropriée
$response = $responses[$lang]['default'];

// Vérifier les mots-clés
foreach ($responses[$lang] as $keyword => $reply) {
    if (strpos($message, $keyword) !== false) {
        $response = $reply;
        break;
    }
}

echo $response;
?>