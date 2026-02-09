<?php

/**
 * User Model
 * Gestion des opérations CRUD pour la table user
 */

namespace app\models;

use Flight;
use PDO;
use PDOException;

class User {
    private $db;
    private $table = 'tk_user';
    
    public function __construct() {
        $app = \Flight::app();
        $this->db = Flight::db();
    }
    
    /**
     * Récupérer tous les utilisateurs
     * @param array $options Options de filtrage et pagination
     * @return array
     */
    public function getAll($options = []) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        // Filtres
        if (!empty($options['role'])) {
            $sql .= " WHERE role = ?";
            $params[] = $options['role'];
        }
        
        if (!empty($options['status'])) {
            $sql .= (empty($params) ? " WHERE" : " AND") . " status = ?";
            $params[] = $options['status'];
        }
        
        // no department column in tk_user
        
        // Recherche
        if (!empty($options['search'])) {
            $sql .= (empty($params) ? " WHERE" : " AND") . " 
                (name LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $options['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Tri
        $orderBy = $options['order_by'] ?? 'join_date';
        $order = $options['order'] ?? 'DESC';
        $sql .= " ORDER BY {$orderBy} {$order}";
        
        // Pagination
        if (!empty($options['limit'])) {
            $offset = $options['offset'] ?? 0;
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$options['limit'];
            $params[] = (int)$offset;
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in User::getAll - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer un utilisateur par son ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_user = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in User::getById - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer un utilisateur par son email
     * @param string $email
     * @return array|null
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in User::getByEmail - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Créer un nouvel utilisateur
     * @param array $data
     * @return int|bool ID de l'utilisateur créé ou false en cas d'erreur
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
            (name, email, status, phone, join_date, last_active, pwd, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Hash du mot de passe
        
        $params = [
            $data['name'],
            $data['email'],
            $data['status'] ?? 'active',
            $data['phone'] ?? '',
            $data['join_date'] ?? date('Y-m-d'),
            $data['last_active'] ?? date('Y-m-d H:i:s'),
            $data['pwd'],
            $data['role'] ?? 'user'
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in User::create - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour un utilisateur
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
            name = ?, email = ?, status = ?, phone = ?, last_active = ?, role = ?";
        $params = [
            $data['name'],
            $data['email'],
            $data['status'],
            $data['phone'],
            date('Y-m-d H:i:s'),
            $data['role'] ?? 'user'
        ];
        
        // Ajouter le mot de passe seulement s'il est fourni
        if (!empty($data['pwd'])) {
            $sql .= ", pwd = ?";
            $params[] = $data['pwd'];
        }
        
        // Ajouter la date de join si elle est fournie
        if (!empty($data['join_date'])) {
            $sql .= ", join_date = ?";
            $params[] = $data['join_date'];
        }
        
        $sql .= " WHERE id_user = ?";
        $params[] = $id;
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error in User::update - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un utilisateur
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id_user = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in User::delete - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compter le nombre total d'utilisateurs
     * @param array $filters Filtres optionnels
     * @return int
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        if (!empty($filters['role'])) {
            $sql .= " WHERE role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= (empty($params) ? " WHERE" : " AND") . " status = ?";
            $params[] = $filters['status'];
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Error in User::count - " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Vérifier si un email existe déjà
     * @param string $email
     * @param int|null $excludeId ID à exclure de la vérification
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id_user FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id_user != ?";
            $params[] = $excludeId;
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            error_log("Error in User::emailExists - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour la dernière activité d'un utilisateur
     * @param int $id
     * @return bool
     */
    public function updateLastActive($id) {
        $sql = "UPDATE {$this->table} SET last_active = NOW() WHERE id_user = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in User::updateLastActive - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les statistiques des utilisateurs
     * @return array
     */
    public function getStats() {
        $stats = [];
        
        // Total par rôle
        $sql = "SELECT role, COUNT(*) as count FROM {$this->table} GROUP BY role";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats['by_role'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            $stats['by_role'] = [];
        }
        
        // Total par statut
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            $stats['by_status'] = [];
        }
        
        // no department stats for tk_user
        
        return $stats;
    }

    public function updateStatus($userId, $status){
        $sql = "UPDATE {$this->table} SET last_active = NOW(), status = ? WHERE id_user = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $userId]);
        } catch (PDOException $e) {
            error_log("Error in User::updateLastActive - " . $e->getMessage());
            return false;
        }
    }
}
