CREATE DATABASE IF NOT EXISTS safeschool_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE safeschool_hub;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Aggiunge colonna role se la tabella esiste già (upgrade sicuro)
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user','admin') NOT NULL DEFAULT 'user';

CREATE TABLE IF NOT EXISTS reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(120) NOT NULL,
  title VARCHAR(190) NOT NULL,
  description TEXT NOT NULL,
  priority ENUM('Bassa','Media','Alta') DEFAULT 'Media',
  status VARCHAR(40) DEFAULT 'Aperta',
  tracking_code VARCHAR(20) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS vault_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  site_name VARCHAR(120) NOT NULL,
  username VARCHAR(190) NOT NULL,
  password_plain TEXT NOT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- UTENTE ADMIN ROOT
-- email:    admin@fermi.it
-- password: Admin@Fermi2025!
-- (hash bcrypt generato con password_hash)
-- =============================================
INSERT IGNORE INTO users (name, email, password_hash, role)
VALUES (
  'Amministratore',
  'admin@fermi.it',
  '$2y$12$YzQ5NmE4OTViYTI2NzA5N.hF9wBkECgU1mIWrPCrRQqvPpHV7TBO2',
  'admin'
);
