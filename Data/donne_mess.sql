USE takalokalo;

-- Insert users into tk_user (note: 'moderator' -> 'user' to match tk_user role ENUM)
INSERT INTO tk_user (name, email, role, status, phone, join_date, last_active, pwd) VALUES
('John Doe', 'john.doe@example.com', 'admin', 'active', '+261341234567', '2024-01-15', '2026-02-01 09:12', 'admin123'),
('Jane Smith', 'jane.smith@example.com', 'user', 'active', '+261330001122', '2024-03-10', '2026-02-01 17:45', 'user123'),
('Mike Johnson', 'mike.johnson@example.com', 'user', 'active', '+261320045678', '2023-11-05', '2026-01-30 14:20', 'mod123'),
('Sarah Williams', 'sarah.williams@example.com', 'user', 'inactive', '+261381112233', '2024-06-22', '2025-12-20 08:05', 'user123'),
('Alex Ranaivo', 'alex.ranaivo@example.com', 'user', 'active', '+261340998877', '2024-09-01', '2026-02-02 08:30', 'user123'),
('Nina Rakoto', 'nina.rakoto@example.com', 'admin', 'active', '+261321234999', '2023-08-18', '2026-02-02 10:05', 'admin123');

INSERT INTO tk_discussion (title, id_user1, id_user2, date_creation) VALUES
('Onboarding - Jane', 1, 2, '2025-12-01'),
('Support Ticket #1042', 2, 3, '2026-01-10'),
('HR Request - Contract', 4, 6, '2026-01-18'),
('Budget Q1', 5, 6, '2026-01-25');

INSERT INTO tk_messages (id_discussion, id_sender, contenue, date_envoie) VALUES
(1, 1, 'Welcome aboard Jane! Let me know if you need access to any tools.', '2025-12-01'),
(1, 2, 'Thanks John! I need access to the analytics dashboard.', '2025-12-01'),
(1, 1, 'Granted. You should be able to log in now.', '2025-12-02'),

(2, 2, 'Hi Support, I cannot reset my password. The link is expired.', '2026-01-10'),
(2, 3, 'Hello Jane, please try again. I just generated a new reset link.', '2026-01-10'),
(2, 2, 'It works now, thank you!', '2026-01-11'),

(3, 4, 'Hello, I need a copy of my updated contract.', '2026-01-18'),
(3, 6, 'Hi Sarah, I will send it to your email today.', '2026-01-18'),
(3, 6, 'Contract sent. Please confirm reception.', '2026-01-19'),
(3, 4, 'Received, thanks!', '2026-01-19'),

(4, 5, 'Can we review the Q1 budget assumptions tomorrow morning?', '2026-01-25'),
(4, 6, 'Yes, schedule a meeting at 10:00.', '2026-01-25'),
(4, 5, 'Meeting invite sent.', '2026-01-26');
