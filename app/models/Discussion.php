<?php

/**
 * Discussion Model
 * Gestion des opérations CRUD pour la table discussion
 */

namespace app\models;

use Flight;
use PDO;
use PDOException;

class Discussion {
    private $db;
    private $table = 'discussion';
    
    public function __construct() {
        $app = \Flight::app();
        $this->db = Flight::db();
    }
    
    /**
     * Récupérer toutes les discussions
     * @param array $options Options de filtrage et pagination
     * @return array
     */
    public function getAll($options = []) {
        $sql = "SELECT d.*, 
                       u1.name as user1_name, u1.email as user1_email,
                       u2.name as user2_name, u2.email as user2_email
                FROM {$this->table} d
                LEFT JOIN user u1 ON d.id_user1 = u1.id_user
                LEFT JOIN user u2 ON d.id_user2 = u2.id_user";
        $params = [];
        
        // Filtres
        if (!empty($options['id_user'])) {
            $sql .= " WHERE (d.id_user1 = ? OR d.id_user2 = ?)";
            $params[] = $options['id_user'];
            $params[] = $options['id_user'];
        }
        
        if (!empty($options['title'])) {
            $sql .= (empty($params) ? " WHERE" : " AND") . " d.title LIKE ?";
            $params[] = '%' . $options['title'] . '%';
        }
        
        // Tri
        $sql .= " ORDER BY d.updated_at DESC, d.created_at DESC";
        
        // Pagination
        if (!empty($options['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $options['limit'];
        }
        
        if (!empty($options['offset'])) {
            $sql .= " OFFSET ?";
            $params[] = $options['offset'];
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            error_log("Error in Discussion::getAll - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer une discussion par son ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT d.*, 
                       u1.name as user1_name, u1.email as user1_email,
                       u2.name as user2_name, u2.email as user2_email
                FROM {$this->table} d
                LEFT JOIN user u1 ON d.id_user1 = u1.id_user
                LEFT JOIN user u2 ON d.id_user2 = u2.id_user
                WHERE d.id_discussion = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in Discussion::getById - " . $e->getMessage());
            return null;
        }
    }
    

    public function getConversations($id_user){
        $sql = "SELECT 
                d.id_discussion,
                d.title,
                u.id_user,
                u.name,
                u.email,
                u.phone,
                u.role
                FROM discussion d
                JOIN user u on u.id_user = d.id_user2
                WHERE d.id_user1 = '$id_user'
                UNION 
                SELECT 
                d.id_discussion,
                d.title,
                u.id_user,
                u.name,
                u.email,
                u.phone,
                u.role
                FROM discussion d
                JOIN user u on u.id_user = d.id_user1
                WHERE d.id_user2 = '$id_user'
              ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            error_log("Error in Discussion::getConversations - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les discussions d'un utilisateur
     * @param int $userId
     * @return array
     */
    public function getByUser($userId) {
        return $this->getAll(['id_user' => $userId]);
    }
    
    /**
     * Créer une nouvelle discussion
     * @param array $data
     * @return int|bool ID de la discussion créée ou false en cas d'erreur
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (title, id_user1, id_user2, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['id_user1'],
            $data['id_user2'],
            $data['created_at'] ?? date('Y-m-d'),
            $data['updated_at'] ?? date('Y-m-d')
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in Discussion::create - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour une discussion
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['title'])) {
            $updates[] = "title = ?";
            $params[] = $data['title'];
        }
        
        if (isset($data['id_user1'])) {
            $updates[] = "id_user1 = ?";
            $params[] = $data['id_user1'];
        }
        
        if (isset($data['id_user2'])) {
            $updates[] = "id_user2 = ?";
            $params[] = $data['id_user2'];
        }
        
        if (isset($data['updated_at'])) {
            $updates[] = "updated_at = ?";
            $params[] = $data['updated_at'];
        } else {
            $updates[] = "updated_at = ?";
            $params[] = date('Y-m-d');
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql .= implode(', ', $updates) . " WHERE id_discussion = ?";
        $params[] = $id;
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error in Discussion::update - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer une discussion
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id_discussion = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Discussion::delete - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifier si une discussion existe entre deux utilisateurs
     * @param int $user1Id
     * @param int $user2Id
     * @return array|null
     */
    public function existsBetweenUsers($user1Id, $user2Id) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (id_user1 = ? AND id_user2 = ?) 
                   OR (id_user1 = ? AND id_user2 = ?)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user1Id, $user2Id, $user2Id, $user1Id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in Discussion::existsBetweenUsers - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Mettre à jour la date de dernière activité
     * @param int $id
     * @return bool
     */
    public function updateLastActivity($id) {
        return $this->update($id, ['updated_at' => date('Y-m-d')]);
    }
}