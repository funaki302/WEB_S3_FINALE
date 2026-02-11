<?php
namespace app\controllers;

use app\models\ObjetHistory;
use Flight;

class ObjetHistoryController {

    private $objetHistoryModel;

    public function __construct() {
        $this->objetHistoryModel = new ObjetHistory();
    }

    public function getAllJson() {
        $histories = $this->objetHistoryModel->getAll();
        Flight::json(['ok' => true, 'data' => $histories]);
    }

    public function getByIdJson($id) {
        $history = $this->objetHistoryModel->getById($id);
        if ($history) {
            Flight::json(['ok' => true, 'data' => $history]);
        } else {
            Flight::json(['ok' => false, 'error' => 'Historique non trouvé'], 404);
        }
    }

    public function createJson() {
        $body = Flight::request()->data->getData();
        
        $idObjet = isset($body['id_objet']) ? (int)$body['id_objet'] : 0;
        $idProprietaire = isset($body['id_proprietaire']) ? (int)$body['id_proprietaire'] : 0;
        $idEchange = isset($body['id_echange']) ? (int)$body['id_echange'] : 0;
        $dateEchange = $body['date_echange'] ?? null;

        if ($idObjet <= 0 || $idProprietaire <= 0 || $idEchange <= 0) {
            Flight::json(['ok' => false, 'error' => 'Paramètres invalides'], 400);
            return;
        }

        $id = $this->objetHistoryModel->insert($idObjet, $idProprietaire, $idEchange, $dateEchange);
        if ($id) {
            Flight::json(['ok' => true, 'id_objet_history' => $id]);
        } else {
            Flight::json(['ok' => false, 'error' => 'Erreur lors de la création'], 500);
        }
    }

    public function updateJson($id) {
        $body = Flight::request()->data->getData();
        
        $idObjet = isset($body['id_objet']) ? (int)$body['id_objet'] : null;
        $idProprietaire = isset($body['id_proprietaire']) ? (int)$body['id_proprietaire'] : null;
        $idEchange = isset($body['id_echange']) ? (int)$body['id_echange'] : null;
        $dateEchange = $body['date_echange'] ?? null;

        $success = $this->objetHistoryModel->update($id, $idObjet, $idProprietaire, $idEchange, $dateEchange);
        if ($success) {
            Flight::json(['ok' => true]);
        } else {
            Flight::json(['ok' => false, 'error' => 'Erreur lors de la mise à jour ou historique non trouvé'], 500);
        }
    }

    public function deleteJson($id) {
        $success = $this->objetHistoryModel->delete($id);
        if ($success) {
            Flight::json(['ok' => true]);
        } else {
            Flight::json(['ok' => false, 'error' => 'Erreur lors de la suppression ou historique non trouvé'], 500);
        }
    }

    public function getByObjetJson($idObjet) {
        $histories = $this->objetHistoryModel->getByObjet($idObjet);
        Flight::json(['ok' => true, 'data' => $histories]);
    }

    public function getByProprietaireJson($idProprietaire) {
        $histories = $this->objetHistoryModel->getByProprietaire($idProprietaire);
        Flight::json(['ok' => true, 'data' => $histories]);
    }

    public function getByEchangeJson($idEchange) {
        $histories = $this->objetHistoryModel->getByEchange($idEchange);
        Flight::json(['ok' => true, 'data' => $histories]);
    }

    public function getObjetHistory($idObjet) {
        return $this->objetHistoryModel->getObjetHistory($idObjet);
    }
}
