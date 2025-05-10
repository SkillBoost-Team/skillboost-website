<?php
session_start();

// ==============================================
// GESTION DE LA LANGUE ET TRADUCTIONS
// ==============================================
$lang = 'fr';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en', 'es', 'ar'])) {
    $lang = $_GET['lang'];
    setcookie('lang', $lang, time() + (86400 * 30), "/");
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
}
$translations = [
    'fr' => [
        'title' => 'SkillBoost',
        'register' => 'Inscription',
        'login' => 'Connexion',
        'forgot' => 'Mot de passe oublié',
        'reset' => 'Réinitialiser le mot de passe',
        'username' => "Nom d'utilisateur",
        'full_name' => 'Nom complet',
        'nom' => 'Nom affiché',
        'email' => 'Email',
        'password' => 'Mot de passe',
        'confirm_password' => 'Confirmer le mot de passe',
        'submit_register' => "S'inscrire",
        'already_registered' => 'Déjà inscrit?',
        'login_here' => 'Se connecter',
        'forgot_link' => 'Mot de passe oublié?',
        'create_account' => 'Créer un compte',
        'send_reset' => 'Envoyer le lien de réinitialisation',
        'back_login' => 'Retour à la connexion',
        'reset_password' => 'Réinitialiser',
        'topbar_address' => 'Bloc E, Esprit, Cite La Gazelle',
        'topbar_phone' => '+216 90 044 054',
        'topbar_email' => 'SkillBoost@gmail.com',
        'home' => 'Accueil',
        'projects' => 'Projets',
        'trainings' => 'Formations',
        'events' => 'Événements',
        'investments' => 'Investissements',
        'complaints' => 'Réclamations',
        'newsletter' => 'Abonnez-vous à notre newsletter pour les dernières actualités.',
        'newsletter_btn' => "S'inscrire",
        'footer_about' => "Plateforme complète pour l'entrepreneuriat et l'investissement.",
        'quick_links' => 'Liens rapides',
        'contact' => 'Contact',
        'newsletter_title' => 'Newsletter',
        'faq' => 'FAQ',
        'help' => 'Aide',
        'cookies' => 'Cookies',
        'voice_input' => 'Saisie vocale',
        'start_voice' => 'Commencer la saisie vocale',
        'stop_voice' => 'Arrêter la saisie vocale',
        'voice_not_supported' => 'La saisie vocale n\'est pas supportée par votre navigateur',
        'chatbot_title' => 'Assistant Virtuel',
        'chatbot_placeholder' => 'Posez votre question ici...',
        'chatbot_greeting' => 'Bonjour ! Comment puis-je vous aider ?',
        'chatbot_error' => "Désolé, j'ai rencontré une erreur. Veuillez réessayer plus tard.",
    ],
    'en' => [
        'title' => 'SkillBoost',
        'register' => 'Register',
        'login' => 'Login',
        'forgot' => 'Forgot password',
        'reset' => 'Reset password',
        'username' => 'Username',
        'full_name' => 'Full name',
        'nom' => 'Display name',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm password',
        'submit_register' => 'Sign up',
        'already_registered' => 'Already registered?',
        'login_here' => 'Login here',
        'forgot_link' => 'Forgot password?',
        'create_account' => 'Create an account',
        'send_reset' => 'Send reset link',
        'back_login' => 'Back to login',
        'reset_password' => 'Reset',
        'topbar_address' => 'Bloc E, Esprit, Cite La Gazelle',
        'topbar_phone' => '+216 90 044 054',
        'topbar_email' => 'SkillBoost@gmail.com',
        'home' => 'Home',
        'projects' => 'Projects',
        'trainings' => 'Trainings',
        'events' => 'Events',
        'investments' => 'Investments',
        'complaints' => 'Complaints',
        'newsletter' => 'Subscribe to our newsletter for the latest news.',
        'newsletter_btn' => 'Subscribe',
        'footer_about' => 'Comprehensive platform for entrepreneurship and investment.',
        'quick_links' => 'Quick Links',
        'contact' => 'Contact',
        'newsletter_title' => 'Newsletter',
        'faq' => 'FAQ',
        'help' => 'Help',
        'cookies' => 'Cookies',
        'voice_input' => 'Voice input',
        'start_voice' => 'Start voice input',
        'stop_voice' => 'Stop voice input',
        'voice_not_supported' => 'Voice input not supported by your browser',
        'chatbot_title' => 'Virtual Assistant',
        'chatbot_placeholder' => 'Ask your question here...',
        'chatbot_greeting' => 'Hello! How can I help you?',
        'chatbot_error' => 'Sorry, I encountered an error. Please try again later.',
    ],
    'es' => [
        'title' => 'SkillBoost',
        'register' => 'Registro',
        'login' => 'Iniciar sesión',
        'forgot' => 'Olvidé mi contraseña',
        'reset' => 'Restablecer contraseña',
        'username' => 'Nombre de usuario',
        'full_name' => 'Nombre completo',
        'nom' => 'Nombre para mostrar',
        'email' => 'Correo electrónico',
        'password' => 'Contraseña',
        'confirm_password' => 'Confirmar contraseña',
        'submit_register' => 'Registrarse',
        'already_registered' => '¿Ya registrado?',
        'login_here' => 'Iniciar sesión',
        'forgot_link' => '¿Olvidó su contraseña?',
        'create_account' => 'Crear una cuenta',
        'send_reset' => 'Enviar enlace de restablecimiento',
        'back_login' => 'Volver al inicio de sesión',
        'reset_password' => 'Restablecer',
        'topbar_address' => 'Bloc E, Esprit, Cite La Gazelle',
        'topbar_phone' => '+216 90 044 054',
        'topbar_email' => 'SkillBoost@gmail.com',
        'home' => 'Inicio',
        'projects' => 'Proyectos',
        'trainings' => 'Formaciones',
        'events' => 'Eventos',
        'investments' => 'Inversiones',
        'complaints' => 'Reclamaciones',
        'newsletter' => 'Suscríbase a nuestro boletín para las últimas noticias.',
        'newsletter_btn' => 'Suscribirse',
        'footer_about' => 'Plataforma integral para el emprendimiento y la inversión.',
        'quick_links' => 'Enlaces rápidos',
        'contact' => 'Contacto',
        'newsletter_title' => 'Boletín',
        'faq' => 'FAQ',
        'help' => 'Ayuda',
        'cookies' => 'Cookies',
        'voice_input' => 'Entrada de voz',
        'start_voice' => 'Iniciar entrada de voz',
        'stop_voice' => 'Detener entrada de voz',
        'voice_not_supported' => 'La entrada de voz no es compatible con su navegador',
        'chatbot_title' => 'Asistente Virtual',
        'chatbot_placeholder' => 'Haz tu pregunta aquí...',
        'chatbot_greeting' => '¡Hola! ¿Cómo puedo ayudarte?',
        'chatbot_error' => 'Lo siento, encontré un error. Por favor, inténtelo de nuevo más tarde.',
    ],
    'ar' => [
        'title' => 'SkillBoost',
        'register' => 'تسجيل',
        'login' => 'تسجيل الدخول',
        'forgot' => 'نسيت كلمة المرور',
        'reset' => 'إعادة تعيين كلمة المرور',
        'username' => 'اسم المستخدم',
        'full_name' => 'الاسم الكامل',
        'nom' => 'اسم العرض',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'confirm_password' => 'تأكيد كلمة المرور',
        'submit_register' => 'تسجيل',
        'already_registered' => 'مسجل بالفعل؟',
        'login_here' => 'تسجيل الدخول',
        'forgot_link' => 'نسيت كلمة المرور؟',
        'create_account' => 'إنشاء حساب',
        'send_reset' => 'إرسال رابط إعادة التعيين',
        'back_login' => 'العودة لتسجيل الدخول',
        'reset_password' => 'إعادة تعيين',
        'topbar_address' => 'بلوك E، اسبريت، cité la gazelle',
        'topbar_phone' => '+216 90 044 054',
        'topbar_email' => 'SkillBoost@gmail.com',
        'home' => 'الرئيسية',
        'projects' => 'المشاريع',
        'trainings' => 'التدريبات',
        'events' => 'الفعاليات',
        'investments' => 'الاستثمارات',
        'complaints' => 'الشكاوى',
        'newsletter' => 'اشترك في النشرة الإخبارية لأحدث الأخبار.',
        'newsletter_btn' => 'اشترك',
        'footer_about' => 'منصة شاملة لريادة الأعمال والاستثمار.',
        'quick_links' => 'روابط سريعة',
        'contact' => 'اتصال',
        'newsletter_title' => 'النشرة الإخبارية',
        'faq' => 'الأسئلة الشائعة',
        'help' => 'مساعدة',
        'cookies' => 'كوكيز',
        'voice_input' => 'إدخال صوتي',
        'start_voice' => 'بدء الإدخال الصوتي',
        'stop_voice' => 'إيقاف الإدخال الصوتي',
        'voice_not_supported' => 'الإدخال الصوتي غير مدعوم من متصفحك',
        'chatbot_title' => 'المساعد الافتراضي',
        'chatbot_placeholder' => 'اطرح سؤالك هنا...',
        'chatbot_greeting' => 'مرحبًا! كيف يمكنني مساعدتك؟',
        'chatbot_error' => 'عذرًا، واجهت خطأ. يرجى المحاولة مرة أخرى لاحقًا.',
    ]
];
$t = $translations[$lang];

