<?php
// models/Categorie.php

namespace app\models;

use PDO;
use PDOException;

class Categorie {

    private $db;

    public function __construct($db) {
        $this->db = $db; // PDO instance
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
            return false;
        }
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tk_categorie WHERE id_categorie = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByName($nom) {
        $stmt = $this->db->prepare("SELECT * FROM tk_categorie WHERE nom_categorie = ?");
        $stmt->execute([$nom]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll($orderBy = 'nom_categorie ASC') {
        $stmt = $this->db->prepare("
            SELECT * FROM tk_categorie 
            ORDER BY $orderBy
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithCount() {
        $stmt = $this->db->query("
            SELECT 
                c.id_categorie, 
                c.nom_categorie,
                COUNT(o.id_objet) AS nb_objets
            FROM tk_categorie c
            LEFT JOIN tk_objets o ON c.id_categorie = o.id_categorie
            GROUP BY c.id_categorie, c.nom_categorie
            ORDER BY c.nom_categorie ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tk_categorie");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function update($id, $nouveauNom) {
        $stmt = $this->db->prepare("
            UPDATE tk_categorie 
            SET nom_categorie = ? 
            WHERE id_categorie = ?
        ");
        
        try {
            $success = $stmt->execute([$nouveauNom, $id]);
            return $success;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return -1; 
            }
            return false;
        }
    }

    public function delete($id) {
        // On vérifie d'abord s'il y a des objets dans cette catégorie
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tk_objets WHERE id_categorie = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return -2; // → catégorie non vide → interdiction de suppression
        }

        $stmt = $this->db->prepare("DELETE FROM tk_categorie WHERE id_categorie = ?");
        return $stmt->execute([$id]);
    }

    public function nameExists($nom, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM tk_categorie WHERE nom_categorie = ?";
        $params = [$nom];

        if ($excludeId !== null) {
            $sql .= " AND id_categorie != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM tk_categorie";
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