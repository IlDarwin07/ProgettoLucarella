-- SafeSchool Hub — schema completo
-- Per creare l'utente admin dopo l'importazione, esegui:
--   php -r "echo password_hash('LA_TUA_PASSWORD', PASSWORD_BCRYPT, ['cost'=>12]);"
-- e inserisci manualmente l'hash nella tabella users.

CREATE DATABASE IF NOT EXISTS safeschool CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE safeschool;

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120)  NOT NULL,
  email         VARCHAR(190)  NOT NULL UNIQUE,
  password_hash VARCHAR(255)  NOT NULL,
  role          ENUM('user','admin','root') NOT NULL DEFAULT 'user',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reports (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NULL,
  name          VARCHAR(120)  NOT NULL,
  category      VARCHAR(120)  NOT NULL,
  title         VARCHAR(190)  NOT NULL,
  description   TEXT          NOT NULL,
  priority      ENUM('Bassa','Media','Alta') DEFAULT 'Media',
  status        VARCHAR(40)   DEFAULT 'Aperta',
  tracking_code VARCHAR(20)   NOT NULL UNIQUE,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vault_items (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  site_name    VARCHAR(120)  NOT NULL,
  username     VARCHAR(190)  NOT NULL,
  password_enc TEXT          NOT NULL,
  notes        TEXT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
