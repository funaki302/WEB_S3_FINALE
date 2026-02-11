
DROP VIEW IF EXISTS tk_v_user_objet_count;
CREATE VIEW tk_v_user_objet_count AS
SELECT
  o.id_proprietaire AS id_user,
  COUNT(*) AS total_objets
FROM tk_objets o
GROUP BY o.id_proprietaire;

DROP VIEW IF EXISTS tk_v_exchange_received_stats;
CREATE VIEW tk_v_exchange_received_stats AS
SELECT
  e.id_receveur AS id_user,
  COUNT(*) AS total_demandes,
  SUM(CASE WHEN e.status = 'accepter' THEN 1 ELSE 0 END) AS total_accepter,
  SUM(CASE WHEN e.status = 'refuser' THEN 1 ELSE 0 END) AS total_refuser,
  SUM(CASE WHEN e.status = 'attente' THEN 1 ELSE 0 END) AS total_attente,
  SUM(CASE WHEN e.status = 'attente' THEN 1 ELSE 0 END) AS total_non_reponse
FROM tk_echanges e
GROUP BY e.id_receveur;

DROP VIEW IF EXISTS tk_v_exchange_sent_stats;
CREATE VIEW tk_v_exchange_sent_stats AS
SELECT
  e.id_proposeur AS id_user,
  COUNT(*) AS total_demandes,
  SUM(CASE WHEN e.status = 'accepter' THEN 1 ELSE 0 END) AS total_accepter,
  SUM(CASE WHEN e.status = 'refuser' THEN 1 ELSE 0 END) AS total_refuser,
  SUM(CASE WHEN e.status = 'attente' THEN 1 ELSE 0 END) AS total_attente,
  SUM(CASE WHEN e.status = 'attente' THEN 1 ELSE 0 END) AS total_non_reponse
FROM tk_echanges e
GROUP BY e.id_proposeur;

DROP VIEW IF EXISTS tk_v_exchange_received_details;
CREATE VIEW tk_v_exchange_received_details AS
SELECT
  e.id_echange,
  e.id_proposeur,
  e.id_receveur,
  e.objet_proposer,
  e.objet_requise,
  e.status,
  e.date_proposition,
  op.title AS objet_proposer_title,
  orq.title AS objet_requise_title,
  u.name AS proposeur_name
FROM tk_echanges e
LEFT JOIN tk_objets op ON e.objet_proposer = op.id_objet
LEFT JOIN tk_objets orq ON e.objet_requise = orq.id_objet
LEFT JOIN tk_user u ON e.id_proposeur = u.id_user;

DROP VIEW IF EXISTS tk_v_exchange_sent_details;
CREATE VIEW tk_v_exchange_sent_details AS
SELECT
  e.id_echange,
  e.id_proposeur,
  e.id_receveur,
  e.objet_proposer,
  e.objet_requise,
  e.status,
  e.date_proposition,
  op.title AS objet_proposer_title,
  orq.title AS objet_requise_title,
  orq.prix_estime AS objet_requise_prix,
  u.name AS receveur_name
FROM tk_echanges e
LEFT JOIN tk_objets op ON e.objet_proposer = op.id_objet
LEFT JOIN tk_objets orq ON e.objet_requise = orq.id_objet
LEFT JOIN tk_user u ON e.id_receveur = u.id_user;

DROP VIEW IF EXISTS tk_v_exchange_partners_count;
CREATE VIEW tk_v_exchange_partners_count AS
SELECT
  t.user_id,
  t.partner_id,
  u.name AS partner_name,
  u.email AS partner_email,
  COUNT(*) AS total_transactions,
  SUM(CASE WHEN t.status = 'accepter' THEN 1 ELSE 0 END) AS total_accepter,
  SUM(CASE WHEN t.status = 'refuser' THEN 1 ELSE 0 END) AS total_refuser,
  SUM(CASE WHEN t.status = 'attente' THEN 1 ELSE 0 END) AS total_attente
FROM (
  SELECT
    e.id_proposeur AS user_id,
    e.id_receveur AS partner_id,
    e.status AS status
  FROM tk_echanges e
  UNION ALL
  SELECT
    e.id_receveur AS user_id,
    e.id_proposeur AS partner_id,
    e.status AS status
  FROM tk_echanges e
) t
JOIN tk_user u ON u.id_user = t.partner_id
GROUP BY t.user_id, t.partner_id, u.name, u.email;
