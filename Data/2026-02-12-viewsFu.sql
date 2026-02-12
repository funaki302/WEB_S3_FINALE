
DROP VIEW IF EXISTS tk_v_exchange_user_transactions;
CREATE VIEW tk_v_exchange_user_transactions AS
SELECT
  u.id_user AS user_id,
  e.id_echange,
  e.status,
  e.date_proposition,
  CASE
    WHEN u.id_user = e.id_proposeur THEN e.id_receveur
    ELSE e.id_proposeur
  END AS partner_id,
  CASE
    WHEN u.id_user = e.id_proposeur THEN u2.name
    ELSE u1.name
  END AS partner_name,
  CASE
    WHEN u.id_user = e.id_proposeur THEN e.objet_requise
    ELSE e.objet_proposer
  END AS received_objet_id,
  CASE
    WHEN u.id_user = e.id_proposeur THEN orq.title
    ELSE op.title
  END AS received_objet_title,
  CASE
    WHEN u.id_user = e.id_proposeur THEN (
      SELECT oi.image
      FROM tk_objet_img oi
      WHERE oi.id_objet = orq.id_objet
      ORDER BY oi.id_objet_img ASC
      LIMIT 1
    )
    ELSE (
      SELECT oi.image
      FROM tk_objet_img oi
      WHERE oi.id_objet = op.id_objet
      ORDER BY oi.id_objet_img ASC
      LIMIT 1
    )
  END AS received_objet_image,
  CASE
    WHEN u.id_user = e.id_proposeur THEN e.objet_proposer
    ELSE e.objet_requise
  END AS given_objet_id,
  CASE
    WHEN u.id_user = e.id_proposeur THEN op.title
    ELSE orq.title
  END AS given_objet_title,
  CASE
    WHEN u.id_user = e.id_proposeur THEN (
      SELECT oi.image
      FROM tk_objet_img oi
      WHERE oi.id_objet = op.id_objet
      ORDER BY oi.id_objet_img ASC
      LIMIT 1
    )
    ELSE (
      SELECT oi.image
      FROM tk_objet_img oi
      WHERE oi.id_objet = orq.id_objet
      ORDER BY oi.id_objet_img ASC
      LIMIT 1
    )
  END AS given_objet_image
FROM tk_echanges e
JOIN tk_user u ON u.id_user IN (e.id_proposeur, e.id_receveur)
LEFT JOIN tk_user u1 ON u1.id_user = e.id_proposeur
LEFT JOIN tk_user u2 ON u2.id_user = e.id_receveur
LEFT JOIN tk_objets op ON e.objet_proposer = op.id_objet
LEFT JOIN tk_objets orq ON e.objet_requise = orq.id_objet;
