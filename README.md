# SafeSchool Hub

SafeSchool Hub è una web app in PHP, MySQL, HTML, CSS e JavaScript.

## Funzioni
- Segnalazioni pubbliche usabili anche senza login
- Registrazione e login utenti con password hashate (password_hash PHP)
- Database MySQL con PDO e prepared statements
- Calcolatore sicurezza password con 12 criteri valutati
- Generatore password casuali (18 char) e passphrase
- Mini password manager scolastico per utenti autenticati (vault)
- Tema chiaro/scuro ottimizzato

## Setup rapido
1. Importa `sql/schema.sql` in MySQL/MariaDB
2. Configura `includes/db.php` con le credenziali del tuo server
3. Avvia con Apache/XAMPP/LAMP
4. Apri `index.php` nel browser

## Struttura
```
├── index.php          <- Frontend HTML/CSS/JS
├── api.php            <- API backend PHP
├── includes/
│   ├── db.php         <- Connessione PDO
│   └── auth.php       <- Sessione utente
└── sql/
    └── schema.sql     <- Schema database
```
