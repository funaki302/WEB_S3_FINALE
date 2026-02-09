<?php
// models/Objet.php
class Objet {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        $sql = "INSERT INTO tk_objets (id_proprietaire, id_categorie, title, description, prix_estime, date_creation)
                VALUES (:id_proprietaire, :id_categorie, :title, :description, :prix_estime, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT o.*, c.nom_categorie, u.name AS proprietaire 
            FROM tk_objets o
            LEFT JOIN tk_categorie c ON o.id_categorie = c.id_categorie
            LEFT JOIN tk_user u ON o.id_proprietaire = u.id_user
            WHERE o.id_objet = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM tk_objets WHERE id_proprietaire = ? ORDER BY date_creation DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // etc.
}