<?php
namespace app\controllers;

use app\models\ObjetImg;
use Flight;

class ObjetImgController {

    private $objetImgModel;

    public function __construct() {
        $this->objetImgModel = new ObjetImg();
    }

    public function uploadImageJson() {
        error_log('uploadImageJson appelé');
        
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            error_log('Upload image: utilisateur non connecté');
            return ['ok' => false, 'error' => 'Non connecté'];
        }

        error_log('Upload image: début du processus');
        error_log('FILES: ' . print_r($_FILES, true));
        error_log('POST: ' . print_r($_POST, true));

        if (!isset($_FILES['image']) || !isset($_POST['id_objet'])) {
            error_log('Upload image: paramètres manquants');
            return ['ok' => false, 'error' => 'Paramètres manquants'];
        }

        $idObjet = (int)$_POST['id_objet'];
        if ($idObjet <= 0) {
            error_log('Upload image: ID objet invalide: ' . $idObjet);
            return ['ok' => false, 'error' => 'ID objet invalide'];
        }

        error_log('Upload image: tentative d\'upload pour objet ' . $idObjet);
        $result = $this->objetImgModel->create($idObjet, $_FILES['image']);
        error_log('Upload image: résultat = ' . print_r($result, true));
        
        return $result;
    }

    public function getImagesByObjetJson() {
        $idObjet = Flight::request()->query['id_objet'] ?? null;
        if (!$idObjet) {
            return [];
        }

        $images = $this->objetImgModel->getByObjet((int)$idObjet);
        
        $baseUrl = '/uploads/objets/';
        foreach ($images as &$img) {
            $img['url'] = $baseUrl . $img['image'];
        }
        
        return $images;
    }

    public function getFirstImageByObjetJson() {
        $idObjet = Flight::request()->query['id_objet'] ?? null;
        if (!$idObjet) {
            return null;
        }

        $image = $this->objetImgModel->getFirstByObjet((int)$idObjet);
        
        if ($image) {
            $image['url'] = '/uploads/objets/' . $image['image'];
        }
        
        return $image;
    }

    public function deleteImageJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return ['ok' => false, 'error' => 'Non connecté'];
        }

        $idObjetImg = Flight::request()->data->getData()['id_objet_img'] ?? null;
        if (!$idObjetImg) {
            return ['ok' => false, 'error' => 'Paramètre manquant'];
        }

        $success = $this->objetImgModel->delete((int)$idObjetImg);
        return ['ok' => $success];
    }

    public function getByObjet($id_objet) {
        return $this->objetImgModel->getByObjet($id_objet);
    }
}
