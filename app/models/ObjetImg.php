<?php
namespace app\models;

use Flight;
use PDO;
use PDOException;

class ObjetImg {

    private $db;
    private $uploadPath;

    public function __construct() {
        $this->db = Flight::db();
        $this->uploadPath = __DIR__ . '/../../public/uploads/objets/';
    }

    public function create($idObjet, $imageFile) {
        error_log('ObjetImg::create - début avec idObjet=' . $idObjet);
        error_log('ObjetImg::create - imageFile=' . print_r($imageFile, true));
        
        if (!isset($imageFile['tmp_name']) || !is_uploaded_file($imageFile['tmp_name'])) {
            error_log('ObjetImg::create - fichier invalide ou non uploadé');
            return ['ok' => false, 'error' => 'Fichier invalide'];
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $imageFile['type'] ?? '';
        error_log('ObjetImg::create - type fichier=' . $fileType);
        
        if (!in_array($fileType, $allowedTypes)) {
            error_log('ObjetImg::create - type non autorisé');
            return ['ok' => false, 'error' => 'Type de fichier non autorisé'];
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($imageFile['size'] > $maxSize) {
            error_log('ObjetImg::create - fichier trop volumineux: ' . $imageFile['size']);
            return ['ok' => false, 'error' => 'Fichier trop volumineux (max 5MB)'];
        }

        $extension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('obj_', true) . '.' . $extension;
        $filePath = $this->uploadPath . $fileName;
        
        error_log('ObjetImg::create - chemin upload=' . $filePath);
        error_log('ObjetImg::create - uploadPath existe? ' . (is_dir($this->uploadPath) ? 'oui' : 'non'));
        error_log('ObjetImg::create - uploadPath writable? ' . (is_writable($this->uploadPath) ? 'oui' : 'non'));

        if (!move_uploaded_file($imageFile['tmp_name'], $filePath)) {
            error_log('ObjetImg::create - échec move_uploaded_file');
            return ['ok' => false, 'error' => 'Erreur lors de l\'upload'];
        }

        error_log('ObjetImg::create - fichier déplacé avec succès');

        try {
            $stmt = $this->db->prepare("
                INSERT INTO tk_objet_img (id_objet, image) 
                VALUES (:id_objet, :image)
            ");
            $stmt->bindValue(':id_objet', (int)$idObjet, PDO::PARAM_INT);
            $stmt->bindValue(':image', $fileName, PDO::PARAM_STR);
            $stmt->execute();
            
            $id = (int)$this->db->lastInsertId();
            error_log('ObjetImg::create - insertion BDD réussie, id=' . $id);
            
            return ['ok' => true, 'id_objet_img' => $id];
        } catch (PDOException $e) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            error_log('Error in ObjetImg::create - ' . $e->getMessage());
            return ['ok' => false, 'error' => 'Erreur base de données'];
        }
    }

    public function getByObjet($idObjet) {
        $stmt = $this->db->prepare("
            SELECT * FROM tk_objet_img 
            WHERE id_objet = ? 
            ORDER BY id_objet_img ASC
        ");
        $stmt->execute([(int)$idObjet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFirstByObjet($idObjet) {
        $stmt = $this->db->prepare("
            SELECT * FROM tk_objet_img 
            WHERE id_objet = ? 
            ORDER BY id_objet_img ASC 
            LIMIT 1
        ");
        $stmt->execute([(int)$idObjet]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($idObjetImg) {
        try {
            $stmt = $this->db->prepare("SELECT image FROM tk_objet_img WHERE id_objet_img = ?");
            $stmt->execute([(int)$idObjetImg]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($img && file_exists($this->uploadPath . $img['image'])) {
                unlink($this->uploadPath . $img['image']);
            }

            $stmt = $this->db->prepare("DELETE FROM tk_objet_img WHERE id_objet_img = ?");
            return $stmt->execute([(int)$idObjetImg]);
        } catch (PDOException $e) {
            error_log('Error in ObjetImg::delete - ' . $e->getMessage());
            return false;
        }
    }

    public function deleteByObjet($idObjet) {
        try {
            $images = $this->getByObjet($idObjet);
            
            foreach ($images as $img) {
                if (file_exists($this->uploadPath . $img['image'])) {
                    unlink($this->uploadPath . $img['image']);
                }
            }

            $stmt = $this->db->prepare("DELETE FROM tk_objet_img WHERE id_objet = ?");
            return $stmt->execute([(int)$idObjet]);
        } catch (PDOException $e) {
            error_log('Error in ObjetImg::deleteByObjet - ' . $e->getMessage());
            return false;
        }
    }
}
