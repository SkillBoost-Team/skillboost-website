<?php
require_once '../models/ReponsesModel.php';

class ReclamationController {
    private $model;
    private $reclamation_id;

    public function __construct($pdo) {
        session_start();
        $this->model = new ReclamationModel($pdo);
        $this->reclamation_id = isset($_GET['reclamation_id']) ? intval($_GET['reclamation_id']) : 0;

        if ($this->reclamation_id <= 0) {
            die("ID de réclamation invalide.");
        }
    }

    public function handleRequest() {
        // Vérifier si la réclamation existe
        $reclamation = $this->model->getReclamationById($this->reclamation_id);
        if (!$reclamation) {
            die("Réclamation non trouvée.");
        }

        // Traitement des actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostActions();
        } elseif (isset($_GET['delete_response'])) {
            $this->handleDeleteResponse();
        } elseif (isset($_GET['change_status'])) {
            $this->handleStatusChange();
        }

        // Récupérer les données pour la vue
        $responses = $this->model->getResponsesForReclamation($this->reclamation_id);
        $reclamation = $this->model->getReclamationById($this->reclamation_id);

        // Messages de session
        $success_message = $_SESSION['success_message'] ?? null;
        $error_message = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);

        // Inclure la vue
        require '../views/reclamation_responses.php';
    }

    private function handlePostActions() {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_response':
                    $this->addResponse();
                    break;
                case 'update_response':
                    $this->updateResponse();
                    break;
            }
        }
    }

    private function addResponse() {
        $response_text = trim($_POST['response_text']);
        if (!empty($response_text)) {
            try {
                $this->model->addResponse($this->reclamation_id, 1, $response_text);
                
                // Mise à jour du statut si c'est la première réponse
                $count = $this->model->countResponses($this->reclamation_id);
                if ($count == 1) {
                    $this->model->updateStatus($this->reclamation_id, 'in-progress');
                }
                
                $_SESSION['success_message'] = "Réponse ajoutée avec succès.";
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Erreur lors de l'ajout de la réponse : " . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = "Le texte de la réponse ne peut pas être vide.";
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $this->reclamation_id);
        exit();
    }

    private function updateResponse() {
        $response_id = isset($_POST['response_id']) ? intval($_POST['response_id']) : 0;
        $response_text = trim($_POST['response_text']);
        
        if ($response_id > 0 && !empty($response_text)) {
            try {
                $affected = $this->model->updateResponse($response_id, $this->reclamation_id, $response_text);
                
                if ($affected > 0) {
                    $_SESSION['success_message'] = "Réponse modifiée avec succès.";
                } else {
                    $_SESSION['error_message'] = "Aucune modification effectuée ou réponse non trouvée.";
                }
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Erreur lors de la modification de la réponse : " . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = "Données invalides pour la modification.";
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $this->reclamation_id);
        exit();
    }

    private function handleDeleteResponse() {
        $response_id = intval($_GET['delete_response']);
        
        if ($response_id > 0) {
            try {
                $affected = $this->model->deleteResponse($response_id, $this->reclamation_id);
                
                if ($affected > 0) {
                    $_SESSION['success_message'] = "Réponse supprimée avec succès.";
                    
                    // Vérifier s'il reste des réponses et mettre à jour le statut si nécessaire
                    $count = $this->model->countResponses($this->reclamation_id);
                    if ($count == 0) {
                        $this->model->updateStatus($this->reclamation_id, 'new');
                    }
                } else {
                    $_SESSION['error_message'] = "Aucune réponse trouvée à supprimer.";
                }
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Erreur lors de la suppression de la réponse : " . $e->getMessage();
            }
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $this->reclamation_id);
        exit();
    }

    private function handleStatusChange() {
        $new_status = $_GET['change_status'];
        $allowed_statuses = ['new', 'in-progress', 'resolved', 'rejected'];
        
        if (in_array($new_status, $allowed_statuses)) {
            try {
                $this->model->updateStatus($this->reclamation_id, $new_status);
                $_SESSION['success_message'] = "Statut de la réclamation mis à jour avec succès.";
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour du statut : " . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = "Statut invalide.";
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $this->reclamation_id);
        exit();
    }
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'skillboost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $controller = new ReclamationController($pdo);
    $controller->handleRequest();
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>