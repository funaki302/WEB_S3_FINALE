<?php
namespace app\controllers;

use app\models\Exchange;
use app\models\Objet;
use Flight;

class ExchangeController {

    private $exchangeModel;
    private $objetModel;

    public function __construct() {
        $this->exchangeModel = new Exchange();
        $this->objetModel = new Objet();
    }

    public function getTargetObjetJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return null;
        }

        $id = Flight::request()->query['id'] ?? null;
        if (!$id) {
            return null;
        }

        return $this->objetModel->findById((int)$id);
    }

    public function getMyObjetsJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return [];
        }

        return $this->objetModel->getAllByUser((int)$userId, 200, 0);
    }

    public function createExchangeJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return ['ok' => false, 'error' => 'Non connecté'];
        }

        $body = Flight::request()->data->getData();
        $objetProposer = isset($body['objet_proposer']) ? (int)$body['objet_proposer'] : 0;
        $objetRequise = isset($body['objet_requise']) ? (int)$body['objet_requise'] : 0;

        if ($objetProposer <= 0 || $objetRequise <= 0) {
            return ['ok' => false, 'error' => 'Paramètres invalides'];
        }

        if ($this->exchangeModel->hasPendingExchangeForObject($objetProposer)) {
            return ['ok' => false, 'error' => 'Ton objet est déjà engagé dans un échange en attente'];
        }

        $proposerObj = $this->objetModel->findById($objetProposer);
        $requiseObj = $this->objetModel->findById($objetRequise);

        if (!$proposerObj || !$requiseObj) {
            return ['ok' => false, 'error' => 'Objet introuvable'];
        }

        if ((int)$proposerObj['id_proprietaire'] !== (int)$userId) {
            return ['ok' => false, 'error' => 'Cet objet ne t\'appartient pas'];
        }

        if ((int)$requiseObj['id_proprietaire'] === (int)$userId) {
            return ['ok' => false, 'error' => 'Tu ne peux pas échanger avec ton propre objet'];
        }

        $idReceveur = (int)$requiseObj['id_proprietaire'];

        $newId = $this->exchangeModel->create((int)$userId, $idReceveur, $objetProposer, $objetRequise);
        return ['ok' => true, 'id_echange' => $newId];
    }

    public function getReceivedExchangesJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return [];
        }

        $status = Flight::request()->query['status'] ?? null;
        return $this->exchangeModel->getReceivedByUser((int)$userId, $status);
    }

    public function acceptExchangeJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return ['ok' => false, 'error' => 'Non connecté'];
        }

        $body = Flight::request()->data->getData();
        $idEchange = isset($body['id_echange']) ? (int)$body['id_echange'] : 0;
        if ($idEchange <= 0) {
            return ['ok' => false, 'error' => 'Paramètres invalides'];
        }

        return $this->exchangeModel->acceptExchange($idEchange, (int)$userId);
    }

    public function refuseExchangeJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return ['ok' => false, 'error' => 'Non connecté'];
        }

        $body = Flight::request()->data->getData();
        $idEchange = isset($body['id_echange']) ? (int)$body['id_echange'] : 0;
        if ($idEchange <= 0) {
            return ['ok' => false, 'error' => 'Paramètres invalides'];
        }

        $ex = $this->exchangeModel->getById($idEchange);
        if (!$ex) {
            return ['ok' => false, 'error' => 'Echange introuvable'];
        }
        if ((int)$ex['id_receveur'] !== (int)$userId) {
            return ['ok' => false, 'error' => 'Non autorisé'];
        }
        if ($ex['status'] !== 'attente') {
            return ['ok' => false, 'error' => 'Echange déjà traité'];
        }

        $this->exchangeModel->updateStatus($idEchange, 'refuser');
        return ['ok' => true];
    }

    public function EchangesAttente($id_user){
        return $this->exchangeModel->EchangeAttente($id_user);
    }

	public function getAllWithRequestedObjectDetails($status = null) {
		return $this->exchangeModel->getAllWithRequestedObjectDetails($status);
	}

	public function getReceivedStatsByUser($userId) {
		return $this->exchangeModel->getReceivedStatsByUser((int)$userId);
	}

	public function getSentStatsByUser($userId) {
		return $this->exchangeModel->getSentStatsByUser((int)$userId);
	}

	public function getStatusStats() {
		return $this->exchangeModel->getStatusStats();
	}
}
