# Relazione elaborato – Borsa di Studio "Prof. Stefano Lucarella"

## 1. Titolo e contesto

**Titolo del progetto:** SafeSchool Hub – piattaforma web per la sicurezza informatica scolastica.

**Autore:** Daniele Signorile, studente dell’ITT "Enrico Fermi" di Francavilla Fontana (BR), indirizzo Informatica e Telecomunicazioni, a.s. 2025/2026.

Il progetto è stato sviluppato come elaborato originale per la partecipazione alla Borsa di Studio "Prof. Stefano Lucarella", con l’obiettivo di valorizzare le competenze tecniche acquisite e di applicarle a un problema reale del contesto scolastico.

## 2. Problema affrontato

Nella pratica quotidiana degli istituti scolastici, la sicurezza informatica è spesso percepita come un aspetto secondario:
- password deboli o riutilizzate;
- account condivisi tra studenti;
- incidenti di sicurezza (furto credenziali, accessi non autorizzati, uso improprio dei dispositivi) che non vengono segnalati;
- scarsa consapevolezza delle buone pratiche di cybersecurity.

Questa situazione espone studenti, docenti e infrastrutture scolastiche a rischi concreti (perdita di dati, violazione della privacy, interruzione dei servizi digitali).

**SafeSchool Hub** nasce per rispondere a questo bisogno: fornire alla comunità scolastica uno strumento unico, semplice da usare e tecnicamente solido, che permetta di segnalare problemi, gestire in modo corretto le credenziali e promuovere comportamenti responsabili nell’uso delle tecnologie.

## 3. Descrizione generale del progetto

SafeSchool Hub è una piattaforma web sviluppata in PHP, MySQL/MariaDB, HTML, CSS e JavaScript, pensata per essere installata su un server della scuola (ad esempio un Raspberry Pi con Apache) e raggiungibile da studenti e personale tramite browser.

Le principali funzionalità sono:
- **Segnalazioni pubbliche:** chiunque, anche senza account, può inviare una segnalazione di problemi di sicurezza (dispositivi lasciati incustoditi, account compromessi, uso improprio delle risorse, ecc.) e ricevere un codice di tracciamento per seguirne lo stato.
- **Autenticazione sicura:** gli studenti possono registrarsi e autenticarsi con un sistema di login che utilizza password gestite in modo sicuro.
- **Password Vault:** ogni utente autenticato dispone di un deposito personale, cifrato, in cui salvare e organizzare le proprie password in modo sicuro.
- **Analizzatore e generatore di password:** la piattaforma offre strumenti per valutare la robustezza delle password e per generare password sicure (o passphrase) senza doverle inventare manualmente.
- **Sistema di badge ed esperienza (XP):** le azioni corrette (come l’uso di password forti, l’aggiornamento del vault, la segnalazione di problemi) vengono premiate con punti XP e badge, per rendere l’educazione alla sicurezza più coinvolgente.
- **Pannello amministrativo:** un’interfaccia dedicata permette agli amministratori di visualizzare, filtrare e gestire le segnalazioni, aggiornando gli stati e intervenendo in caso di incidenti.
- **Account root:** l’account root dispone di una panoramica completa di tutti gli utenti, può visualizzare i loro profili e nominare o revocare il ruolo di amministratore, garantendo un controllo centralizzato della piattaforma.

Il progetto è stato progettato per essere completamente funzionante e installabile in un contesto reale, non solo come prototipo teorico.

## 4. Scelte tecnologiche e architetturali

Dal punto di vista architetturale, SafeSchool Hub segue un modello a **front-end leggero con backend PHP** che espone endpoint in JSON, organizzati in un unico file `api.php`. L’applicazione utilizza:
- **PHP 8+** come linguaggio principale per la logica server;
- **MySQL/MariaDB** per la gestione dei dati (utenti, segnalazioni, vault, badge, log attività);
- **PDO** con prepared statements per tutte le query, in modo da prevenire attacchi SQL injection;
- **HTML5, CSS3 e JavaScript vanilla** per l’interfaccia utente.

La scelta di non utilizzare framework complessi è deliberata: consente di mantenere il progetto più leggero, comprensibile e facilmente installabile in un contesto scolastico, anche su hardware a risorse limitate.

## 5. Aspetti di sicurezza (originalità tecnica)

