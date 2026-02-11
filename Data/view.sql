
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

