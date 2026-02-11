create or replace view tk_v_info_objet as
select o.*,
c.nom_categorie,
u.name as proprietaire_name,
u.email as proprietaire_email,
u.role as proprietaire_role,
u.phone as proprietaire_phone,
u.status as proprietaire_status,
u.join_date as proprietaire_join_date
from tk_objets o
left join tk_categorie c on o.id_categorie = c.id_categorie
left join tk_user u on o.id_proprietaire = u.id_user;