<?php
require_once 'ReclamationModel.php';

class ReclamationController {
    private $model;

    public function __construct() {
        $this->model = new ReclamationModel('localhost', 'skillboost', 'root', '');
    }

    // Action principale : Afficher la liste des réclamations
    public function index() {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'type' => $_GET['type'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'date' => $_GET['date'] ?? ''
        ];
        $reclamations = $this->model->getAllReclamations($filters);

        // Inclure la vue
        include 'views/reclamations.php';
    }

    // Action pour créer une réclamation
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                ':full_name' => $_POST['full_name'],
                ':email' => $_POST['email'],
                ':subject' => $_POST['subject'],
                ':type' => $_POST['type'],
                ':priority' => $_POST['priority'],
                ':description' => $_POST['description'],
                ':status' => $_POST['status']
            ];
            $this->model->createReclamation($data);
            header("Location: reclamations.php");
            exit();
        }
    }

    // Action pour mettre à jour une réclamation
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                ':full_name' => $_POST['full_name'],
                ':email' => $_POST['email'],
                ':subject' => $_POST['subject'],
                ':type' => $_POST['type'],
                ':priority' => $_POST['priority'],
                ':description' => $_POST['description'],
                ':status' => $_POST['status']
            ];
            $this->model->updateReclamation($id, $data);
            header("Location: reclamations.php");
            exit();
        }
    }

    // Action pour supprimer une réclamation
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $this->model->deleteReclamation($id);
            header("Location: reclamations.php");
            exit();
        }
    }

    // Action pour marquer une réclamation comme résolue
    public function resolve() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $this->model->resolveReclamation($id);
            header("Location: reclamations.php");
            exit();
        }
    }
}

// Router les actions
$action = $_GET['action'] ?? 'index';
$controller = new ReclamationController();

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'resolve':
        $controller->resolve();
        break;
    default:
        $controller->index();
        break;
}