// ==============================================
// CONFIGURATION
// ==============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'skillboost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SITE_URL', 'http://localhost/integ');
define('GOOGLE_CLIENT_ID', 'your-google-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-google-secret');

// ==============================================
// CONNEXION À LA BASE DE DONNÉES
// ==============================================
try {
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// ==============================================
// FONCTIONS PRINCIPALES
// ==============================================
function handleRegister($conn) {
    $required = ['username', 'full_name', 'nom', 'email', 'password', 'confirm_password'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            return "Tous les champs sont obligatoires";
        }
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        return "Les mots de passe ne correspondent pas";
    }

    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $nom = trim($_POST['nom']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        return "Email invalide";
    }

    // Vérifier si l'email ou le username existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount() > 0) {
        return "Email ou nom d'utilisateur déjà utilisé";
    }

    // Hachage du mot de passe
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertion dans la base de données
    try {
        $stmt = $conn->prepare("INSERT INTO users 
            (username, full_name, nom, email, password_hash, role) 
            VALUES (?, ?, ?, ?, ?, 'utilisateur')");
        
        $stmt->execute([$username, $full_name, $nom, $email, $password_hash]);

        $_SESSION['message'] = "Inscription réussie! Vous pouvez maintenant vous connecter";
        header("Location: ?action=login");
        exit();
    } catch (PDOException $e) {
        error_log("Erreur d'inscription: " . $e->getMessage());
        return "Une erreur est survenue lors de l'inscription";
    }
}

