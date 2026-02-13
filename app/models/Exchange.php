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

    public function hasPendingExchangeForObject($objetId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM tk_echanges e
            WHERE e.status = 'attente'
              AND e.objet_proposer = :oid
        ");
        $stmt->bindValue(':oid', (int)$objetId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int)($row['total'] ?? 0)) > 0;
    }

    public function getById($idEchange) {
        $stmt = $this->db->prepare("SELECT * FROM tk_echanges WHERE id_echange = ?");
        $stmt->execute([(int)$idEchange]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getReceivedByUser($userId, $status = null) {
        $sql = "
            SELECT *
            FROM tk_v_exchange_received_details
            WHERE id_receveur = :uid
        ";

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status ";
        }

        $sql .= " ORDER BY date_proposition DESC ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', (int)$userId, PDO::PARAM_INT);
        if ($status !== null && $status !== '') {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserTransactions($userId, $limit = 50) {
        $sql = "
            SELECT *
            FROM tk_v_exchange_user_transactions
            WHERE user_id = :uid
            ORDER BY date_proposition DESC
            LIMIT :lim
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':uid', (int)$userId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in Exchange::getUserTransactions - ' . $e->getMessage());
            return [];
        }
    }

    public function getSentByUser($userId, $status = null) {
        $sql = "
            SELECT *
            FROM tk_v_exchange_sent_details
            WHERE id_proposeur = :uid
        ";

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status ";
        }

        $sql .= " ORDER BY date_proposition DESC ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', (int)$userId, PDO::PARAM_INT);
        if ($status !== null && $status !== '') {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPartnersCountByUser($userId, $limit = 50) {
        $sql = "
            SELECT *
            FROM tk_v_exchange_partners_count
            WHERE user_id = :uid
            ORDER BY total_transactions DESC, partner_name ASC
            LIMIT :lim
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', (int)$userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReceivedStatsByUser($userId) {
        $sql = "
            SELECT *
            FROM tk_v_exchange_received_stats
            WHERE id_user = ?
            LIMIT 1
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in Exchange::getReceivedStatsByUser - ' . $e->getMessage());
            $row = null;
        }

        return $row ?: [
            'id_user' => (int)$userId,
            'total_demandes' => 0,
            'total_accepter' => 0,
            'total_refuser' => 0,
            'total_attente' => 0,
            'total_non_reponse' => 0,
        ];
    }

    public function getSentStatsByUser($userId) {
        $sql = "
            SELECT *
            FROM tk_v_exchange_sent_stats
            WHERE id_user = ?
            LIMIT 1
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in Exchange::getSentStatsByUser - ' . $e->getMessage());
            $row = null;
        }

        return $row ?: [
            'id_user' => (int)$userId,
            'total_demandes' => 0,
            'total_accepter' => 0,
            'total_refuser' => 0,
            'total_attente' => 0,
            'total_non_reponse' => 0,
        ];
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

            $stmt = $this->db->prepare("
                UPDATE tk_echanges
                SET status = 'refuser'
                WHERE status = 'attente'
                  AND id_echange <> :id
                  AND objet_requise = :orq
            ");
            $stmt->bindValue(':id', (int)$idEchange, PDO::PARAM_INT);
            $stmt->bindValue(':orq', (int)$objetRequise, PDO::PARAM_INT);
            $stmt->execute();

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

    public function EchangeAttente($id_receveur){
        $sql = "
            SELECT e.*,
            e.objet_proposer AS id_objet_proposer,
            e.objet_requise AS id_objet_requise,
            u1.name name_proposeur, u1.email email_proposeur,
            o1.title objet_proposer_title, o1.prix_estime prix_proposer,
            o2.title objet_requise_title, o2.prix_estime prix_requise
            FROM tk_echanges e
            JOIN tk_user u1 ON e.id_proposeur = u1.id_user
            JOIN tk_objets o1 ON e.objet_proposer = o1.id_objet
            JOIN tk_objets o2 ON e.objet_requise = o2.id_objet
            WHERE e.status = 'attente'
              AND e.id_receveur = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_receveur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithRequestedObjectDetails($status = null) {
        $sql = "
            SELECT
                e.id_echange,
                e.id_proposeur,
                e.id_receveur,
                e.objet_proposer,
                e.objet_requise,
                e.status,
                e.date_proposition,
                orq.title AS objet_requise_title,
                orq.prix_estime AS objet_requise_prix,
                (
                    SELECT oi.image
                    FROM tk_objet_img oi
                    WHERE oi.id_objet = orq.id_objet
                    ORDER BY oi.id_objet_img ASC
                    LIMIT 1
                ) AS objet_requise_image,
                u1.name AS proposeur_name,
                u2.name AS receveur_name
            FROM tk_echanges e
            LEFT JOIN tk_objets orq ON e.objet_requise = orq.id_objet
            LEFT JOIN tk_user u1 ON e.id_proposeur = u1.id_user
            LEFT JOIN tk_user u2 ON e.id_receveur = u2.id_user
        ";

		$params = [];
		if ($status !== null && $status !== '') {
			$sql .= " WHERE e.status = ? ";
			$params[] = $status;
		}

		$sql .= " ORDER BY e.date_proposition DESC ";

		try {
			$stmt = $this->db->prepare($sql);
			$stmt->execute($params);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			error_log('Error in Exchange::getAllWithRequestedObjectDetails - ' . $e->getMessage());
			return [];
		}
	}

	public function getStatusStats() {
		$sql = "
			SELECT status, COUNT(*) AS total
			FROM tk_echanges
			GROUP BY status
		";
		try {
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			error_log('Error in Exchange::getStatusStats - ' . $e->getMessage());
			$rows = [];
		}

		$counts = [
			'attente' => 0,
			'accepter' => 0,
			'refuser' => 0,
		];
		foreach ($rows as $r) {
			$st = strtolower((string)($r['status'] ?? ''));
			$val = (int)($r['total'] ?? 0);
			if (array_key_exists($st, $counts)) {
				$counts[$st] = $val;
			}
		}

		$total = array_sum($counts);
		$percent = [
			'attente' => 0,
			'accepter' => 0,
			'refuser' => 0,
		];
		if ($total > 0) {
			foreach ($counts as $k => $v) {
				$percent[$k] = (int)round(($v * 100) / $total);
			}
		}

		return [
			'total' => $total,
			'counts' => $counts,
			'percent' => $percent,
		];
	}
}
