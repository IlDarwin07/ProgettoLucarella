DROP DATABASE IF EXISTS safeschool_hub;
CREATE DATABASE safeschool_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE safeschool_hub;

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120)  NOT NULL,
  email         VARCHAR(190)  NOT NULL UNIQUE,
  password_hash VARCHAR(255)  NOT NULL,
  role          ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reports (
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

CREATE TABLE vault_items (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  user_id        INT NOT NULL,
  site_name      VARCHAR(120)  NOT NULL,
  username       VARCHAR(190)  NOT NULL,
  password_plain TEXT          NOT NULL,
  notes          TEXT NULL,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- root@itisff.it / admin1234  (bcrypt cost=12)
INSERT INTO users (name, email, password_hash, role) VALUES (
  'Root Administrator',
  'root@itisff.it',
  '$2y$12$YkaxOfsUVySKfniUCV0a4O7FQ8GPTJawpzOY6EI9GeBVP/b9tPzuu',
  'admin'
);
