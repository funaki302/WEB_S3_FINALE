<?php
// models/User.php
class User {

    private $db;

    public function __construct($db) {
        $this->db = $db; // PDO instance
    }

    public function create($data) {
        $sql = "INSERT INTO tk_user (name, email, status, phone, join_date, last_active, pwd, role)
                VALUES (:name, :email, :status, :phone, :join_date, NOW(), :pwd, :role)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM tk_user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tk_user WHERE id_user = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLastActive($id) {
        $stmt = $this->db->prepare("UPDATE tk_user SET last_active = NOW() WHERE id_user = ?");
        return $stmt->execute([$id]);
    }

    // Ajoute update, delete, getAll, etc. selon besoin
}