function handleLogin($conn) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        return "Email et mot de passe requis";
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, full_name, nom, email, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;
        header("Location: index.html");
        exit();
    }

    return "Email ou mot de passe incorrect";
}

function handleForgotPassword($conn) {
    if (empty($_POST['email'])) {
        return "Email requis";
    }

    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        return "Email non trouvé";
    }

    $token = bin2hex(random_bytes(32));
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
    $stmt->execute([$token, $expiry, $email]);

    // En production, vous devriez envoyer un email ici
    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_token'] = $token;
    
    return "Un lien de réinitialisation a été généré (simulé)";
}

function handleResetPassword($conn) {
    if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
        return "Tous les champs sont requis";
    }

    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        return "Les mots de passe ne correspondent pas";
    }

    $token = $_GET['token'] ?? '';
    $email = $_SESSION['reset_email'] ?? '';
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    if (empty($token) || empty($email)) {
        return "Lien invalide";
    }

    // Vérifier le token
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$email, $token]);
    
    if ($stmt->rowCount() === 0) {
        return "Lien expiré ou invalide";
    }

    // Mettre à jour le mot de passe
    $stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?");
    if ($stmt->execute([$new_password, $email])) {
        unset($_SESSION['reset_email']);
        $_SESSION['message'] = "Mot de passe réinitialisé avec succès";
        header("Location: ?action=login");
        exit();
    }

    return "Erreur lors de la réinitialisation";
}

// ==============================================
// GESTION DES REQUÊTES
// ==============================================
$action = $_GET['action'] ?? 'login';
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'register':
            $message = handleRegister($conn);
            break;
        case 'login':
            $message = handleLogin($conn);
            break;
        case 'forgot':
            $message = handleForgotPassword($conn);
            break;
        case 'reset':
            $message = handleResetPassword($conn);
            break;
    }
}

