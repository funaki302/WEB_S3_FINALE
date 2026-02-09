<?php

/**
 * Message Model
 * Gestion des opérations CRUD pour la table messages
 */

namespace app\models;

use Flight;
use PDO;
use PDOException;

class Message {
    private $db;
    private $table = 'messages';
    
    public function __construct() {
        $app = \Flight::app();
        $this->db = Flight::db();
    }
    
    /**
     * Récupérer tous les messages
     * @param array $options Options de filtrage et pagination
     * @return array
     */
    public function getAll($options = []) {
        $sql = "SELECT m.*, 
                       u.name as sender_name, u.email as sender_email,
                       d.title as discussion_title
                FROM {$this->table} m
                LEFT JOIN user u ON m.id_sender = u.id_user
                LEFT JOIN discussion d ON m.id_discussion = d.id_discussion";
        $params = [];
        
        // Filtres
        if (!empty($options['id_discussion'])) {
            $sql .= " WHERE m.id_discussion = ?";
            $params[] = $options['id_discussion'];
        }
        
        if (!empty($options['id_sender'])) {
            $sql .= (empty($params) ? " WHERE" : " AND") . " m.id_sender = ?";
            $params[] = $options['id_sender'];
        }
        
        if (!empty($options['content'])) {
            $sql .= (empty($params) ? " WHERE" : " AND") . " m.content LIKE ?";
            $params[] = '%' . $options['content'] . '%';
        }
        
        // Tri par date d'envoi décroissante
        $sql .= " ORDER BY m.sent_at DESC, m.id_message DESC";
        
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
            error_log("Error in Message::getAll - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer un message par son ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT m.*, 
                       u.name as sender_name, u.email as sender_email,
                       d.title as discussion_title
                FROM {$this->table} m
                LEFT JOIN user u ON m.id_sender = u.id_user
                LEFT JOIN discussion d ON m.id_discussion = d.id_discussion
                WHERE m.id_message = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in Message::getById - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer tous les messages d'une discussion
     * @param int $discussionId
     * @param array $options Options supplémentaires (limit, offset)
     * @return array
     */
    public function getByDiscussion($discussionId, $options = []) {
        $options['id_discussion'] = $discussionId;
        return $this->getAll($options);
    }
    
    /**
     * Récupérer les derniers messages d'une discussion
     * @param int $discussionId
     * @param int $limit Nombre de messages à récupérer
     * @return array
     */
    public function getLatestByDiscussion($discussionId, $limit = 50) {
        return $this->getByDiscussion($discussionId, ['limit' => $limit]);
    }
    
    /**
     * Récupérer les messages d'un utilisateur
     * @param int $userId
     * @param array $options Options supplémentaires
     * @return array
     */
    public function getBySender($userId, $options = []) {
        $options['id_sender'] = $userId;
        return $this->getAll($options);
    }
    
    /**
     * Créer un nouveau message
     * @param array $data
     * @return int|bool ID du message créé ou false en cas d'erreur
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (id_discussion, id_sender, content, sent_at) 
                VALUES (?, ?, ?, ?)";
        
        $params = [
            $data['id_discussion'],
            $data['id_sender'],
            $data['content'],
            $data['sent_at'] ?? date('Y-m-d H:i:s')
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $messageId = $this->db->lastInsertId();
            
            // Mettre à jour la date de dernière activité de la discussion
            if ($messageId) {
                $discussionModel = new Discussion();
                $discussionModel->updateLastActivity($data['id_discussion']);
            }
            
            return $messageId;
        } catch (PDOException $e) {
            error_log("Error in Message::create - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour un message
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['content'])) {
            $updates[] = "content = ?";
            $params[] = $data['content'];
        }
        
        if (isset($data['sent_at'])) {
            $updates[] = "sent_at = ?";
            $params[] = $data['sent_at'];
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql .= implode(', ', $updates) . " WHERE id_message = ?";
        $params[] = $id;
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error in Message::update - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un message
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id_message = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Message::delete - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer tous les messages d'une discussion
     * @param int $discussionId
     * @return bool
     */
    public function deleteByDiscussion($discussionId) {
        $sql = "DELETE FROM {$this->table} WHERE id_discussion = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$discussionId]);
        } catch (PDOException $e) {
            error_log("Error in Message::deleteByDiscussion - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compter le nombre de messages dans une discussion
     * @param int $discussionId
     * @return int
     */
    public function countByDiscussion($discussionId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE id_discussion = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$discussionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            error_log("Error in Message::countByDiscussion - " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupérer le dernier message d'une discussion
     * @param int $discussionId
     * @return array|null
     */
    public function getLastMessageByDiscussion($discussionId) {
        $sql = "SELECT m.*, 
                       u.name as sender_name, u.email as sender_email
                FROM {$this->table} m
                LEFT JOIN user u ON m.id_sender = u.id_user
                WHERE m.id_discussion = ?
                ORDER BY m.sent_at DESC, m.id_message DESC
                LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$discussionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in Message::getLastMessageByDiscussion - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Marquer les messages comme lus pour un utilisateur dans une discussion
     * Note: Cette méthode suppose l'ajout d'une colonne 'read_at' dans la table
     * @param int $discussionId
     * @param int $userId
     * @return bool
     */
    public function markAsRead($discussionId, $userId) {
        // Cette méthode nécessiterait une table de lecture séparée ou une colonne read_at
        // Pour l'instant, on peut la laisser comme placeholder
        error_log("Message::markAsRead - Not implemented yet. Requires additional table or column.");
        return false;
    }
}