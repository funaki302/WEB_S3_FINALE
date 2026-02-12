<?php
namespace app\models;

use Flight;
use PDO;
use PDOException;

class Categorie {

    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function create($nom) {
        $sql = "INSERT INTO tk_categorie (nom_categorie) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([$nom]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return -1; // → signifie déjà existant
            }
            error_log("Error in Categorie::create - " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tk_categorie WHERE id_categorie = ? AND date_inactif IS NULL");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error in Categorie::findById - " . $e->getMessage());
            return null;
        }
    }

    public function findByName($nom) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tk_categorie WHERE nom_categorie = ? AND date_inactif IS NULL");
            $stmt->execute([$nom]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error in Categorie::findByName - " . $e->getMessage());
            return null;
        }
    }

    public function getAll($orderBy = 'nom_categorie ASC') {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM tk_categorie 
                WHERE date_inactif IS NULL
                ORDER BY $orderBy
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Categorie::getAll - " . $e->getMessage());
            return [];
        }
    }

    public function getAllWithCount() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    c.id_categorie, 
                    c.nom_categorie,
                    COUNT(o.id_objet) AS nb_objets
                FROM tk_categorie c
                LEFT JOIN tk_objets o ON c.id_categorie = o.id_categorie
                WHERE c.date_inactif IS NULL
                GROUP BY c.id_categorie, c.nom_categorie
                ORDER BY c.nom_categorie ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Categorie::getAllWithCount - " . $e->getMessage());
            return [];
        }
    }

    public function countAll() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tk_categorie WHERE date_inactif IS NULL");
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            error_log("Error in Categorie::countAll - " . $e->getMessage());
            return 0;
        }
    }

    public function update($id, $nouveauNom) {
        $stmt = $this->db->prepare("
            UPDATE tk_categorie 
            SET nom_categorie = ? 
            WHERE id_categorie = ? AND date_inactif IS NULL
        ");
        
        try {
            $success = $stmt->execute([$nouveauNom, $id]);
            return $success;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return -1; 
            }
            error_log("Error in Categorie::update - " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        // On vérifie d'abord s'il y a des objets dans cette catégorie
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tk_objets WHERE id_categorie = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                return -2; // → catégorie non vide → interdiction de suppression
            }

            $stmt = $this->db->prepare("DELETE FROM tk_categorie WHERE id_categorie = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Categorie::delete - " . $e->getMessage());
            return false;
        }
    }

    public function archive($id) {
        try {
            $stmt = $this->db->prepare("UPDATE tk_categorie SET date_inactif = NOW() WHERE id_categorie = ? AND date_inactif IS NULL");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Categorie::archive - " . $e->getMessage());
            return false;
        }
    }

    public function nameExists($nom, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM tk_categorie WHERE nom_categorie = ? AND date_inactif IS NULL";
        $params = [$nom];

        if ($excludeId !== null) {
            $sql .= " AND id_categorie != ?";
            $params[] = $excludeId;
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error in Categorie::nameExists - " . $e->getMessage());
            return false;
        }
    }

    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM tk_categorie WHERE date_inactif IS NULL";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error in Categorie::getCount - " . $e->getMessage());
            return 0;
        }
    }
}