# Relazione descrittiva del progetto "SafeSchool Hub"

## 1. Titolo del progetto
**SafeSchool Hub – Piattaforma web per la sicurezza informatica scolastica**

---

## 2. Presentazione generale
Il progetto "SafeSchool Hub" è una piattaforma web sviluppata per affrontare in modo pratico alcuni problemi legati alla sicurezza informatica in ambito scolastico. L'idea nasce dall'osservazione di situazioni frequenti all'interno delle scuole, come l'utilizzo di password deboli, la difficoltà nel segnalare problemi tecnici o di sicurezza e la scarsa attenzione verso le buone pratiche digitali.

L'obiettivo del progetto è quello di offrire uno strumento semplice, moderno e utile, in grado di unire gestione delle segnalazioni, sensibilizzazione sulla sicurezza e supporto agli utenti nella creazione e conservazione sicura delle password.

---

## 3. Obiettivi del progetto
Gli obiettivi principali del lavoro sono stati i seguenti:
- realizzare una web app accessibile e facile da usare;
- permettere l'invio di segnalazioni anche da parte di utenti non registrati;
- sviluppare un sistema di autenticazione sicuro;
- integrare strumenti utili alla sicurezza informatica, come un generatore di password e un archivio personale protetto;
- valorizzare competenze di programmazione web, gestione database e progettazione lato server.

---

## 4. Tecnologie utilizzate
Per la realizzazione del progetto sono state utilizzate diverse tecnologie studiate durante il percorso scolastico:
- **PHP** per la logica lato server e la gestione delle API;
- **MySQL/MariaDB** per la progettazione e gestione del database;
- **HTML5** per la struttura delle pagine;
- **CSS3** per la parte grafica e l'interfaccia utente;
- **JavaScript** per le interazioni dinamiche lato client;
- **PDO** per l'accesso sicuro al database tramite query preparate.

Queste tecnologie rendono il progetto pienamente coerente con il percorso di studi dell'indirizzo Informatica e Telecomunicazioni.

---

## 5. Funzionalità principali

### a) Sistema di segnalazione
Gli utenti possono inviare segnalazioni relative a problemi o situazioni che riguardano la sicurezza o l'organizzazione dell'ambiente scolastico. Le segnalazioni possono essere inviate anche senza registrazione, così da facilitare la partecipazione di tutta la comunità scolastica. Ogni segnalazione riceve un codice di tracciamento univoco.

### b) Registrazione e login
È presente un sistema di autenticazione con registrazione utente e accesso tramite email e password. Le password non vengono mai salvate in chiaro, ma protette tramite hashing con algoritmo bcrypt (cost factor 12).

### c) Password Vault
Ogni utente autenticato può salvare in modo riservato alcune credenziali personali in un archivio protetto. I dati sono cifrati con algoritmo AES-256-CBC prima di essere salvati nel database. Questa funzione è stata progettata con finalità didattiche per mostrare l'importanza della protezione delle informazioni sensibili.

### d) Generatore e analizzatore di password
La piattaforma è in grado di generare password casuali robuste (18 caratteri) o passphrase a 4 parole, e di valutare il livello di sicurezza delle password inserite attraverso 12 criteri distinti, aiutando l'utente a comprendere cosa rende una password sicura.

### e) Profilo utente e gamification
Il sistema prevede badge, punteggi XP e piccoli elementi di gamification per incentivare comportamenti corretti e una partecipazione più attiva alla piattaforma.

### f) Pannello amministratore
Gli utenti con ruolo admin possono visualizzare, aggiornare lo stato ed eliminare le segnalazioni ricevute tramite un'interfaccia dedicata.

---

## 6. Aspetti di sicurezza
Uno degli elementi più importanti del progetto riguarda la sicurezza informatica. In particolare:
- le password degli utenti sono protette con hashing bcrypt (cost 12), con ricalcolo automatico se il cost factor cambia;
- l'accesso al database avviene tramite PDO con prepared statements, eliminando il rischio di SQL injection;
- il sistema distingue tra utenti normali e amministratori, con controllo dei permessi lato server;
- il vault utilizza cifratura AES-256-CBC con IV casuale per ogni voce;
- la configurazione (credenziali DB, chiave vault) è separata dal codice applicativo in un file dedicato.

Questo aspetto rappresenta uno dei punti di maggiore originalità del progetto, poiché unisce sviluppo web e attenzione concreta ai principi della cybersecurity.

---

## 7. Originalità e valore formativo
Il progetto non si limita a proporre un semplice sito web informativo, ma realizza un sistema interattivo con finalità pratiche e formative. L'originalità consiste nell'aver integrato in un'unica piattaforma funzionalità diverse ma coerenti tra loro: segnalazioni anonime, autenticazione sicura, analisi delle password, archivio cifrato e gamification.

Dal punto di vista formativo, il lavoro ha permesso di approfondire:
- la progettazione di basi di dati relazionali;
- la gestione delle sessioni utente in PHP;
- l'uso della programmazione lato server con architettura API REST;
- l'organizzazione di un progetto web completo con separazione frontend/backend;
- i principi fondamentali della sicurezza applicata alle web app.

---

## 8. Conclusione
"SafeSchool Hub" rappresenta un progetto coerente con il percorso di studi dell'indirizzo Informatica e Telecomunicazioni e con le competenze sviluppate durante gli anni scolastici. Attraverso questo lavoro è stato realizzato un elaborato utile, originale e tecnicamente strutturato, capace di unire aspetti pratici, attenzione alla sicurezza e applicazione concreta delle conoscenze acquisite.

---

*Progetto realizzato da Daniele Signorile — ITT Enrico Fermi, Francavilla Fontana (BR)*  
*Indirizzo: Informatica e Telecomunicazioni — A.S. 2025/2026*
