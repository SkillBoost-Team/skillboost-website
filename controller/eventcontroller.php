<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/event.php';

class EvenementController
{
    public function listEvenements()
    {
        $sql = "SELECT * FROM evenement"; // Assurez-vous que la table a bien les nouveaux champs
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addEvenement($evenement)
    {
        $sql = "INSERT INTO evenement (titre, description, date_evenement, type_evenement, lieu_ou_lien, statut)  
                VALUES (:titre, :description, :date_evenement, :type_evenement, :lieu_ou_lien, :statut)";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $evenement->getTitre(),
                'description' => $evenement->getDescription(),
                'date_evenement' => $evenement->getDateEvenement(),
                'type_evenement' => $evenement->getTypeEvenement(),
                'lieu_ou_lien' => $evenement->getLieuOuLien(),
                'statut' => $evenement->getStatut(),
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function deleteEvenement($id)
    {
        $sql = "DELETE FROM evenement WHERE idevenement = :idevenement";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':idevenement', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function updateEvenement($id, $data)
    {
        $db = config::getConnexion();
        $sql = "UPDATE evenement 
                SET titre = :titre, description = :description, date_evenement = :date_evenement, 
                    type_evenement = :type_evenement, lieu_ou_lien = :lieu_ou_lien, statut = :statut 
                WHERE idevenement = :idevenement";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'titre' => $data['titre'],
                'description' => $data['description'],
                'date_evenement' => $data['date_evenement'],
                'type_evenement' => $data['type_evenement'],
                'lieu_ou_lien' => $data['lieu_ou_lien'],
                'statut' => $data['statut'],
                'idevenement' => $id,
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function getEventById($id)
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM evenement WHERE idevenement = :idevenement";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idevenement', $id, PDO::PARAM_INT);
            $stmt->execute();

            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            return $event ? $event : false;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }
}
