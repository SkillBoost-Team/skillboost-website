<?php
class ViewFormationFrontModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch a single formation by ID
    public function getFormationById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM formation WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch a single quiz by formation ID
    public function getQuizByFormationId($formationId) {
        $stmt = $this->pdo->prepare("SELECT * FROM quiz WHERE id_formation = :id_formation");
        $stmt->execute(['id_formation' => $formationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Save quiz results
    public function saveParticipation($userId, $formationId, $quizId, $score, $passed) {
        $query = "INSERT INTO quiz_results (user_id, formation_id, quiz_id, score, passed)
                  VALUES (:user_id, :formation_id, :quiz_id, :score, :passed)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'user_id' => $userId,
            'formation_id' => $formationId,
            'quiz_id' => $quizId,
            'score' => $score,
            'passed' => $passed
        ]);
    }

    public function hasUserPassedQuiz($userId, $quizId) {
        $query = "SELECT * FROM quiz_results WHERE user_id = :user_id AND quiz_id = :quiz_id AND passed = 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $userId, 'quiz_id' => $quizId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['passed'] == 1;
    }

    // Generate a certificate file path
    public function generateCertificatePath($userId) {
        return "../certificates/certificate_user_$userId.pdf";
    }
    

    // Fetch a single quiz by quiz ID
    public function getQuizById($quizId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM quiz WHERE id = :id");
            $stmt->bindParam(':id', $quizId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching quiz: ' . $e->getMessage());
        }
    }
}