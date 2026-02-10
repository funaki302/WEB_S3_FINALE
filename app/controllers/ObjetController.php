<?php
namespace app\controllers;
use app\models\Objet;
use Flight;
// controllers/ObjetController.php
class ObjetController {

    private $objetModel;
    private $objetImgModel;

    public function __construct() {
        $this->objetModel    = new Objet();
    }

    /* public function create($postData, $files = []) {
        $data = [
            'id_proprietaire' => $_SESSION['user_id'], // à adapter
            'id_categorie'    => $postData['categorie'],
            'title'           => trim($postData['title']),
            'description'     => trim($postData['description']),
            'prix_estime'     => $postData['prix_estime'] ?? null,
        ];

        $id_objet = $this->objetModel->create($data);
        if (!$id_objet) {
            return ['success' => false];
        }

        // Gestion des images
        if (!empty($files['images'])) {
            foreach ($files['images'] as $img) {
                // À adapter selon ta logique d'upload
                $filename = $this->uploadImage($img); 
                if ($filename) {
                    $this->objetImgModel->addImage($id_objet, $filename);
                }
            }
        }

        return ['success' => true, 'id_objet' => $id_objet];
    }

    public function uploadImage($file) {
        // logique d'upload à implémenter (move_uploaded_file, vérif type/taille, etc.)
        // retourne le nom du fichier ou chemin relatif
        return 'uploads/objets/' . uniqid() . '_' . basename($file['name']);
    }

    public function getMyObjects() {
        return $this->objetModel->getAllByUser($_SESSION['user_id']);
    } */

    public function getObjet_User($id_user){
        return $this->objetModel->getObjet_User($id_user);
    }

    public function getObjetsNotOwnedByCurrentUser($limit = 50, $offset = 0) {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return [];
        }
        return $this->objetModel->getAllNotOwnedByUser($userId, (int)$limit, (int)$offset);
    }

    public function getObjetsNotOwnedByCurrentUserJson() {
        $limit = Flight::request()->query['limit'] ?? 50;
        $offset = Flight::request()->query['offset'] ?? 0;
        return $this->getObjetsNotOwnedByCurrentUser((int)$limit, (int)$offset);
    }

    public function getObjetsNotOwnedByCurrentUserSearchJson() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return [];
        }

        $keyword = Flight::request()->query['keyword'] ?? '';
        $categoryId = Flight::request()->query['categorie'] ?? null;
        $limit = Flight::request()->query['limit'] ?? 60;
        $offset = Flight::request()->query['offset'] ?? 0;

        return $this->objetModel->searchNotOwnedByUser((int)$userId, $keyword, $categoryId, (int)$limit, (int)$offset);
    }

    public function getCategoriesJson() {
        return $this->objetModel->getAllCategories();
    }

    public function getCount() {
        return $this->objetModel->getCount();
    }

    public function getCountExchanges() {
        return $this->objetModel->getCountExchanges();
    }
}