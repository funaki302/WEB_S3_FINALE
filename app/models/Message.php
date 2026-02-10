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
    private $table = 'tk_messages';
    
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
            LEFT JOIN tk_user u ON m.id_sender = u.id_user
            LEFT JOIN tk_discussion d ON m.id_discussion = d.id_discussion
            ";
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
            $sql .= (empty($params) ? " WHERE" : " AND") . " m.contenue LIKE ?";
            $params[] = '%' . $options['content'] . '%';
        }
        
        // Tri par date d'envoi croissante
        $sql .= " ORDER BY m.date_envoie ASC";
        
        // Pagination
        if (!empty($options['limit'])) {
            $offset = $options['offset'] ?? 0;
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int) $options['limit'];
            $params[] = (int) $offset;
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
            LEFT JOIN tk_user u ON m.id_sender = u.id_user
            LEFT JOIN tk_discussion d ON m.id_discussion = d.id_discussion
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
                (id_discussion, id_sender, contenue, date_envoie, seen_at) 
                VALUES (?, ?, ?, NOW(), NULL)";
        
        $params = [
            $data['id_discussion'],
            $data['id_sender'],
            $data['contenue'] ?? ($data['content'] ?? '')
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in Message::create - " . $e->getMessage());
            return false;
        }
    }

    public function markSeen($discussionId, $viewerUserId) {
        $sql = "UPDATE {$this->table}
                SET seen_at = NOW()
                WHERE id_discussion = ?
                  AND id_sender != ?
                  AND seen_at IS NULL";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int) $discussionId, (int) $viewerUserId]);
            return (int) $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error in Message::markSeen - " . $e->getMessage());
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
            $updates[] = "contenue = ?";
            $params[] = $data['content'];
        }

        if (isset($data['contenue'])) {
            $updates[] = "contenue = ?";
            $params[] = $data['contenue'];
        }
        
        if (isset($data['date_envoie'])) {
            $updates[] = "date_envoie = ?";
            $params[] = $data['date_envoie'];
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
            LEFT JOIN tk_user u ON m.id_sender = u.id_user
            WHERE m.id_discussion = ?
            ORDER BY m.date_envoie DESC, m.id_message DESC
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