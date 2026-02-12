<?php
namespace app\models;

use Flight;
use PDO;
use PDOException;
// models/Objet.php
class Objet {

    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function getAllNotOwnedByUser($userId, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie, u.name AS proprietaire,
                   (SELECT image FROM tk_objet_img WHERE id_objet = o.id_objet LIMIT 1) AS image,
                   (
                     SELECT COUNT(*)
                     FROM tk_echanges e
                     WHERE e.status = 'attente'
                       AND e.objet_requise = o.id_objet
                   ) AS pending_count
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            WHERE o.id_proprietaire <> :user_id
            AND o.date_inactif IS NULL
            ORDER BY o.date_creation DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCategories() {
        $stmt = $this->db->prepare("SELECT id_categorie, nom_categorie FROM tk_categorie ORDER BY nom_categorie ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchNotOwnedByUser($userId, $keyword = null, $categoryId = null, $limit = 60, $offset = 0) {
        $conditions = ["o.id_proprietaire <> :user_id"];
        $params = [];

        $keyword = is_string($keyword) ? trim($keyword) : '';
        if ($keyword !== '') {
            $conditions[] = "(o.title LIKE :kw OR o.description LIKE :kw)";
            $params[':kw'] = '%' . $keyword . '%';
        }

        if ($categoryId !== null && $categoryId !== '' && (int)$categoryId > 0) {
            $conditions[] = "o.id_categorie = :cat_id";
        }

        $whereSql = implode(' AND ', $conditions);

        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie, u.name AS proprietaire,
                   (SELECT image FROM tk_objet_img WHERE id_objet = o.id_objet LIMIT 1) AS image,
                   (
                     SELECT COUNT(*)
                     FROM tk_echanges e
                     WHERE e.status = 'attente'
                       AND e.objet_requise = o.id_objet
                   ) AS pending_count
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            WHERE $whereSql
            AND o.date_inactif IS NULL
            ORDER BY o.date_creation DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        if (array_key_exists(':kw', $params)) {
            $stmt->bindValue(':kw', $params[':kw'], PDO::PARAM_STR);
        }
        if ($categoryId !== null && $categoryId !== '' && (int)$categoryId > 0) {
            $stmt->bindValue(':cat_id', (int)$categoryId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO tk_objets 
                (id_proprietaire, id_categorie, title, description, prix_estime, date_creation)
                VALUES 
                (:id_proprietaire, :id_categorie, :title, :description, :prix_estime, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':id_proprietaire' => $data['id_proprietaire'],
            ':id_categorie'    => $data['id_categorie'],
            ':title'           => trim($data['title']),
            ':description'     => trim($data['description'] ?? ''),
            ':prix_estime'     => $data['prix_estime'] ?? null
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT o.*, 
                   c.nom_categorie, 
                   u.name AS proprietaire_name, 
                   u.email AS proprietaire_email
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            WHERE o.id_objet = ?
            AND o.date_inactif IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll($limit = 20, $offset = 0, $orderBy = 'date_creation DESC') {
        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie, u.name AS proprietaire,
                   (SELECT image FROM tk_objet_img WHERE id_objet = o.id_objet LIMIT 1) AS image
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            WHERE o.date_inactif IS NULL
            ORDER BY $orderBy
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUser($userId, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie,
                   (SELECT image FROM tk_objet_img WHERE id_objet = o.id_objet LIMIT 1) AS image
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            WHERE o.id_proprietaire = :user_id
            AND o.date_inactif IS NULL
              AND NOT EXISTS (
                SELECT 1
                FROM tk_echanges e
                WHERE e.status = 'attente'
                  AND e.objet_proposer = o.id_objet
              )
            ORDER BY o.date_creation DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($catId, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name AS proprietaire 
            FROM tk_objets o
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            WHERE o.id_categorie = ?
            AND o.date_inactif IS NULL
            ORDER BY o.date_creation DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$catId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tk_objets WHERE date_inactif IS NULL");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countByUser($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM tk_objets WHERE id_proprietaire = ? AND date_inactif IS NULL");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getStatsByUser($userId) {
        $sql = "
            SELECT
                COUNT(*) AS total_objets,
                COALESCE(SUM(prix_estime), 0) AS total_prix
            FROM tk_objets
            WHERE id_proprietaire = ? AND date_inactif IS NULL
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in Objet::getStatsByUser - ' . $e->getMessage());
            $row = null;
        }

        return [
            'total_objets' => (int)($row['total_objets'] ?? 0),
            'total_prix' => (float)($row['total_prix'] ?? 0),
        ];
    }

    public function update($id, $data) {
        error_log("Update modèle appelé avec ID: " . $id . " et data: " . print_r($data, true));
        
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $values[":$key"] = $value;
        }

        if (empty($fields)) {
            error_log("Aucun champ à mettre à jour");
            return false;
        }

        $sql = "UPDATE tk_objets SET " . implode(', ', $fields) . " WHERE id_objet = :id";
        $values[':id'] = $id;
        
        error_log("SQL: " . $sql);
        error_log("Values: " . print_r($values, true));

        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($values);
            error_log("Execute result: " . ($result ? 'true' : 'false'));
            error_log("Error info: " . print_r($stmt->errorInfo(), true));
            return $result;
        } catch (PDOException $e) {
            error_log("PDOException: " . $e->getMessage());
            return false;
        }
    }

    public function update_inactif($id) {
        $this->db->beginTransaction();
        try {
            // Rendre l'objet inactif
            $stmt = $this->db->prepare("UPDATE tk_objets SET date_inactif = NOW() WHERE id_objet = ?");
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                throw new Exception('Échec de la mise à jour');
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Erreur update_inactif: " . $e->getMessage());
            return false;
        }
    }

    public function belongsToUser($objetId, $userId) {
        $stmt = $this->db->prepare("SELECT id_proprietaire FROM tk_objets WHERE id_objet = ? AND date_inactif IS NULL");
        $stmt->execute([$objetId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['id_proprietaire'] == $userId;
    }

    public function getObjet_User($userId) {
        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie,
                   (SELECT image FROM tk_objet_img WHERE id_objet = o.id_objet LIMIT 1) AS image
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            WHERE o.id_proprietaire = ?
            AND o.date_inactif IS NULL
            ORDER BY o.date_creation DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM tk_objets WHERE date_inactif IS NULL";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error in Objet::getCount - " . $e->getMessage());
            return 0;
        }
    }

    public function getCountExchanges() {
        $sql = "SELECT COUNT(*) as total FROM tk_echanges";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error in Objet::getCountExchanges - " . $e->getMessage());
            return 0;
        }
    }

    // Information complet
    public function getObjetById($id) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM tk_v_info_objet o
            WHERE o.id_objet = ? 
            AND date_inactif IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
}