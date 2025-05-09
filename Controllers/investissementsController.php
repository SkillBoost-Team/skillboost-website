<?php
require_once __DIR__ . '/../models/investissement.php';
require_once __DIR__ . '/../config/config.php';

class InvestissementsController {
    private $db;
    private $investissementModel;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->investissementModel = new Investissement($this->db);
    }

    public function index() {
        try {
            // Initialiser les variables avec des valeurs par défaut
            $data = [
                'investissements' => [],
                'total_investissements' => 0,
                'investissements_actifs' => 0,
                'investissements_en_attente' => 0,
                'investissements_annules' => 0,
                'revenus_projets' => []
            ];

            // Récupérer tous les investissements
            $query = "SELECT 
                        i.*,
                        p.titre as titre_projet,
                        p.id_createur,
                        creator.nom as nom_createur,
                        creator.prenom as prenom_createur,
                        investor.nom as nom_investisseur,
                        investor.prenom as prenom_investisseur,
                        i.date_investissement as date_creation
                     FROM investissement i
                     JOIN projet p ON i.id_projet = p.id
                     JOIN utilisateur creator ON p.id_createur = creator.id
                     JOIN utilisateur investor ON i.id_investisseur = investor.id
                     ORDER BY i.date_investissement DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $data['investissements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculer les statistiques des investissements
            if (!empty($data['investissements'])) {
                foreach ($data['investissements'] as $investissement) {
                    $data['total_investissements'] += $investissement['montant'];
                    switch ($investissement['statut']) {
                        case 'Accepté':
                            $data['investissements_actifs']++;
                            break;
                        case 'Proposé':
                            $data['investissements_en_attente']++;
                            break;
                        case 'Refusé':
                            $data['investissements_annules']++;
                            break;
                    }
                }
            }

            // Récupérer tous les revenus des projets
            $query = "SELECT 
                        r.*,
                        p.titre as titre_projet
                     FROM revenu_projet r
                     JOIN projet p ON r.id_projet = p.id
                     ORDER BY r.date_revenu DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $data['revenus_projets'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [
                'investissements' => [],
                'total_investissements' => 0,
                'investissements_actifs' => 0,
                'investissements_en_attente' => 0,
                'investissements_annules' => 0,
                'revenus_projets' => [],
                'error' => "Une erreur est survenue lors de la récupération des données."
            ];
        }
    }

    public function delete() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Méthode non autorisée');
            }

            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($id <= 0) {
                throw new Exception('ID invalide');
            }

            // Vérifier si l'investissement existe
            $query = "SELECT * FROM investissement WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                throw new Exception('Investissement non trouvé');
            }

            // Supprimer l'investissement
            $query = "DELETE FROM investissement WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Erreur lors de la suppression');
            }

            // Récupérer les nouveaux compteurs
            $data = $this->index();
            
            echo json_encode([
                'success' => true,
                'message' => 'Investissement supprimé avec succès',
                'counters' => [
                    'total_investissements' => $data['total_investissements'],
                    'investissements_actifs' => $data['investissements_actifs'],
                    'investissements_en_attente' => $data['investissements_en_attente'],
                    'investissements_annules' => $data['investissements_annules']
                ]
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function update() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            // Récupérer les données JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                throw new Exception('Données invalides');
            }

            // Valider les données
            $id = isset($data['id']) ? (int)$data['id'] : 0;
            $montant = isset($data['montant']) ? (float)$data['montant'] : 0;
            $pourcentage = isset($data['pourcentage']) ? (float)$data['pourcentage'] : 0;
            $statut = isset($data['statut']) ? $data['statut'] : '';
            $date_investissement = isset($data['date_investissement']) ? $data['date_investissement'] : null;

            if ($id <= 0) {
                throw new Exception('ID invalide');
            }
            if ($montant <= 0) {
                throw new Exception('Le montant doit être supérieur à 0');
            }
            if ($pourcentage <= 0 || $pourcentage > 100) {
                throw new Exception('Le pourcentage doit être entre 0 et 100');
            }
            if (!in_array($statut, ['Proposé', 'Accepté', 'Refusé'])) {
                throw new Exception('Statut invalide');
            }
            if (!$date_investissement) {
                throw new Exception('Date invalide');
            }

            // Vérifier si l'investissement existe
            $query = "SELECT * FROM investissement WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                throw new Exception('Investissement non trouvé');
            }

            // Mettre à jour l'investissement
            $query = "UPDATE investissement 
                     SET montant = :montant, 
                         pourcentage = :pourcentage, 
                         statut = :statut,
                         date_investissement = :date_investissement
                     WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':montant', $montant);
            $stmt->bindParam(':pourcentage', $pourcentage);
            $stmt->bindParam(':statut', $statut);
            $stmt->bindParam(':date_investissement', $date_investissement);
            
            if (!$stmt->execute()) {
                throw new Exception('Erreur lors de la mise à jour');
            }

            // Récupérer les nouveaux compteurs
            $data = $this->index();
            
            echo json_encode([
                'success' => true,
                'message' => 'Investissement modifié avec succès',
                'counters' => [
                    'total_investissements' => $data['total_investissements'],
                    'investissements_actifs' => $data['investissements_actifs'],
                    'investissements_en_attente' => $data['investissements_en_attente'],
                    'investissements_annules' => $data['investissements_annules']
                ]
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function updateRevenu() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                throw new Exception('Données invalides');
            }

            // Valider les données
            $id = isset($data['id']) ? (int)$data['id'] : 0;
            $montant = isset($data['montant']) ? (float)$data['montant'] : 0;
            $date_revenu = isset($data['date_revenu']) ? $data['date_revenu'] : null;
            $description = isset($data['description']) ? $data['description'] : '';

            if ($id <= 0) {
                throw new Exception('ID invalide');
            }
            if ($montant <= 0) {
                throw new Exception('Le montant doit être supérieur à 0');
            }
            if (!$date_revenu) {
                throw new Exception('Date invalide');
            }

            // Mettre à jour le revenu
            $query = "UPDATE revenu_projet 
                     SET montant = :montant,
                         date_revenu = :date_revenu,
                         description = :description
                     WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':montant', $montant);
            $stmt->bindParam(':date_revenu', $date_revenu);
            $stmt->bindParam(':description', $description);
            
            if (!$stmt->execute()) {
                throw new Exception('Erreur lors de la mise à jour du revenu');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Revenu modifié avec succès'
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function deleteRevenu() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Méthode non autorisée');
            }

            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($id <= 0) {
                throw new Exception('ID invalide');
            }

            // Vérifier si le revenu existe
            $query = "SELECT * FROM revenu_projet WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                throw new Exception('Revenu non trouvé');
            }

            // Supprimer le revenu
            $query = "DELETE FROM revenu_projet WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Erreur lors de la suppression');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Revenu supprimé avec succès'
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
}

// Router simple pour gérer les actions
if (isset($_GET['action'])) {
    $controller = new InvestissementsController();
    switch ($_GET['action']) {
        case 'delete':
            $controller->delete();
            break;
        case 'update':
            $controller->update();
            break;
        case 'updateRevenu':
            $controller->updateRevenu();
            break;
        case 'deleteRevenu':
            $controller->deleteRevenu();
            break;
    }
} 