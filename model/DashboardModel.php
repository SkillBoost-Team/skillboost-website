<?php
class DashboardModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all formations from the database
    public function getAllFormations() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM formation ORDER BY date_creation DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching formations: ' . $e->getMessage());
        }
    }

    // Fetch a single formation by ID
    public function getFormationById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM formation WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching formation: ' . $e->getMessage());
        }
    }

    // Update a formation in the database
    public function updateFormation($id, $titre, $description, $duree, $niveau, $certificat) {
        try {
            $stmt = $this->pdo->prepare("UPDATE formation SET titre = :titre, description = :description, duree = :duree, niveau = :niveau, certificat = :certificat WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':duree', $duree, PDO::PARAM_INT);
            $stmt->bindParam(':niveau', $niveau);
            $stmt->bindParam(':certificat', $certificat);
            $stmt->execute();
            return 1;
        } catch (PDOException $e) {
            die('Error updating formation: ' . $e->getMessage());
        }
    }

    // Delete a formation from the database
    public function deleteFormation($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM formation WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Error deleting formation: ' . $e->getMessage());
        }
    }

    // Add a new quiz to the database
    public function addQuiz($formationId, $question1, $answer1, $question2, $answer2, $question3, $answer3) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO quiz (id_formation, question1, answer1, question2, answer2, question3, answer3)
                VALUES (:id_formation, :question1, :answer1, :question2, :answer2, :question3, :answer3)
            ");
            $stmt->bindParam(':id_formation', $formationId, PDO::PARAM_INT);
            $stmt->bindParam(':question1', $question1);
            $stmt->bindParam(':answer1', $answer1, PDO::PARAM_BOOL);
            $stmt->bindParam(':question2', $question2);
            $stmt->bindParam(':answer2', $answer2, PDO::PARAM_BOOL);
            $stmt->bindParam(':question3', $question3);
            $stmt->bindParam(':answer3', $answer3, PDO::PARAM_BOOL);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Error adding quiz: ' . $e->getMessage());
        }
    }

    // Get quiz by formation ID
    public function getQuizByFormationId($formationId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM quiz WHERE id_formation = :id_formation");
            $stmt->bindParam(':id_formation', $formationId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching quiz: ' . $e->getMessage());
        }
    }

    // Get quiz by ID
    public function getQuizById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM quiz WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching quiz: ' . $e->getMessage());
        }
    }

    // Update a quiz in the database
    public function updateQuiz($quizId, $question1, $answer1, $question2, $answer2, $question3, $answer3) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE quiz 
                SET question1 = :question1, answer1 = :answer1, 
                    question2 = :question2, answer2 = :answer2, 
                    question3 = :question3, answer3 = :answer3 
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $quizId, PDO::PARAM_INT);
            $stmt->bindParam(':question1', $question1);
            $stmt->bindParam(':answer1', $answer1, PDO::PARAM_BOOL);
            $stmt->bindParam(':question2', $question2);
            $stmt->bindParam(':answer2', $answer2, PDO::PARAM_BOOL);
            $stmt->bindParam(':question3', $question3);
            $stmt->bindParam(':answer3', $answer3, PDO::PARAM_BOOL);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Error updating quiz: ' . $e->getMessage());
        }
    }

    // Delete a quiz from the database
    public function deleteQuiz($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM quiz WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Error deleting quiz: ' . $e->getMessage());
        }
    }

    //add 
    public function addFormation($titre, $description, $duree, $niveau, $certificat) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO formation (titre, description, duree, niveau, certificat, date_creation)
                VALUES (:titre, :description, :duree, :niveau, :certificat, CURRENT_TIMESTAMP)
            ");
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':duree', $duree, PDO::PARAM_INT);
            $stmt->bindParam(':niveau', $niveau);
            $stmt->bindParam(':certificat', $certificat);
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            die('Error adding formation: ' . $e->getMessage());
        }
    }

    public function filterFormations($filters) {

        $sql = "SELECT * FROM formation WHERE 1=1";
        $params = [];

        // Validate and add titre filter
        if (!empty($filters['titre'])) {
            $sql .= " AND titre LIKE :titre";
            $params[':titre'] = '%' . $filters['titre'] . '%';
        }

        // Validate and add niveau filter
        if (!empty($filters['niveau'])) {
            $sql .= " AND niveau = :niveau";
            $params[':niveau'] = $filters['niveau'];
        }

        // Validate and add date_creation filter
        if (!empty($filters['date_creation'])) {
            $sql .= " AND DATE(date_creation) = :date_creation";
            $params[':date_creation'] = $filters['date_creation'];
        }

        // Prepare and execute the query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}