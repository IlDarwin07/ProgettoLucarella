<?php
/**
 * Configurazione centralizzata di SafeSchool Hub
 * Modifica questo file per adattare il progetto al tuo ambiente.
 *
 * VAULT_KEY: deve essere esattamente 32 byte (64 caratteri hex).
 * Genera la tua chiave con:
 *   php -r "echo bin2hex(random_bytes(32));"
 */

// --- Database ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'safeschool');
define('DB_USER', 'root');
define('DB_PASS', '');

// --- Chiave AES-256-CBC per il vault password (esattamente 32 byte) ---
// IMPORTANTE: sostituire con una chiave casuale in produzione!
define('VAULT_KEY', hex2bin('0000000000000000000000000000000000000000000000000000000000000000'));
