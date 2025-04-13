<?php
require_once __DIR__ . '/../models/ReclamationModel.php';

class ReclamationController {
    private $model;

    public function __construct() {
        $this->model = new ReclamationModel();
    }

    public function index() {
        $reclamations = $this->model->getAll();
        include '../application/views/reclamations_admin.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->insert($_POST);
            header('Location: index.php');
        }
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php');
    }

    public function edit($id) {
        $reclamation = $this->model->find($id);
        include '../application/views/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($_POST);
            header('Location: index.php');
        }
    }
}
?>
