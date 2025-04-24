<?php
require_once __DIR__.'/../models/Claim.php';
require_once __DIR__.'/../models/Reward.php';

class ClaimsController {
    public function submit() {
        try {
            $claimModel = new Claim();
            $rewardModel = new Reward();
            
            $claimId = $claimModel->create($_SESSION['user_id'], [
                'description' => $_POST['description'],
                'is_constructive' => $this->isConstructive($_POST['description'])
            ]);

            if (!empty($_FILES['media'])) {
                $filePath = $this->processUpload($_FILES['media']);
                $claimModel->addMedia($claimId, $filePath, $_FILES['media']['type']);
                $rewardModel->addMediaBonus($_SESSION['user_id']);
            }

            header('Location: /claim-success');
        } catch (Exception $e) {
            error_log($e->getMessage());
            header('Location: /claim-error');
        }
    }

    private function isConstructive($text) {
        // Implémentez votre logique d'analyse ici
        return (str_word_count($text) > 50;
    }

    private function processUpload($file) {
        $uploadDir = __DIR__.'/../uploads/';
        $fileName = uniqid().'_'.basename($file['name']);
        move_uploaded_file($file['tmp_name'], $uploadDir.$fileName);
        return '/uploads/'.$fileName;
    }
}