<?php
require_once __DIR__.'/config/Database.php';
require_once __DIR__.'/config/functions.php';
require_once __DIR__.'/controllers/ReclamationController.php';
require_once __DIR__.'/controllers/ChatbotController.php';
require_once __DIR__.'/controllers/AuthController.php';

// Initialisation de la session
session_start();

// Chargement des dépendances
$db = Database::getConnection();

// Gestion multilingue
$availableLanguages = ['fr', 'en', 'es', 'ar'];
$defaultLanguage = 'fr';

if (isset($_GET['lang']) {
    $_SESSION['lang'] = in_array($_GET['lang'], $availableLanguages) ? $_GET['lang'] : $defaultLanguage;
} elseif (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $defaultLanguage;
}

// Chargement des traductions
require_once __DIR__.'/lang/'.$_SESSION['lang'].'.php';

// Initialisation des contrôleurs
$reclamationController = new ReclamationController($db);
$chatbotController = new ChatbotController($db);
$authController = new AuthController($db);

// Analyse de l'URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/skillboost'; // Ajustez selon votre configuration
$route = str_replace($basePath, '', $requestUri);
$route = trim($route, '/');
$routeParts = explode('/', $route);

// Router principal
try {
    switch ($routeParts[0] ?? 'home') {
        // Routes publiques
        case '':
        case 'home':
            require __DIR__.'/views/home.php';
            break;
            
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->login($_POST);
            } else {
                require __DIR__.'/views/auth/login.php';
            }
            break;
            
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->register($_POST);
            } else {
                require __DIR__.'/views/auth/register.php';
            }
            break;
            
        // Routes réclamations
        case 'reclamations':
            $this->handleReclamationsRoutes($routeParts);
            break;
            
        // API Chatbot
        case 'api':
            header('Content-Type: application/json');
            if ($routeParts[1] === 'chatbot') {
                echo $chatbotController->handleRequest();
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint API non trouvé']);
            }
            break;
            
        // Admin
        case 'admin':
            $this->handleAdminRoutes($routeParts);
            break;
            
        default:
            http_response_code(404);
            require __DIR__.'/views/errors/404.php';
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    require __DIR__.'/views/errors/500.php';
}

// Fonction de routage pour les réclamations
function handleReclamationsRoutes($routeParts) {
    global $reclamationController;
    
    switch ($routeParts[1] ?? 'list') {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $reclamationController->create($_POST);
            } else {
                $reclamationController->showCreateForm();
            }
            break;
            
        case 'show':
            if (isset($routeParts[2])) {
                $reclamationController->show($routeParts[2]);
            } else {
                header('Location: /reclamations');
            }
            break;
            
        case 'respond':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($routeParts[2])) {
                $reclamationController->addResponse($routeParts[2], $_POST);
            }
            break;
            
        default:
            $page = $_GET['page'] ?? 1;
            $reclamationController->index($page);
    }
}

// Fonction de routage pour l'admin
function handleAdminRoutes($routeParts) {
    if (!isset($_SESSION['admin'])) {
        header('Location: /login');
        exit();
    }
    
    global $reclamationController;
    
    switch ($routeParts[1] ?? 'dashboard') {
        case 'dashboard':
            require __DIR__.'/views/admin/dashboard.php';
            break;
            
        case 'reclamations':
            $this->handleAdminReclamations($routeParts);
            break;
            
        default:
            http_response_code(404);
            require __DIR__.'/views/errors/404.php';
    }
}