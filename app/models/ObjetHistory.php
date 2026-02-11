<?php
namespace app\models;

use Flight;
use PDO;
use PDOException;

class ObjetHistory {

    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT oh.*, o.nom_objet, u.username as proprietaire_nom, e.status as echange_status
                FROM tk_objet_history oh
                LEFT JOIN tk_objets o ON oh.id_objet = o.id_objet
                LEFT JOIN tk_user u ON oh.id_proprietaire = u.id_user
                LEFT JOIN tk_echanges e ON oh.id_echange = e.id_echange
                ORDER BY oh.date_echange DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::getAll - ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT oh.*, o.nom_objet, u.username as proprietaire_nom, e.status as echange_status
                FROM tk_objet_history oh
                LEFT JOIN tk_objets o ON oh.id_objet = o.id_objet
                LEFT JOIN tk_user u ON oh.id_proprietaire = u.id_user
                LEFT JOIN tk_echanges e ON oh.id_echange = e.id_echange
                WHERE oh.id_objet_history = ?
            ");
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::getById - ' . $e->getMessage());
            return null;
        }
    }

    public function insert($idObjet, $idProprietaire, $idEchange, $dateEchange = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO tk_objet_history (id_objet, id_proprietaire, id_echange, date_echange) 
                VALUES (:id_objet, :id_proprietaire, :id_echange, :date_echange)
            ");
            
            $stmt->bindValue(':id_objet', (int)$idObjet, PDO::PARAM_INT);
            $stmt->bindValue(':id_proprietaire', (int)$idProprietaire, PDO::PARAM_INT);
            $stmt->bindValue(':id_echange', (int)$idEchange, PDO::PARAM_INT);
            
            if ($dateEchange) {
                $stmt->bindValue(':date_echange', $dateEchange, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':date_echange', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::insert - ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $idObjet = null, $idProprietaire = null, $idEchange = null, $dateEchange = null) {
        try {
            $fields = [];
            $params = [];
            
            if ($idObjet !== null) {
                $fields[] = "id_objet = ?";
                $params[] = (int)$idObjet;
            }
            
            if ($idProprietaire !== null) {
                $fields[] = "id_proprietaire = ?";
                $params[] = (int)$idProprietaire;
            }
            
            if ($idEchange !== null) {
                $fields[] = "id_echange = ?";
                $params[] = (int)$idEchange;
            }
            
            if ($dateEchange !== null) {
                $fields[] = "date_echange = ?";
                $params[] = $dateEchange;
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $params[] = (int)$id;
            
            $sql = "UPDATE tk_objet_history SET " . implode(', ', $fields) . " WHERE id_objet_history = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::update - ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM tk_objet_history WHERE id_objet_history = ?");
            return $stmt->execute([(int)$id]);
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::delete - ' . $e->getMessage());
            return false;
        }
    }

    public function getObjetHistory($idObjet) {
        try {
            $stmt = $this->db->prepare("
                SELECT *
                FROM tk_v_objet_history
                WHERE id_objet = ?
            ");
            $stmt->execute([(int)$idObjet]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::getByObjet - ' . $e->getMessage());
            return [];
        }
    }

    public function getByProprietaire($idProprietaire) {
        try {
            $stmt = $this->db->prepare("
                SELECT oh.*, o.nom_objet, u.username as proprietaire_nom, e.status as echange_status
                FROM tk_objet_history oh
                LEFT JOIN tk_objets o ON oh.id_objet = o.id_objet
                LEFT JOIN tk_user u ON oh.id_proprietaire = u.id_user
                LEFT JOIN tk_echanges e ON oh.id_echange = e.id_echange
                WHERE oh.id_proprietaire = ?
                ORDER BY oh.date_echange DESC
            ");
            $stmt->execute([(int)$idProprietaire]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::getByProprietaire - ' . $e->getMessage());
            return [];
        }
    }

    public function getByEchange($idEchange) {
        try {
            $stmt = $this->db->prepare("
                SELECT oh.*, o.nom_objet, u.username as proprietaire_nom, e.status as echange_status
                FROM tk_objet_history oh
                LEFT JOIN tk_objets o ON oh.id_objet = o.id_objet
                LEFT JOIN tk_user u ON oh.id_proprietaire = u.id_user
                LEFT JOIN tk_echanges e ON oh.id_echange = e.id_echange
                WHERE oh.id_echange = ?
                ORDER BY oh.date_echange DESC
            ");
            $stmt->execute([(int)$idEchange]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in ObjetHistory::getByEchange - ' . $e->getMessage());
            return [];
        }
    }
}