Un elemento centrale del progetto è l’attenzione alla **sicurezza dei dati** e alla corretta gestione delle credenziali:

- **Password hashing:** le password degli utenti non vengono mai salvate in chiaro nel database. Viene utilizzata la funzione di hashing con algoritmo robusto e cost elevato. In questo modo, anche in caso di compromissione del database, le password non risultano immediatamente leggibili.

- **Cifratura del vault:** le credenziali memorizzate nel vault sono cifrate utilizzando un algoritmo di cifratura simmetrica a chiave forte. Per ogni voce viene generato un IV (Initialization Vector) casuale. La chiave di cifratura non è inserita direttamente nel codice sorgente, ma viene letta da un file di configurazione esterno, in modo da separare le informazioni sensibili dal repository.

- **Separazione e gestione dei ruoli:** ogni azione ad alta criticità (ad esempio la gestione delle segnalazioni o la visualizzazione di determinati dati) verifica lato server il ruolo dell’utente (`user`, `admin`, `root`). L’account root dispone di un elenco completo di tutti gli utenti con i loro profili, può nominarli amministratori o riportarli al ruolo di utente semplice, garantendo così un controllo fine sui privilegi senza dover intervenire direttamente sul database.

- **Gestione sicura delle sessioni:** il sistema di autenticazione prevede la corretta gestione delle sessioni e la distruzione completa della sessione al logout, per ridurre il rischio di utilizzo non autorizzato degli account.

Questi accorgimenti, pur essendo ispirati alle best practice professionali, sono stati applicati in un contesto didattico e documentati in modo da poter essere spiegati anche a coetanei.

## 6. Coerenza con il percorso di studi

Il progetto è pienamente coerente con il percorso di studi di Informatica e Telecomunicazioni dell’ITT "Enrico Fermi". Le diverse discipline trovano applicazione diretta:

- **Informatica:** progettazione e sviluppo dell’applicazione web, gestione di strutture dati, implementazione di algoritmi per la sicurezza delle password e la gestione delle segnalazioni.
- **Tecnologie e Progettazione di Sistemi Informatici:** progettazione dell’architettura client–server, definizione delle entità del database, analisi dei casi d’uso e realizzazione di una soluzione installabile su server reale.
- **TPSEE/Telecomunicazioni:** consapevolezza dei rischi legati alle reti e ai sistemi connessi, e implementazione di strumenti per la protezione delle informazioni.
- **Educazione civica e cittadinanza digitale:** promozione di un uso consapevole e responsabile delle tecnologie, sensibilizzazione alla segnalazione di comportamenti scorretti e alla protezione dei dati personali.

In questo senso, SafeSchool Hub non è solo un prodotto tecnico, ma anche uno strumento educativo che può essere integrato nelle attività scolastiche.

## 7. Chiarezza espositiva e possibilità di utilizzo

La piattaforma è accompagnata da:
- una documentazione tecnica che descrive installazione, requisiti e scelte di sicurezza;
- un’interfaccia utente pensata per essere utilizzabile anche da persone non esperte, con pagine semplici, testi chiari e messaggi di feedback in italiano;
- una struttura modulare che permette, in futuro, di aggiungere nuove funzionalità (ad esempio percorsi formativi, quiz sulla sicurezza, notifiche e-mail).

Durante la presentazione alla Commissione, il progetto può essere mostrato tramite:
- demo in locale o su server scolastico;
- screenshot delle principali schermate (login, dashboard, segnalazioni, vault, pannello admin);
- eventuale breve video che evidenzi le fasi principali di utilizzo.

## 8. Conclusioni

SafeSchool Hub rappresenta, per chi lo ha realizzato, un "capolavoro" nel senso indicato dal regolamento della Borsa di Studio: un elaborato originale, tecnicamente solido e coerente con il percorso di studi, che affronta un problema reale della vita scolastica.

Attraverso questo progetto è stato possibile unire teoria e pratica, consolidare competenze di programmazione e sicurezza, e allo stesso tempo proporre uno strumento potenzialmente utile alla comunità scolastica. In linea con lo spirito della Borsa "Prof. Stefano Lucarella", il lavoro vuole dimostrare come la passione per l’informatica possa tradursi in soluzioni concrete, innovative e orientate al bene comune.
