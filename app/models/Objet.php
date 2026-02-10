<?php
// models/Objet.php
class Objet {

    private $db;

    public function __construct($db) {
        $this->db = $db;
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
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll($limit = 20, $offset = 0, $orderBy = 'date_creation DESC') {
        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie, u.name AS proprietaire 
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            ORDER BY $orderBy
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUser($userId, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie 
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            WHERE o.id_proprietaire = ?
            ORDER BY o.date_creation DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($catId, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name AS proprietaire 
            FROM tk_objets o
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            WHERE o.id_categorie = ?
            ORDER BY o.date_creation DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$catId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tk_objets");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countByUser($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM tk_objets WHERE id_proprietaire = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $values[":$key"] = $value;
        }

        if (empty($fields)) return false;

        $sql = "UPDATE tk_objets SET " . implode(', ', $fields) . " WHERE id_objet = :id";
        $values[':id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $this->db->beginTransaction();
        try {
            // Supprimer les images associées (optionnel - selon ta logique)
            $stmt = $this->db->prepare("DELETE FROM tk_objet_img WHERE id_objet = ?");
            $stmt->execute([$id]);

            // Supprimer l'objet
            $stmt = $this->db->prepare("DELETE FROM tk_objets WHERE id_objet = ?");
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function belongsToUser($objetId, $userId) {
        $stmt = $this->db->prepare("SELECT id_proprietaire FROM tk_objets WHERE id_objet = ?");
        $stmt->execute([$objetId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['id_proprietaire'] == $userId;
    }
}