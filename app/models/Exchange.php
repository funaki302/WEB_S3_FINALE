<?php
namespace app\models;

use Flight;
use PDO;
use PDOException;

class Exchange {

    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function create($idProposeur, $idReceveur, $objetProposer, $objetRequise) {
        $stmt = $this->db->prepare("
            INSERT INTO tk_echanges (id_proposeur, id_receveur, objet_proposer, objet_requise, status, date_proposition)
            VALUES (:id_proposeur, :id_receveur, :objet_proposer, :objet_requise, 'attente', NOW())
        ");
        $stmt->bindValue(':id_proposeur', (int)$idProposeur, PDO::PARAM_INT);
        $stmt->bindValue(':id_receveur', (int)$idReceveur, PDO::PARAM_INT);
        $stmt->bindValue(':objet_proposer', (int)$objetProposer, PDO::PARAM_INT);
        $stmt->bindValue(':objet_requise', (int)$objetRequise, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function getById($idEchange) {
        $stmt = $this->db->prepare("SELECT * FROM tk_echanges WHERE id_echange = ?");
        $stmt->execute([(int)$idEchange]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getReceivedByUser($userId, $status = null) {
        $sql = "
            SELECT e.*, 
                   op.title AS objet_proposer_title,
                   orq.title AS objet_requise_title,
                   u.name AS proposeur_name
            FROM tk_echanges e
            LEFT JOIN tk_objets op ON e.objet_proposer = op.id_objet
            LEFT JOIN tk_objets orq ON e.objet_requise = orq.id_objet
            LEFT JOIN tk_user u ON e.id_proposeur = u.id_user
            WHERE e.id_receveur = :uid
        ";

        if ($status !== null && $status !== '') {
            $sql .= " AND e.status = :status ";
        }

        $sql .= " ORDER BY e.date_proposition DESC ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', (int)$userId, PDO::PARAM_INT);
        if ($status !== null && $status !== '') {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($idEchange, $status) {
        $stmt = $this->db->prepare("UPDATE tk_echanges SET status = :status WHERE id_echange = :id");
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':id', (int)$idEchange, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function acceptExchange($idEchange, $currentUserId) {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("SELECT * FROM tk_echanges WHERE id_echange = :id FOR UPDATE");
            $stmt->bindValue(':id', (int)$idEchange, PDO::PARAM_INT);
            $stmt->execute();
            $echange = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$echange) {
                $this->db->rollBack();
                return ['ok' => false, 'error' => 'Echange introuvable'];
            }

            if ((int)$echange['id_receveur'] !== (int)$currentUserId) {
                $this->db->rollBack();
                return ['ok' => false, 'error' => 'Non autorisé'];
            }

            if ($echange['status'] !== 'attente') {
                $this->db->rollBack();
                return ['ok' => false, 'error' => 'Echange déjà traité'];
            }

            $objetProposer = (int)$echange['objet_proposer'];
            $objetRequise = (int)$echange['objet_requise'];
            $idProposeur = (int)$echange['id_proposeur'];
            $idReceveur = (int)$echange['id_receveur'];

            $stmt = $this->db->prepare("SELECT id_objet, id_proprietaire FROM tk_objets WHERE id_objet IN (?, ?) FOR UPDATE");
            $stmt->execute([$objetProposer, $objetRequise]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $owners = [];
            foreach ($rows as $r) {
                $owners[(int)$r['id_objet']] = (int)$r['id_proprietaire'];
            }

            if (!isset($owners[$objetProposer]) || !isset($owners[$objetRequise])) {
                $this->db->rollBack();
                return ['ok' => false, 'error' => 'Objet introuvable'];
            }

            if ($owners[$objetProposer] !== $idProposeur || $owners[$objetRequise] !== $idReceveur) {
                $this->db->rollBack();
                return ['ok' => false, 'error' => 'Les propriétaires ont changé, échange impossible'];
            }

            $stmt = $this->db->prepare("UPDATE tk_echanges SET status = 'accepter' WHERE id_echange = ?");
            $stmt->execute([(int)$idEchange]);

            $stmt = $this->db->prepare("UPDATE tk_objets SET id_proprietaire = :owner WHERE id_objet = :obj");
            $stmt->execute([':owner' => $idReceveur, ':obj' => $objetProposer]);
            $stmt->execute([':owner' => $idProposeur, ':obj' => $objetRequise]);

            $stmt = $this->db->prepare("
                INSERT INTO tk_objet_history (id_objet, id_proprietaire, id_echange, date_echange)
                VALUES (:id_objet, :id_proprietaire, :id_echange, NOW())
            ");

            $stmt->execute([
                ':id_objet' => $objetProposer,
                ':id_proprietaire' => $idReceveur,
                ':id_echange' => (int)$idEchange
            ]);
            $stmt->execute([
                ':id_objet' => $objetRequise,
                ':id_proprietaire' => $idProposeur,
                ':id_echange' => (int)$idEchange
            ]);

            $this->db->commit();
            return ['ok' => true];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Error in Exchange::acceptExchange - ' . $e->getMessage());
            return ['ok' => false, 'error' => 'Erreur serveur'];
        }
    }
}