// ==============================================
// AFFICHAGE
// ==============================================
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillBoost - <?= $t[$action] ?? ucfirst($action) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .auth-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .voice-btn {
            position: absolute;
            right: 40px;
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
        .voice-btn.listening {
            color: #e74c3c;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
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
    </style>
</head>
<body>
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
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i><?= $t['topbar_address'] ?></small>
                    <small class="me-3 text-light"><i class="fab fa-whatsapp me-2"></i><a href="https://wa.me/21690044054" class="text-light" target="_blank" style="text-decoration:none;"><?= $t['topbar_phone'] ?></a></small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i><?= $t['topbar_email'] ?></small>
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
                    <a href="index.php" class="nav-item nav-link"><i class="fas fa-home"></i> <?= $t['home'] ?></a>
                    <a href="login.php" class="nav-item nav-link active"><i class="fas fa-sign-in-alt"></i> <?= $t['login'] ?></a>
                    <a href="#" class="nav-item nav-link"><i class="fas fa-project-diagram"></i> <?= $t['projects'] ?></a>
                    <a href="Formations.php" class="nav-item nav-link"><i class="fas fa-graduation-cap"></i> <?= $t['trainings'] ?></a>
                    <a href="evenements.php" class="nav-item nav-link"><i class="fas fa-calendar-alt"></i> <?= $t['events'] ?></a>
                    <a href="gestionInvestissement.php" class="nav-item nav-link"><i class="fas fa-chart-line"></i> <?= $t['investments'] ?></a>
                    <a href="reclamations.php" class="nav-item nav-link"><i class="fas fa-exclamation-circle"></i> <?= $t['complaints'] ?></a>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar End -->
    <div class="container py-5">
        <div class="auth-container">
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            <?php switch($action): 
                case 'register': ?>
                    <h2 class="text-center mb-4"><?= $t['register'] ?></h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label"><?= $t['username'] ?></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= $t['full_name'] ?></label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= $t['nom'] ?></label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['email'] ?></label>
                            <input type="email" name="email" class="form-control" id="regEmail" required>
                            <button type="button" class="voice-btn" data-field="regEmail" title="<?= $t['start_voice'] ?>"><i class="fas fa-microphone"></i></button>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['password'] ?></label>
                            <input type="password" name="password" id="regPassword" class="form-control" required>
                            <span class="password-toggle" onclick="togglePassword('regPassword')">👁️</span>
                            <button type="button" class="voice-btn" data-field="regPassword" title="<?= $t['start_voice'] ?>"><i class="fas fa-microphone"></i></button>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['confirm_password'] ?></label>
                            <input type="password" name="confirm_password" id="regConfirmPassword" class="form-control" required>
                            <span class="password-toggle" onclick="togglePassword('regConfirmPassword')">👁️</span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><?= $t['submit_register'] ?></button>
                        <div class="mt-3 text-center">
                            <?= $t['already_registered'] ?> <a href="?action=login"><?= $t['login_here'] ?></a>
                        </div>
                    </form>
                    <?php break; ?>
                <?php case 'login': ?>
                    <h2 class="text-center mb-4"><?= $t['login'] ?></h2>
                    <form method="POST">
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['email'] ?></label>
                            <input type="email" name="email" class="form-control" id="loginEmail" required>
                            <button type="button" class="voice-btn" data-field="loginEmail" title="<?= $t['start_voice'] ?>"><i class="fas fa-microphone"></i></button>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['password'] ?></label>
                            <input type="password" name="password" id="loginPassword" class="form-control" required>
                            <span class="password-toggle" onclick="togglePassword('loginPassword')">👁️</span>
                            <button type="button" class="voice-btn" data-field="loginPassword" title="<?= $t['start_voice'] ?>"><i class="fas fa-microphone"></i></button>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3"><?= $t['login_here'] ?></button>
                        <div class="text-center">
                            <a href="?action=forgot"><?= $t['forgot_link'] ?></a> | 
                            <a href="?action=register"><?= $t['create_account'] ?></a>
                        </div>
                    </form>
                    <?php break; ?>
                <?php case 'forgot': ?>
                    <h2 class="text-center mb-4"><?= $t['forgot'] ?></h2>
                    <form method="POST">
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['email'] ?></label>
                            <input type="email" name="email" class="form-control" id="forgotEmail" required>
                            <button type="button" class="voice-btn" data-field="forgotEmail" title="<?= $t['start_voice'] ?>"><i class="fas fa-microphone"></i></button>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><?= $t['send_reset'] ?></button>
                        <div class="mt-3 text-center">
                            <a href="?action=login"><?= $t['back_login'] ?></a>
                        </div>
                    </form>
                    <?php break; ?>
                <?php case 'reset': ?>
                    <h2 class="text-center mb-4"><?= $t['reset'] ?></h2>
                    <form method="POST">
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['password'] ?></label>
                            <input type="password" name="new_password" id="newPassword" class="form-control" required>
                            <span class="password-toggle" onclick="togglePassword('newPassword')">👁️</span>
                            <button type="button" class="voice-btn" data-field="newPassword" title="<?= $t['start_voice'] ?>"><i class="fas fa-microphone"></i></button>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label"><?= $t['confirm_password'] ?></label>
                            <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required>
                            <span class="password-toggle" onclick="togglePassword('confirmPassword')">👁️</span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><?= $t['reset_password'] ?></button>
                    </form>
                    <?php break; ?>
            <?php endswitch; ?>
        </div>
    </div>
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
                <?= $t['chatbot_greeting'] ?>
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
                    <p><?= $t['footer_about'] ?></p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3"><?= $t['quick_links'] ?></h4>
                    <a class="btn btn-link" href="index.php"><?= $t['home'] ?></a>
                    <a class="btn btn-link" href="Formations.php"><?= $t['trainings'] ?></a>
                    <a class="btn btn-link" href="evenements.php"><?= $t['events'] ?></a>
                    <a class="btn btn-link" href="reclamations.php"><?= $t['complaints'] ?></a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3"><?= $t['contact'] ?></h4>
                    <p><i class="fa fa-map-marker-alt me-3"></i><?= $t['topbar_address'] ?></p>
                    <p><i class="fab fa-whatsapp me-3"></i><a href="https://wa.me/21690044054" class="text-light" target="_blank" style="text-decoration:none;"><?= $t['topbar_phone'] ?></a></p>
                    <p><i class="fa fa-envelope me-3"></i><?= $t['topbar_email'] ?></p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3"><?= $t['newsletter_title'] ?></h4>
                    <p><?= $t['newsletter'] ?></p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="<?= $t['email'] ?>">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2"><?= $t['newsletter_btn'] ?></button>
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
                            <a href="#"><?= $t['home'] ?></a>
                            <a href="#"><?= $t['cookies'] ?></a>
                            <a href="#"><?= $t['help'] ?></a>
                            <a href="#"><?= $t['faq'] ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
    <!-- Chatbot Script -->
    <script>
        $(document).ready(function() {
            // Toggle chatbot
            $('.chatbot-toggler').click(function() {
                $('.chatbot-container').toggle();
                if ($('.chatbot-container').is(':visible')) {
                    $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                }
            });
            $('.close-chatbot').click(function() {
                $('.chatbot-container').hide();
            });
            function sendMessage() {
                const input = $('#chatbot-input');
                const message = input.val().trim();
                if (message) {
                    $('#chatbot-messages').append(`
                        <div class="chatbot-message user-message">
                            ${message}
                        </div>
                    `);
                    input.val('');
                    $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                    $('#chatbot-messages').append(`
                        <div class="chatbot-message bot-message typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    `);
                    $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                    $.ajax({
                        url: 'chatbot_handler.php',
                        type: 'POST',
                        data: {
                            message: message,
                            lang: '<?= $lang ?>'
                        },
                        success: function(response) {
                            $('.typing-indicator').remove();
                            $('#chatbot-messages').append(`
                                <div class="chatbot-message bot-message">
                                    ${response}
                                </div>
                            `);
                            $('.chatbot-body').scrollTop($('.chatbot-body')[0].scrollHeight);
                        },
                        error: function() {
                            $('.typing-indicator').remove();
                            $('#chatbot-messages').append(`
                                <div class="chatbot-message bot-message">
                                    <?= $t['chatbot_error'] ?>
                                </div>
                            `);
                        }
                    });
                }
            }
            $('#chatbot-send').click(sendMessage);
            $('#chatbot-input').keypress(function(e) {
                if (e.which == 13) {
                    sendMessage();
                }
            });
        });
    </script>
    <!-- Voice Recognition Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const voiceButtons = document.querySelectorAll('.voice-btn');
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            voiceButtons.forEach(btn => {
                btn.disabled = true;
                btn.title = "<?= $t['voice_not_supported'] ?>";
                btn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
                btn.style.cursor = 'not-allowed';
            });
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
        voiceButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const fieldId = this.getAttribute('data-field');
                currentField = document.getElementById(fieldId);
                if (isListening) {
                    recognition.stop();
                    resetButton(this);
                    isListening = false;
                    return;
                }
                voiceButtons.forEach(resetButton);
                recognition.start();
                this.classList.add('listening');
                this.innerHTML = '<i class="fas fa-microphone-slash"></i>';
                this.title = "<?= $t['stop_voice'] ?>";
                isListening = true;
                if (currentField) {
                    currentField.style.borderColor = '#4CAF50';
                    currentField.style.boxShadow = '0 0 5px #4CAF50';
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
                currentField.value = transcript;
                const eventInput = new Event('input', { bubbles: true });
                currentField.dispatchEvent(eventInput);
            }
        };
        recognition.onerror = function(event) {
            voiceButtons.forEach(resetButton);
            isListening = false;
        };
        recognition.onend = function() {
            voiceButtons.forEach(resetButton);
            isListening = false;
        };
    });
    </script>
</body>
</html>