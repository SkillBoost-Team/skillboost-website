<?php
require_once __DIR__ . '/../models/investissement.php';
require_once __DIR__ . '/../models/projet.php';

class InvestissementController {
    private $db;
    private static $instance = null;

    private function __construct($db) {
        $this->db = $db;
    }

    public static function getInstance($db) {
        if (self::$instance === null) {
            self::$instance = new self($db);
        }
        return self::$instance;
    }

    public function index() {
        try {
            // Afficher tous les projets actifs, même ceux du créateur
            $query = "SELECT p.*, 
                     COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_actuel,
                     p.montant - COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_restant,
                     u.nom as nom_createur,
                     u.prenom as prenom_createur
                     FROM projet p
                     LEFT JOIN investissement i ON p.id = i.id_projet
                     LEFT JOIN utilisateur u ON p.id_createur = u.id
                     WHERE p.statut = 'En cours'
                     GROUP BY p.id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function getMontantInvesti($id_projet, $id_investisseur) {
        try {
            $query = "SELECT COALESCE(SUM(montant), 0) as montant_investi 
                     FROM investissement 
                     WHERE id_projet = :id_projet 
                     AND id_investisseur = :id_investisseur";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_projet', $id_projet);
            $stmt->bindParam(':id_investisseur', $id_investisseur);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['montant_investi'];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function store($postData) {
        try {
            // 1. Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Vous devez être connecté pour investir");
            }

            // 2. Vérifier si le projet existe et récupérer ses informations
            $query = "SELECT p.*, 
                     COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_investi
                     FROM projet p
                     LEFT JOIN investissement i ON p.id = i.id_projet AND i.statut = 'Accepté'
                     WHERE p.id = :id_projet
                     GROUP BY p.id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_projet', $postData['id_projet'], PDO::PARAM_INT);
            $stmt->execute();
            $projet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$projet) {
                throw new Exception("Projet non trouvé");
            }

            // 3. Vérifier que l'utilisateur n'est pas le créateur du projet
            if ($projet['id_createur'] == $_SESSION['user_id']) {
                throw new Exception("Vous ne pouvez pas investir dans votre propre projet");
            }

            // 4. Calculer le montant restant à investir
            $montant_restant = $projet['montant'] - $projet['montant_investi'];

            // 5. Vérifier que le montant est valide
            if (!is_numeric($postData['montant']) || $postData['montant'] <= 0 || $postData['montant'] > $montant_restant) {
                throw new Exception("Le montant d'investissement doit être entre 1 et " . number_format($montant_restant, 2) . " DT");
            }

            // 5b. Vérifier que le pourcentage est valide
            if (!isset($postData['pourcentage']) || !is_numeric($postData['pourcentage']) || $postData['pourcentage'] < 1 || $postData['pourcentage'] > 100) {
                throw new Exception("Le pourcentage doit être entre 1 et 100");
            }

            // 6. Insérer l'investissement
            $query = "INSERT INTO investissement (id_projet, id_investisseur, montant, pourcentage, statut, date_investissement) 
                     VALUES (:id_projet, :id_investisseur, :montant, :pourcentage, 'Proposé', NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_projet', $postData['id_projet'], PDO::PARAM_INT);
            $stmt->bindParam(':id_investisseur', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':montant', $postData['montant'], PDO::PARAM_STR);
            $stmt->bindParam(':pourcentage', $postData['pourcentage'], PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'enregistrement de l'investissement");
            }
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getHistoriqueInvestissements($id_investisseur) {
        try {
            $query = "SELECT i.*, p.titre as projet_titre 
                     FROM investissement i 
                     JOIN projet p ON i.id_projet = p.id 
                     WHERE i.id_investisseur = :id_investisseur 
                     ORDER BY i.date_investissement DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_investisseur', $id_investisseur);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getMesProjetsEtInvestissements($id_createur) {
        try {
            // Récupérer les projets avec leurs investissements
            $query = "SELECT 
                        p.id as projet_id,
                        p.titre,
                        p.description,
                        p.montant as montant_objectif,
                        p.statut as statut_projet,
                        p.date_creation,
                        COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_actuel,
                        COUNT(DISTINCT i.id) as nombre_investissements,
                        COUNT(DISTINCT CASE WHEN i.statut = 'Proposé' THEN i.id END) as investissements_en_attente
                     FROM projet p
                     LEFT JOIN investissement i ON p.id = i.id_projet
                     WHERE p.id_createur = :id_createur
                     GROUP BY p.id, p.titre, p.description, p.montant, p.statut, p.date_creation
                     ORDER BY p.date_creation DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_createur', $id_createur, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getInvestissementsPourProjet($id_projet) {
        try {
            $query = "SELECT 
                        i.*,
                        u.nom as nom_investisseur,
                        u.prenom as prenom_investisseur,
                        p.id_createur
                     FROM investissement i
                     JOIN utilisateur u ON i.id_investisseur = u.id
                     JOIN projet p ON i.id_projet = p.id
                     WHERE i.id_projet = :id_projet
                     ORDER BY i.date_investissement DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function hasProjects($user_id) {
        try {
            $query = "SELECT COUNT(*) as count FROM projet WHERE id_createur = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateStatutInvestissement(int $id_investissement, string $nouveau_statut, int $id_createur): bool {
        try {
            // 1) Vérifier que l'investissement appartient à un projet du créateur
            $sql = "
                SELECT i.id 
                FROM investissement i
                JOIN projet p ON i.id_projet = p.id
                WHERE i.id = :id_investissement
                  AND p.id_createur = :id_createur
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_investissement' => $id_investissement,
                ':id_createur'       => $id_createur
            ]);
            if (!$stmt->fetch()) {
                throw new Exception("Investissement non trouvé ou non autorisé");
            }

            // 2) Si Refusé → mise à jour du statut
            if ($nouveau_statut === 'Refusé') {
                $upd = $this->db->prepare("
                    UPDATE investissement
                       SET statut = :statut
                     WHERE id = :id
                ");
                return $upd->execute([
                    ':statut' => $nouveau_statut,
                    ':id'     => $id_investissement
                ]);
            }

            // 3) Si Accepté → mise à jour du statut
            if ($nouveau_statut === 'Accepté') {
                $upd = $this->db->prepare("
                    UPDATE investissement
                       SET statut = :statut
                     WHERE id = :id
                ");
                return $upd->execute([
                    ':statut' => $nouveau_statut,
                    ':id'     => $id_investissement
                ]);
            }

            return false;
        } catch (Exception $e) {
            error_log("InvestissementController::updateStatutInvestissement : " . $e->getMessage());
            throw $e;
        }
    }
    
    public function getAllInvestissements() {
        try {
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getInvestissementById($id) {
        try {
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
                     WHERE i.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function updateRevenusProjet() {
        try {
            // 1. Récupérer tous les projets qui ont atteint leur objectif
            $sql = "
                SELECT 
                    p.id as projet_id,
                    p.montant as montant_objectif,
                    COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_actuel,
                    MAX(CASE WHEN i.statut = 'Accepté' THEN i.date_investissement END) as date_completion
                FROM projet p
                LEFT JOIN investissement i ON p.id = i.id_projet
                GROUP BY p.id, p.montant
                HAVING montant_actuel >= montant_objectif
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $projets_complets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($projets_complets as $projet) {
                if (!$projet['date_completion']) continue;

                // 2. Récupérer la dernière date de revenu existante pour ce projet
                $sql = "
                    SELECT MAX(date_revenu) as derniere_date
                    FROM revenu_projet
                    WHERE id_projet = :projet_id
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':projet_id' => $projet['projet_id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                // Définir la date de début
                $date_debut = $result['derniere_date'] 
                    ? date('Y-m-d', strtotime($result['derniere_date'] . ' +1 month'))
                    : date('Y-m-d', strtotime($projet['date_completion']));

                // 3. Générer les dates mensuelles jusqu'à aujourd'hui
                $date_courante = new DateTime($date_debut);
                $aujourd_hui = new DateTime();

                // Préparer la requête d'insertion
                $insert_sql = "
                    INSERT IGNORE INTO revenu_projet (id_projet, montant, date_revenu)
                    VALUES (:projet_id, NULL, :date_revenu)
                ";
                $insert_stmt = $this->db->prepare($insert_sql);

                // Ajouter une ligne pour chaque mois jusqu'à aujourd'hui
                while ($date_courante <= $aujourd_hui) {
                    $insert_stmt->execute([
                        ':projet_id' => $projet['projet_id'],
                        ':date_revenu' => $date_courante->format('Y-m-d')
                    ]);
                    $date_courante->modify('+1 month');
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour des revenus : " . $e->getMessage());
            return false;
        }
    }

    public function getRevenusProjetComplet($id_createur) {
        try {
            // 1. Récupérer tous les projets complétés du créateur
            $sql = "
                SELECT 
                    p.id as projet_id,
                    p.titre as titre_projet,
                    p.montant as montant_objectif,
                    COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_actuel,
                    MAX(CASE WHEN i.statut = 'Accepté' THEN i.date_investissement END) as date_completion
                FROM projet p
                LEFT JOIN investissement i ON p.id = i.id_projet
                WHERE p.id_createur = :id_createur
                GROUP BY p.id, p.titre, p.montant
                HAVING montant_actuel >= montant_objectif
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_createur' => $id_createur]);
            $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. Pour chaque projet, récupérer ses revenus
            foreach ($projets as &$projet) {
                $sql = "
                    SELECT 
                        rp.*,
                        CASE 
                            WHEN rp.montant IS NULL THEN 'En attente'
                            ELSE 'Renseigné'
                        END as statut_revenu
                    FROM revenu_projet rp
                    WHERE rp.id_projet = :projet_id
                    ORDER BY rp.date_revenu ASC
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':projet_id' => $projet['projet_id']]);
                $projet['revenus'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $projets;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des revenus : " . $e->getMessage());
            return [];
        }
    }

    public function getGainsInvestissements($id_investisseur) {
        try {
            // Récupérer tous les investissements acceptés de l'investisseur dans des projets complétés
            $sql = "
                SELECT 
                    i.id as investissement_id,
                    i.id_projet,
                    i.pourcentage,
                    i.montant as montant_investi,
                    p.titre as titre_projet,
                    p.montant as montant_objectif,
                    COALESCE(SUM(CASE WHEN i2.statut = 'Accepté' THEN i2.montant ELSE 0 END), 0) as montant_total_investi
                FROM investissement i
                JOIN projet p ON i.id_projet = p.id
                LEFT JOIN investissement i2 ON p.id = i2.id_projet
                WHERE i.id_investisseur = :id_investisseur
                AND i.statut = 'Accepté'
                GROUP BY i.id, i.id_projet, i.pourcentage, i.montant, p.titre, p.montant
                HAVING montant_total_investi >= p.montant
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_investisseur' => $id_investisseur]);
            $investissements = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Pour chaque investissement, récupérer les revenus mensuels et calculer les gains
            foreach ($investissements as &$inv) {
                $sql = "
                    SELECT 
                        rp.*,
                        (rp.montant * :pourcentage / 100) as gain_mensuel
                    FROM revenu_projet rp
                    WHERE rp.id_projet = :projet_id
                    AND rp.montant IS NOT NULL
                    ORDER BY rp.date_revenu ASC
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':projet_id' => $inv['id_projet'],
                    ':pourcentage' => $inv['pourcentage']
                ]);
                $revenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Calculer les gains cumulés
                $gains_cumules = 0;
                foreach ($revenus as &$rev) {
                    $gains_cumules += $rev['gain_mensuel'];
                    $rev['gains_cumules'] = $gains_cumules;
                }

                $inv['revenus'] = $revenus;
            }

            return $investissements;
        } catch (Exception $e) {
            error_log("Erreur lors du calcul des gains : " . $e->getMessage());
            return [];
        }
    }
}
