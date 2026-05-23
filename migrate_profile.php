<?php
/**
 * migrate_profile.php
 * Esegui UNA VOLTA per aggiungere le colonne del profilo alla tabella users.
 * Visita: http://[tuo-server]/ProgettoLucarella/migrate_profile.php
 * Dopo l'esecuzione puoi eliminare questo file.
 */
require_once __DIR__ . '/includes/auth.php';

$cols = [
    'class_section VARCHAR(50) DEFAULT NULL',
    'phone VARCHAR(30) DEFAULT NULL',
    'bio VARCHAR(200) DEFAULT NULL',
    'avatar_color TINYINT UNSIGNED DEFAULT 0',
    'xp INT UNSIGNED DEFAULT 0',
    'quiz_score TINYINT UNSIGNED DEFAULT 0',
    'last_login DATE DEFAULT NULL',
    'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
];

$results = [];
foreach ($cols as $def) {
    $col = explode(' ', $def)[0];
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN $def");
        $results[] = "✅ Aggiunta colonna: $col";
    } catch (\Throwable $e) {
        $results[] = "ℹ️ Colonna già presente: $col";
    }
}

// Tabella badge
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        badge_id VARCHAR(40) NOT NULL,
        earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_ub (user_id, badge_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $results[] = "✅ Tabella user_badges OK";
} catch (\Throwable $e) {
    $results[] = "⚠️ Errore user_badges: " . $e->getMessage();
}

header('Content-Type: text/plain; charset=utf-8');
echo implode("\n", $results) . "\n\nMigrazione completata. Puoi eliminare questo file.";
