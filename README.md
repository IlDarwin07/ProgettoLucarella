# SafeSchool Hub 🛡️

> Piattaforma web per la sicurezza informatica scolastica: segnalazioni, gestione password e formazione degli studenti.

---

## 📌 Problema e Motivazione

Nelle scuole, la sicurezza informatica è spesso trascurata: password deboli, account condivisi, incidenti non segnalati. **SafeSchool Hub** nasce per risolvere questi problemi offrendo uno strumento concreto e accessibile a studenti e personale scolastico.

---

## 🚀 Funzionalità

| Funzione | Descrizione |
|---|---|
| **Segnalazioni pubbliche** | Chiunque (anche senza account) può segnalare problemi di sicurezza, con codice di tracciamento |
| **Autenticazione sicura** | Login/registrazione con password hashate via `password_hash()` bcrypt cost-12 |
| **Password Vault** | Gestore password cifrato con AES-256-CBC, accessibile solo all'utente autenticato |
| **Analizzatore password** | 12 criteri di sicurezza valutati in tempo reale |
| **Generatore password** | Password casuali a 18 caratteri o passphrase a 4 parole |
| **Sistema badge/XP** | Gamification per incentivare comportamenti sicuri |
| **Pannello admin** | Gestione segnalazioni, cambio stato, eliminazione |
| **Profilo utente** | Personalizzabile con classe, bio, colore avatar, statistiche |

---

## 🛠️ Tecnologie Utilizzate

- **Backend**: PHP 8+ con architettura API REST (JSON)
- **Database**: MySQL / MariaDB — PDO con prepared statements (anti SQL-injection)
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Sicurezza**: bcrypt (cost 12), AES-256-CBC, CSRF-safe sessions
- **Server**: Apache (XAMPP / LAMP / Raspberry Pi)

---

## 📁 Struttura del Progetto

```
SafeSchoolHub/
├── index.php           ← Dashboard principale (frontend SPA)
├── api.php             ← API backend REST (JSON)
├── login.php           ← Pagina di login
├── register.php        ← Registrazione utente
├── profile.php         ← Profilo e badge
├── guest.php           ← Vista ospite (segnalazioni pubbliche)
├── logout.php          ← Distruzione sessione
├── includes/
│   ├── db.php          ← Connessione PDO (legge da .env)
│   └── auth.php        ← Helper sessione
└── sql/
    └── schema.sql      ← Schema database completo
```

---

## ⚙️ Installazione Rapida

### Prerequisiti
- PHP 8.0+
- MySQL / MariaDB
- Apache (XAMPP, LAMP o Raspberry Pi con Apache)

### Passi

1. **Clona il repository**
   ```bash
   git clone https://github.com/IlDarwin07/ProgettoLucarella.git
   cd ProgettoLucarella
   ```

2. **Configura le credenziali** — copia il file di esempio e modificalo:
   ```bash
   cp .env.example .env
   ```
   Modifica `.env` con i tuoi dati:
   ```
   DB_HOST=localhost
   DB_NAME=safeschool
   DB_USER=root
   DB_PASS=
   VAULT_KEY=genera_una_chiave_casuale_a_32_byte_qui
   ```
   Per generare una chiave sicura per il vault:
   ```bash
   php -r "echo bin2hex(random_bytes(32));"
   ```

3. **Importa il database**
   ```bash
   mysql -u root -p < sql/schema.sql
   ```

4. **Avvia il server**
   Copia i file nella cartella `htdocs/` di XAMPP (o `/var/www/html/` su Linux) e apri `index.php` nel browser.

---

## 🔒 Sicurezza — Scelte Tecniche

Questo progetto applica le seguenti best practice di sicurezza informatica:

- **Password hashing**: `password_hash()` con algoritmo bcrypt e cost factor 12. Ricalcolo automatico dell'hash se il cost factor cambia (`password_needs_rehash`).
- **SQL injection prevention**: tutte le query usano PDO con prepared statements — nessuna concatenazione di input utente.
- **Cifratura vault**: le password salvate nel vault sono cifrate con AES-256-CBC. L'IV è randomico per ogni cifratura. La chiave è esterna al codice sorgente (file `.env`).
- **Separazione ruoli**: controllo `role === 'admin'` lato server per ogni operazione privilegiata.
- **Session security**: distruzione completa della sessione al logout.

---

## 🎓 Contesto Scolastico

Progetto sviluppato da **Daniele Signorile** — studente dell'ITT Enrico Fermi di Francavilla Fontana (BR), indirizzo Informatica e Telecomunicazioni, a.s. 2025/2026.

Sviluppato come elaborato originale per la **Borsa di Studio "Prof. Stefano Lucarella"**.

---

## 📄 Licenza

MIT — Libero utilizzo per scopi educativi.
