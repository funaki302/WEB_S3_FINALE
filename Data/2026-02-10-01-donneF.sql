-- Test data for tk_user
INSERT INTO tk_user (name, email, status, phone, join_date, last_active, pwd, role) VALUES
('Alice Dupont', 'alice@example.com', 'active', '0123456789', '2023-01-01', '2023-12-01 10:00:00', 'password123', 'user'),
('Bob Martin', 'bob@example.com', 'active', '0987654321', '2023-02-01', '2023-12-02 11:00:00', 'password456', 'user'),
('Charlie Durand', 'charlie@example.com', 'inactive', '0567890123', '2023-03-01', '2023-11-15 09:00:00', 'password789', 'admin');

-- Test data for tk_categorie
INSERT INTO tk_categorie (nom_categorie) VALUES
('Electronics'),
('Books'),
('Clothing'),
('Furniture');

-- Test data for tk_objets (assuming user IDs 1,2,3 and category IDs 1,2,3,4)
INSERT INTO tk_objets (id_proprietaire, id_categorie, title, description, prix_estime, date_creation) VALUES
(1, 1, 'Old Laptop', 'A used laptop in good condition', 300.00, '2023-10-01 12:00:00'),
(2, 2, 'Programming Book', 'Learn PHP programming', 25.00, '2023-10-02 13:00:00'),
(3, 3, 'Winter Jacket', 'Warm jacket for cold weather', 50.00, '2023-10-03 14:00:00'),
(1, 4, 'Wooden Table', 'Dining table for 4 people', 150.00, '2023-10-04 15:00:00');

INSERT INTO tk_objets (id_proprietaire, id_categorie, title, description, prix_estime, date_creation) VALUES
(7, 1, 'Smartphone Android', 'Phone unlocked, 128GB storage', 180.00, '2023-10-10 09:15:00'),
(7, 2, 'Roman Policier', 'A detective novel in very good condition', 12.00, '2023-10-10 09:25:00'),
(7, 3, 'Sweat à capuche', 'Hoodie size L, barely worn', 22.50, '2023-10-10 09:35:00'),
(7, 4, 'Chaise de bureau', 'Comfortable chair with adjustable height', 65.00, '2023-10-10 09:45:00'),
(7, 2, 'BD Collection', 'Comic book volume 1-3', 18.00, '2023-10-10 09:55:00');

-- Test data for tk_objet_img
INSERT INTO tk_objet_img (id_objet, image) VALUES
(1, 'laptop.jpg'),
(2, 'book.jpg'),
(3, 'jacket.jpg'),
(4, 'table.jpg');

-- Test data for tk_discussion
INSERT INTO tk_discussion (title, id_user1, id_user2, date_creation) VALUES
('Discussion about laptop', 1, 2, '2023-11-01'),
('Book exchange chat', 2, 3, '2023-11-02');

-- Test data for tk_messages
INSERT INTO tk_messages (id_discussion, id_sender, contenue, date_envoie, seen_at) VALUES
(1, 1, 'Hi, interested in your laptop?', '2023-11-01', '2023-11-01 16:00:00'),
(1, 2, 'Yes, what do you offer?', '2023-11-01', NULL),
(2, 2, 'Want to exchange books?', '2023-11-02', '2023-11-02 17:00:00');

-- Test data for tk_echanges
INSERT INTO tk_echanges (id_proposeur, id_receveur, objet_proposer, objet_requise, status, date_proposition) VALUES
(1, 2, 1, 2, 'attente', '2023-11-05 10:00:00'),
(2, 3, 2, 3, 'accepter', '2023-11-06 11:00:00');

-- Test data for tk_objet_history
INSERT INTO tk_objet_history (id_objet, id_proprietaire, id_echange, date_echange) VALUES
(2, 1, 1, '2023-11-07 12:00:00'),
(3, 2, 2, '2023-11-08 13:00:00');
