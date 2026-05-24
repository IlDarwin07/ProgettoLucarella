<?php
/**
 * Configurazione centralizzata di SafeSchool Hub
 * Modifica questo file per adattare il progetto al tuo ambiente.
 */

// --- Database ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'safeschool');
define('DB_USER', 'root');
define('DB_PASS', '');

// --- Chiave AES per il vault password (32 byte) ---
// IMPORTANTE: in produzione sostituire con una chiave casuale generata con:
// php -r "echo bin2hex(random_bytes(32));"
define('VAULT_KEY', hex2bin('0000000000000000000000000000000000000000000000000000000000000001'));
