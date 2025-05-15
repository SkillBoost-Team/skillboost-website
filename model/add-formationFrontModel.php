<?php
class AddFormationFrontModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Add a new formation to the database
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
}