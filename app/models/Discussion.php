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
    private $table = 'tk_discussion';
    
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
                LEFT JOIN tk_user u1 ON d.id_user1 = u1.id_user
                LEFT JOIN tk_user u2 ON d.id_user2 = u2.id_user";
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
        $sql .= " ORDER BY d.date_creation DESC, d.id_discussion DESC";
        
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
                   u1.name as user1_name, u1.status as user1_status, u1.email as user1_email,
                   u2.name as user2_name, u2.status as user2_status, u2.email as user2_email
            FROM {$this->table} d
            LEFT JOIN tk_user u1 ON d.id_user1 = u1.id_user
            LEFT JOIN tk_user u2 ON d.id_user2 = u2.id_user
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

    public function getRecherche($name, $userId){
        $sql = "SELECT
                    d.id_discussion,
                    d.title,
                    u.id_user,
                    u.name,
                    u.email,
                    u.phone,
                    u.role,
                    u.status
                FROM {$this->table} d
                JOIN tk_user u
                    ON u.id_user = IF(d.id_user1 = :userId, d.id_user2, d.id_user1)
                WHERE u.name LIKE :name
                AND (d.id_user1 = :userId OR d.id_user2 = :userId)";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ":userId" => $userId,
                ":name"   => "%$name%"
            ]);

            // ✅ fetchAll retourne un tableau complet
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results; // retourne toujours un tableau (vide si aucun résultat)

        } catch (PDOException $e) {
            error_log("Error in Discussion::getRecherche - " . $e->getMessage());
            return [];
        }
    }
    
    public function getConversations($userId){
        $sql = "SELECT
                    d.id_discussion,
                    d.title,
                    u.id_user,
                    u.name,
                    u.email,
                    u.phone,
                    u.role,
                    u.status,
                    COALESCE(unread.unread_count, 0) AS unread_count,
                    lastm.id_message AS last_message_id,
                    lastm.contenue AS last_message,
                    lastm.date_envoie AS last_message_date,
                    lastm.id_sender AS last_message_sender
                FROM {$this->table} d
                JOIN tk_user u
                    ON u.id_user = IF(d.id_user1 = ?, d.id_user2, d.id_user1)
                LEFT JOIN (
                    SELECT
                        id_discussion,
                        SUM(CASE WHEN seen_at IS NULL AND id_sender != ? THEN 1 ELSE 0 END) AS unread_count
                    FROM tk_messages
                    GROUP BY id_discussion
                ) unread ON unread.id_discussion = d.id_discussion
                LEFT JOIN tk_messages lastm ON lastm.id_message = (
                    SELECT MAX(m2.id_message)
                    FROM tk_messages m2
                    WHERE m2.id_discussion = d.id_discussion
                )
                WHERE d.id_user1 = ? OR d.id_user2 = ?
                ORDER BY lastm.id_message DESC, d.id_discussion DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int) $userId, (int) $userId, (int) $userId, (int) $userId]);
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
                (title, id_user1, id_user2, date_creation) 
                VALUES (?, ?, ?, NOW())";
        
        $params = [
            $data['title'],
            $data['id_user1'],
            $data['id_user2']
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

        if (isset($data['date_creation'])) {
            $updates[] = "date_creation = ?";
            $params[] = $data['date_creation'];
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
        return $this->update($id, ['date_creation' => date('Y-m-d')]);
    }

    public function getNoConv($userId){
        $sql = "SELECT * FROM tk_user WHERE id_user NOT IN 
                (SELECT
                    u.id_user
                FROM {$this->table} d
                JOIN tk_user u
                    ON u.id_user = IF(d.id_user1 = ?, d.id_user2, d.id_user1)
                WHERE d.id_user1 = ? OR d.id_user2 = ?) 
                AND id_user != ?
            ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int) $userId, (int) $userId, (int) $userId, (int) $userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (PDOException $e) {
            error_log("Error in Discussion::getConversations - " . $e->getMessage());
            return [];
        }
    }
}