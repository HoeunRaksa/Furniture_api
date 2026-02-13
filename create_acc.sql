INSERT INTO bank_accounts (account_number, first_name, last_name, password, balance, is_active, created_at, updated_at) 
VALUES ('1122334455', 'Hoeun', 'Raksa', '$2y$12$1HK0K.oebrxMJg.gczL6RuCAVlxWkwJpQfUTBPdQ9N33MIeuAOyVC', 50000.00, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE first_name='Hoeun', balance=50000.00;
