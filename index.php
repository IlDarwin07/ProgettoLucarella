<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SafeSchool Hub</title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f7f6f2;--surface:#ffffff;--surface-2:#f9f8f5;--border:#dcd9d5;
  --text:#28251d;--muted:#7a7974;--faint:#bab9b4;
  --primary:#01696f;--primary-h:#0c4e54;--primary-hl:rgba(1,105,111,.12);
  --error:#a12c7b;--success:#437a22;--warning:#964219;
  --admin:#a13544;
  --radius:0.5rem;--radius-sm:0.375rem;--radius-lg:0.75rem;
  --shadow:0 4px 24px rgba(0,0,0,.08);
  --font:'Satoshi',system-ui,sans-serif;
  --transition:180ms cubic-bezier(.16,1,.3,1);
}
[data-theme="dark"]{
  --bg:#171614;--surface:#1c1b19;--surface-2:#201f1d;--border:#393836;
  --text:#cdccca;--muted:#797876;--faint:#5a5957;
  --primary:#4f98a3;--primary-h:#227f8b;--primary-hl:rgba(79,152,163,.15);
  --error:#d163a7;--success:#6daa45;--warning:#bb653b;
  --admin:#dd6974;
  --shadow:0 4px 24px rgba(0,0,0,.35);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased;scroll-behavior:smooth}
body{min-height:100dvh;background:var(--bg);color:var(--text);font-family:var(--font);font-size:1rem;line-height:1.6}
img,svg{display:block}
a{color:inherit;text-decoration:none}
button{font:inherit;color:inherit;cursor:pointer;border:none;background:none}

/* === TOPBAR === */
.topbar{position:sticky;top:0;z-index:100;background:var(--bg);border-bottom:1px solid var(--border);
  padding:.5rem 1.25rem;display:flex;align-items:center;gap:12px}
.topbar-logo{display:flex;align-items:center;gap:9px;font-weight:700;font-size:1rem;margin-right:auto}
.topbar-logo svg{color:var(--primary)}
.session-dot{width:9px;height:9px;border-radius:50%;background:var(--faint);flex-shrink:0}
.session-dot.on{background:var(--success)}
.session-dot.admin{background:var(--admin)}
#sessionLabel{font-size:.8rem;color:var(--muted)}

/* === MAIN NAV TABS === */
.main-nav{display:flex;align-items:center;gap:2px;padding:.25rem 1.25rem 0;
  border-bottom:1px solid var(--border);overflow-x:auto;background:var(--bg);
  position:sticky;top:48px;z-index:99}
.nav-tab{display:flex;align-items:center;gap:7px;padding:.6rem .9rem;font-size:.85rem;font-weight:500;
  color:var(--muted);border-bottom:2px solid transparent;border-radius:0;
  white-space:nowrap;transition:color var(--transition)}
.nav-tab:hover{color:var(--text)}
.nav-tab.active{color:var(--primary);border-bottom-color:var(--primary)}
.nav-tab .tab-icon{display:flex;align-items:center;opacity:.7}
.nav-tab .admin-dot{width:7px;height:7px;border-radius:50%;background:var(--admin);display:inline-block}

/* === SECTIONS === */
.section{display:none}.section.active{display:block}
.wrap{max-width:960px;margin:0 auto;padding:1.75rem 1.25rem}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
@media(max-width:700px){.two-col{grid-template-columns:1fr}}

/* === CARDS === */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}
.card-header{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px}
.card-header h3{font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px}
.card-body{padding:14px 16px}
.intro-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.25rem}
.intro-box h1{font-size:1.4rem;margin-bottom:.5rem}

/* === KPI === */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:.75rem;margin-bottom:1.25rem}
.kpi{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1rem;text-align:center}
.kpi-label{font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em}
.kpi-val{font-size:2rem;font-weight:700;color:var(--primary);line-height:1.1;margin-top:.25rem}

/* === BUTTONS === */
.btn{padding:.55rem 1rem;border-radius:var(--radius);font-size:.88rem;font-weight:600;
  transition:background var(--transition),opacity var(--transition),transform .12s,box-shadow var(--transition);
  display:inline-flex;align-items:center;gap:6px}
.btn:active{transform:scale(.97)}
.btn-sm{padding:.38rem .75rem;font-size:.8rem}
.btn-primary{background:var(--primary);color:#fff}.btn-primary:hover{background:var(--primary-h)}
.btn-ghost{background:transparent;border:1px solid var(--border);color:var(--text)}.btn-ghost:hover{background:var(--surface-2,var(--surface))}
.btn-danger{background:transparent;border:1px solid var(--error);color:var(--error)}.btn-danger:hover{background:color-mix(in oklab,var(--error) 10%,transparent)}
.btn-admin{background:var(--admin);color:#fff}
.btn:disabled{opacity:.5;cursor:not-allowed}

/* === FORMS === */
.field{display:flex;flex-direction:column;gap:.35rem}
.label{font-size:.78rem;font-weight:700;color:var(--muted);letter-spacing:.05em;text-transform:uppercase}
.input{padding:.55rem .75rem;border:1px solid var(--border);border-radius:var(--radius);
  background:var(--bg);color:var(--text);font-size:.92rem;font-family:inherit;
  transition:border var(--transition),box-shadow var(--transition)}
.input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-hl)}
textarea.input{resize:vertical}
form.grid-form{display:grid;gap:14px}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
@media(max-width:480px){.row2{grid-template-columns:1fr}}
.anon-row{display:flex;align-items:flex-start;gap:8px;font-size:.88rem;cursor:pointer}
.anon-row input{margin-top:.2rem;accent-color:var(--primary)}

/* === LIST ITEMS === */
.list{display:flex;flex-direction:column;gap:6px}
.item{padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:.85rem}
.item-head{display:flex;justify-content:space-between;align-items:center;gap:6px;font-weight:600;margin-bottom:3px}
.item-meta{display:flex;flex-wrap:wrap;gap:6px;align-items:center;margin-bottom:4px;font-size:.78rem;color:var(--muted)}
.item-body{font-size:.82rem;color:var(--muted)}

/* === BADGES === */
.badge{display:inline-flex;align-items:center;padding:.18rem .55rem;border-radius:var(--radius-sm);font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
.b-alta{background:rgba(161,44,123,.12);color:var(--error)}
.b-media{background:rgba(209,153,0,.12);color:#b07a00}
.b-bassa{background:rgba(67,122,34,.12);color:var(--success)}
.b-aperta{background:rgba(1,105,111,.12);color:var(--primary)}
.b-risolta{background:rgba(67,122,34,.12);color:var(--success)}
.b-chiusa{background:rgba(90,89,87,.12);color:var(--faint)}
.b-bullismo{background:rgba(161,44,123,.12);color:var(--error)}

/* === SCORE === */
.score-wrap{height:8px;background:var(--border);border-radius:9999px;overflow:hidden;margin:.75rem 0 .25rem}
.score-bar{height:100%;background:var(--primary);border-radius:9999px;transition:width .4s ease,background .3s}
.score-info{display:flex;justify-content:space-between;align-items:center}
.check-row{display:flex;align-items:center;gap:8px;padding:5px 6px;border-radius:var(--radius-sm);font-size:.83rem;color:var(--muted);transition:background .15s}
.check-row.pass{color:var(--text)}
.check-row.pass .check-icon{color:var(--primary)}
.check-icon{display:flex;align-items:center;flex-shrink:0}

/* === VAULT === */
.vault-card{padding:14px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);
  background:var(--surface);font-size:.85rem;display:flex;flex-direction:column;gap:5px}
.vault-row{display:flex;justify-content:space-between;align-items:center;gap:8px}
.pw-mask{font-family:monospace;letter-spacing:.15em;cursor:pointer;user-select:none;font-size:.8rem;color:var(--muted)}
.pw-mask.shown{letter-spacing:normal;color:var(--text)}

/* === MSG === */
.msg{padding:.6rem .9rem;border-radius:var(--radius-sm);font-size:.84rem;margin-top:8px}
.msg.ok{background:color-mix(in oklab,var(--primary) 12%,transparent);color:var(--primary)}
.msg.err{background:color-mix(in oklab,var(--error) 12%,transparent);color:var(--error)}

/* === MISC === */
.tiny{font-size:.8rem}.muted{color:var(--muted)}.mt8{margin-top:.5rem}.mt16{margin-top:1rem}
.hidden{display:none!important}
.flex-between{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.divider{border:none;border-top:1px solid var(--border);margin:1.25rem 0}
.feature-chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:.75rem}
.chip{padding:.25rem .7rem;border:1px solid var(--border);border-radius:9999px;font-size:.78rem;color:var(--muted)}
.ic{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.theme-btn{background:var(--surface);border:1px solid var(--border);border-radius:50%;
  width:34px;height:34px;display:grid;place-items:center;color:var(--muted);transition:background var(--transition)}
.theme-btn:hover{background:var(--border)}

/* === ADMIN TABLE === */
.admin-table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.85rem}
thead th{padding:8px 10px;text-align:left;font-size:.75rem;
  color:var(--muted);text-transform:uppercase;letter-spacing:.05em;
  border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:8px 10px;border-bottom:1px solid var(--border);vertical-align:middle}
.status-sel{background:transparent;border:1px solid var(--border);border-radius:var(--radius-sm);
  color:var(--text);font-size:.78rem;padding:.2rem .5rem;font-family:inherit}

/* === AUTH MODAL === */
.modal{display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.45);
  align-items:center;justify-content:center;padding:1rem}
.modal.open{display:flex}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);
  box-shadow:var(--shadow);width:100%;max-width:380px;padding:1.5rem}
.modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem}
.modal-header h3{font-size:1.05rem;font-weight:700}
.modal-tabs{display:flex;gap:4px;margin-bottom:1.25rem}
.modal-tab{flex:1;padding:.45rem;text-align:center;font-size:.85rem;font-weight:600;
  border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--muted);transition:all var(--transition)}
.modal-tab.active{background:var(--primary);color:#fff;border-color:var(--primary)}
</style>
</head>
<body>

<!-- ===== TOPBAR ===== -->
<header class="topbar">
  <a href="index.php" class="topbar-logo">
    <svg width="26" height="26" viewBox="0 0 32 32" fill="none">
      <rect x="2" y="2" width="28" height="28" rx="7" fill="var(--primary)" opacity=".12"/>
      <path d="M16 6 L26 10 L26 18 C26 23 21 27 16 29 C11 27 6 23 6 18 L6 10 Z" fill="var(--primary)" opacity=".25"/>
      <path d="M16 8 L24 11.5 L24 18 C24 22 20 25.5 16 27.5 C12 25.5 8 22 8 18 L8 11.5 Z" stroke="var(--primary)" stroke-width="1.5" fill="none"/>
      <path d="M12 16 L15 19 L20 13" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    SafeSchool Hub
  </a>
  <span class="session-dot" id="sessionDot"></span>
  <span id="sessionLabel">Caricamento…</span>
  <button class="theme-btn" id="themeBtn" aria-label="Cambia tema">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="themeIcon">
      <circle cx="12" cy="12" r="5"/>
      <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
      <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
      <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
      <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
    </svg>
  </button>
  <button class="btn btn-primary btn-sm hidden" id="authBtn">
    <svg class="ic" width="14" height="14" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
    Accedi
  </button>
  <button class="btn btn-primary btn-sm hidden" id="logoutBtn">
    <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    Logout
  </button>
</header>

<!-- ===== NAVIGATION TABS ===== -->
<nav class="main-nav" id="mainNav">
  <button class="nav-tab active" data-tab="home">
    <span class="tab-icon"><svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
    Home
  </button>
  <button class="nav-tab" data-tab="segnalazioni">
    <span class="tab-icon"><svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
    Segnalazioni
  </button>
  <button class="nav-tab" data-tab="strumenti">
    <span class="tab-icon"><svg class="ic" width="15" height="15" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
    Strumenti
  </button>
  <button class="nav-tab hidden" data-tab="admin" id="adminTab">
    <span class="tab-icon"><svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
    Admin <span class="admin-dot"></span>
  </button>
</nav>

<main>

<!-- ===== HOME ===== -->
<div class="section active" id="sec-home">
<div class="wrap">

  <div class="intro-box">
    <h1>Piattaforma sicurezza digitale scolastica</h1>
    <p>SafeSchool Hub offre strumenti pratici per la sicurezza informatica a scuola: puoi segnalare problemi, testare le tue password e conservare le credenziali scolastiche in modo sicuro. <strong>Non serve login</strong> per le funzioni di base.</p>
    <div class="feature-chips">
      <span class="chip">🚨 Segnalazioni</span>
      <span class="chip">🔒 Valutatore password</span>
      <span class="chip">⚙️ Generatore</span>
      <span class="chip">🗄️ Vault personale</span>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="kpi"><div class="kpi-label">Segnalazioni</div><div class="kpi-val" id="kpiReports">&mdash;</div></div>
    <div class="kpi"><div class="kpi-label">Elementi vault</div><div class="kpi-val" id="kpiVault">&mdash;</div></div>
    <div class="kpi"><div class="kpi-label">Utente</div><div class="kpi-val" style="font-size:1rem;line-height:2" id="homeSessionInfo">&mdash;</div></div>
  </div>

  <div class="two-col">
    <div class="card">
      <div class="card-header"><h3>Funzionalità</h3></div>
      <div class="card-body list" style="gap:0">
        <div class="item">
          <div class="item-head">🚨 Segnalazioni</div>
          <div class="item-body">Chiunque, anche senza account, può inviare una segnalazione: bullismo, problemi tecnici, privacy o sicurezza di rete. Puoi farlo in modo completamente <strong>anonimo</strong>. Ricevi un codice di tracciamento univoco.</div>
        </div>
        <div class="item">
          <div class="item-head">🔒 Valutatore &amp; ⚙️ Generatore password</div>
          <div class="item-body">Analizza la forza della tua password in tempo reale (lato client, mai inviata al server), oppure genera istantaneamente una password sicura o una passphrase. Tutto nella scheda <strong>Strumenti</strong>.</div>
        </div>
        <div class="item">
          <div class="item-head">🗄️ Vault personale</div>
          <div class="item-body">Con un account puoi salvare le credenziali di Moodle, Registro, GitHub, Drive nel tuo vault privato su database MySQL. Accessibile dalla scheda <strong>Strumenti</strong>.</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h3>Consigli rapidi</h3></div>
      <div class="card-body">
        <div id="quickList" class="list"></div>
        <button class="btn btn-primary hidden" id="homeLoginBtn" style="width:100%;margin-top:16px" onclick="openAuth()">
          <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Accedi o registrati
        </button>
        <button class="btn btn-ghost hidden" id="homeGoVault" style="width:100%;margin-top:8px" onclick="switchTab('strumenti')">
          Apri il mio vault
        </button>
      </div>
    </div>
  </div>
</div>
</div>

<!-- ===== SEGNALAZIONI ===== -->
<div class="section" id="sec-segnalazioni">
<div class="wrap">
  <div class="flex-between mt8" style="margin-bottom:20px">
    <div>
      <h2 style="font-size:1.5rem;display:flex;align-items:center;gap:10px">
        <svg class="ic" width="20" height="20" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Segnalazioni
      </h2>
      <p class="tiny muted mt8">Invia una segnalazione &mdash; anche in modo anonimo. Non è richiesto alcun account.</p>
    </div>
  </div>
  <div class="two-col">
    <!-- Form -->
    <div class="card">
      <div class="card-header"><h3>Nuova segnalazione</h3></div>
      <div class="card-body">
        <form class="grid-form" id="reportForm">
          <label class="anon-row">
            <input type="checkbox" id="anonCheck" name="anonymous" value="1">
            <span><strong>Invia in modo anonimo</strong> &mdash; il nome non sarà visibile</span>
          </label>
          <div class="row2">
            <div class="field">
              <label class="label">Nome / Classe</label>
              <input class="input" name="name" id="nameInput" placeholder="Opzionale">
            </div>
            <div class="field">
              <label class="label">Categoria</label>
              <select class="input" name="category">
                <option>Bullismo</option>
                <option>Problema tecnico</option>
                <option>Privacy / Dati personali</option>
                <option>Sicurezza rete scolastica</option>
                <option>Materiali didattici</option>
                <option>Altro</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label class="label">Titolo *</label>
            <input class="input" name="title" placeholder="Descrivi brevemente il problema" required>
          </div>
          <div class="field">
            <label class="label">Descrizione *</label>
            <textarea class="input" name="description" rows="4" placeholder="Dettagli, quando è successo, dove…" required></textarea>
          </div>
          <div class="row2">
            <div class="field">
              <label class="label">Priorità</label>
              <select class="input" name="priority">
                <option>Media</option><option>Alta</option><option>Bassa</option>
              </select>
            </div>
            <button class="btn btn-primary" type="submit" style="align-self:end">
              <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Invia segnalazione
            </button>
          </div>
        </form>
        <div id="reportMsg"></div>
      </div>
    </div>
    <!-- Lista ultime segnalazioni -->
    <div class="card">
      <div class="card-header">
        <h3>Ultime segnalazioni pubbliche</h3>
        <span class="badge b-bassa tiny">aggiornate in tempo reale</span>
      </div>
      <div class="card-body">
        <div id="reportsList" class="list"></div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- ===== STRUMENTI ===== -->
<div class="section" id="sec-strumenti">
<div class="wrap">
  <div style="margin-bottom:20px">
    <h2 style="font-size:1.5rem;display:flex;align-items:center;gap:10px">
      <svg class="ic" width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Strumenti
    </h2>
    <p class="tiny muted mt8">Generatore di password sicure, valutatore e vault personale. Tutti in un unico posto.</p>
  </div>

  <!-- ── GENERATORE ── -->
  <div class="two-col">
    <!-- Generatore -->
    <div class="card">
      <div class="card-header">
        <h3>
          <svg class="ic" width="17" height="17" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          Generatore password
        </h3>
      </div>
      <div class="card-body">
        <p class="tiny muted" style="margin-bottom:16px">Genera una password casuale da 18 caratteri oppure una passphrase facile da ricordare composta da parole reali.</p>
        <div class="row2">
          <button class="btn btn-primary" id="genRandom" type="button">
            <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/></svg>
            Genera casuale
          </button>
          <button class="btn btn-ghost" id="genPhrase" type="button">
            <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Genera passphrase
          </button>
        </div>
        <div style="margin-top:14px;display:flex;gap:8px;align-items:center">
          <input class="input" id="generatedPw" readonly placeholder="La password apparirà qui…">
          <button class="btn btn-ghost btn-sm" id="copyPw" type="button">
            <svg class="ic" width="14" height="14" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copia
          </button>
        </div>
        <div id="genMsg" class="muted tiny" style="margin-top:8px"></div>
        <div class="divider"></div>
        <p class="tiny muted">Dopo aver generato una password, puoi testarla subito nel <strong>Valutatore</strong> qui sotto oppure salvarla nel tuo <strong>Vault</strong>.</p>
      </div>
    </div>
    <!-- Checklist -->
    <div class="card">
      <div class="card-header">
        <h3>
          <svg class="ic" width="17" height="17" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          Checklist sicurezza (12 regole)
        </h3>
      </div>
      <div class="card-body">
        <div id="advChecklist" class="list"></div>
      </div>
    </div>
  </div>

  <!-- ── VALUTATORE PASSWORD ── -->
  <div style="margin-top:2.5rem">
    <hr style="border:none;border-top:1px solid var(--border);margin-bottom:1.75rem">
    <div style="margin-bottom:20px">
      <h2 style="font-size:1.35rem;display:flex;align-items:center;gap:10px">
        <svg class="ic" width="18" height="18" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Valutatore password
      </h2>
      <p class="tiny muted mt8">La password viene analizzata localmente nel browser &mdash; non viene mai inviata al server.</p>
    </div>
    <div class="two-col">
      <div class="card">
        <div class="card-header"><h3>Inserisci una password da testare</h3></div>
        <div class="card-body">
          <div class="field">
            <label class="label">Password</label>
            <input class="input" id="scoreInput" type="password" placeholder="Digita o incolla qui…" autocomplete="off">
          </div>
          <div class="score-wrap"><div class="score-bar" id="scoreBar"></div></div>
          <div class="score-info">
            <span id="scoreText" class="muted tiny">Punteggio: 0 / 100</span>
            <span id="scoreLbl" class="tiny"></span>
          </div>
          <div class="mt16">
            <p class="tiny muted">Dopo aver testato la password, puoi salvarla nel tuo <strong>Vault</strong> qui sotto.</p>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3>
            <svg class="ic" width="17" height="17" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Criteri valutati (12)
          </h3>
        </div>
        <div class="card-body">
          <div id="scoreChecks" class="list"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── VAULT ── -->
  <div style="margin-top:2.5rem">
    <hr style="border:none;border-top:1px solid var(--border);margin-bottom:1.75rem">
    <div style="margin-bottom:20px">
      <h2 style="font-size:1.35rem;display:flex;align-items:center;gap:10px">
        <svg class="ic" width="18" height="18" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Il mio Vault
      </h2>
      <p class="tiny muted mt8">Salva le credenziali dei servizi scolastici. Accessibile solo dopo il login.</p>
    </div>
    <div class="two-col">
      <div class="card">
        <div class="card-header"><h3>Aggiungi credenziale</h3></div>
        <div class="card-body">
          <div id="vaultGuestMsg" class="muted">
            <p style="display:flex;align-items:center;gap:8px">
              <svg class="ic" width="16" height="16" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Accedi o crea un account per usare il vault personale.
            </p>
            <button class="btn btn-primary mt16" id="vaultLoginBtn">
              <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
              Accedi / Registrati
            </button>
          </div>
          <form id="vaultForm" class="grid-form hidden">
            <div class="field"><label class="label">Servizio</label><input class="input" name="site_name" placeholder="es. Moodle, Registro, GitHub…" required></div>
            <div class="row2">
              <div class="field"><label class="label">Username / Email</label><input class="input" name="username" placeholder="username@scuola.it" required></div>
              <div class="field"><label class="label">Password</label><input class="input" type="password" name="password_plain" placeholder="••••••••" required></div>
            </div>
            <div class="field"><label class="label">Note</label><textarea class="input" name="notes" rows="2" placeholder="Note opzionali…"></textarea></div>
            <button class="btn btn-primary" type="submit">
              <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Salva nel vault
            </button>
          </form>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3>Credenziali salvate</h3>
          <span class="kpi-val" id="kpiVault2" style="font-size:1.3rem">&mdash;</span>
        </div>
        <div class="card-body">
          <div id="vaultList" class="list"></div>
        </div>
      </div>
    </div>
  </div>

</div>
</div>

<!-- ===== ADMIN ===== -->
<div class="section hidden" id="sec-admin">
<div class="wrap">
  <div class="flex-between" style="margin-bottom:20px">
    <div>
      <h2 style="font-size:1.5rem;display:flex;align-items:center;gap:10px">
        <svg class="ic" width="20" height="20" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Pannello Admin
      </h2>
      <p class="tiny muted mt8">Visibile solo agli amministratori. Gestisci tutte le segnalazioni ricevute.</p>
    </div>
    <button class="btn btn-admin btn-sm" onclick="loadAdminReports()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
      Aggiorna
    </button>
  </div>
  <div class="kpi-grid">
    <div class="kpi"><div class="kpi-label">Totale segnalazioni</div><div class="kpi-val" id="adminTotal">&mdash;</div></div>
    <div class="kpi"><div class="kpi-label">Aperte</div><div class="kpi-val" id="adminAperte">&mdash;</div></div>
    <div class="kpi"><div class="kpi-label">Risolte</div><div class="kpi-val" id="adminRisolte">&mdash;</div></div>
  </div>
  <div class="card">
    <div class="card-header"><h3>Tutte le segnalazioni</h3></div>
    <div class="card-body" style="padding:0">
      <div class="admin-table-wrap">
        <table id="adminTable">
          <thead>
            <tr>
              <th>ID</th><th>Autore</th><th>Categoria</th><th>Titolo</th>
              <th>Priorità</th><th>Stato</th><th>Codice</th><th>Azioni</th>
            </tr>
          </thead>
          <tbody id="adminTbody">
            <tr><td colspan="8" class="muted tiny" style="padding:20px">Caricamento…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>

<!-- ===== AUTH MODAL ===== -->
<div class="modal" id="authModal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Accesso / Registrazione</h3>
      <button id="closeAuth" aria-label="Chiudi">
        <svg class="ic" width="18" height="18" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-tabs">
      <button class="modal-tab active" data-mtab="login">Accedi</button>
      <button class="modal-tab" data-mtab="register">Registrati</button>
    </div>
    <!-- Login form -->
    <div id="mtab-login">
      <form class="grid-form" id="loginForm">
        <div class="field"><label class="label">Email</label><input class="input" name="email" type="email" placeholder="email@scuola.it" required></div>
        <div class="field"><label class="label">Password</label><input class="input" name="password" type="password" placeholder="••••••••" required></div>
        <button class="btn btn-primary" type="submit">Accedi</button>
      </form>
    </div>
    <!-- Register form -->
    <div id="mtab-register" class="hidden">
      <form class="grid-form" id="registerForm">
        <div class="field"><label class="label">Nome</label><input class="input" name="name" placeholder="Il tuo nome" required></div>
        <div class="field"><label class="label">Email</label><input class="input" name="email" type="email" placeholder="email@scuola.it" required></div>
        <div class="field"><label class="label">Password</label><input class="input" name="password" type="password" placeholder="Almeno 8 caratteri" required></div>
        <button class="btn btn-primary" type="submit">Crea account</button>
      </form>
    </div>
    <div id="authMsg"></div>
  </div>
</div>

<script>
/* ========== DATI STATICI ========== */
var IC_LOCK = '<svg style="display:inline-flex;flex-shrink:0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
var IC_PHONE = '<svg style="display:inline-flex;flex-shrink:0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>';
var IC_EYE_OFF = '<svg style="display:inline-flex;flex-shrink:0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
var IC_LOG_OUT = '<svg style="display:inline-flex;flex-shrink:0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>';
var IC_CHECK = '<svg style="display:inline-flex;flex-shrink:0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
var IC_DOT = '<svg style="display:inline-flex;flex-shrink:0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2" fill="currentColor" stroke="none"/></svg>';

var QUICK = [
  [IC_LOCK, 'Usa almeno 12 caratteri nelle password'],
  [IC_PHONE, 'Attiva la verifica in 2 passaggi sulla mail'],
  [IC_EYE_OFF, 'Non condividere password in chat di classe'],
  [IC_LOG_OUT, 'Fai logout dai PC dei laboratori']
];
var ADV = [
  'Non riutilizzare la stessa password su più servizi',
  'Attiva la verifica in due passaggi sulla mail scolastica',
  'Non condividere password in chat di classe o WhatsApp',
  'Blocca lo schermo nei laboratori informatici',
  'Fai logout dai PC condivisi dopo ogni sessione',
  'Non cliccare link sospetti nelle email scolastiche',
  'Usa passphrase lunghe per account importanti',
  'Conserva backup di codici di recupero',
  'Controlla i permessi di app e estensioni browser',
  'Non salvare password in file .txt non cifrati',
  'Usa password diverse per scuola, social e giochi',
  'Cambia subito la password dopo un accesso sospetto'
];
var CRITERIA = [
  ['Almeno 8 caratteri',      function(v){return v.length>=8}],
  ['Almeno 12 caratteri',     function(v){return v.length>=12}],
  ['Almeno 16 caratteri',     function(v){return v.length>=16}],
  ['Almeno 20 caratteri',     function(v){return v.length>=20}],
  ['Lettere maiuscole (A-Z)', function(v){return /[A-Z]/.test(v)}],
  ['Lettere minuscole (a-z)', function(v){return /[a-z]/.test(v)}],
  ['Numeri (0-9)',            function(v){return /[0-9]/.test(v)}],
  ['Simboli speciali (!@#…)', function(v){return /[^\w\s]/.test(v)}],
  ['Spazi o passphrase',      function(v){return /\s/.test(v)}],
  ['Nessuna tripletta (aaa)', function(v){return !/(.)\\1{2,}/.test(v)}],
  ['No parole deboli',        function(v){return !/password|1234|qwerty|admin|scuola/i.test(v)}],
  ['Lunghezza ottimale 20+',  function(v){return v.length>=20}]
];

/* ========== HELPERS ========== */
function api(action, method, data) {
  return fetch('api.php?action='+action, {
    method: method||'GET',
    headers: {'Content-Type':'application/json'},
    body: data ? JSON.stringify(data) : undefined
  }).then(function(r){return r.json();});
}
function showMsg(el, text, type) {
  el.innerHTML = '<div class="msg '+type+'">'+text+'</div>';
}
function badgeP(p) { return p==='Alta'?'b-alta':p==='Bassa'?'b-bassa':'b-media'; }
function badgeSt(s) {
  var m={'Aperta':'b-aperta','In lavorazione':'b-media','Risolta':'b-risolta','Chiusa':'b-chiusa'};
  return m[s]||'b-bassa';
}
function catBadge(c) {
  return '<span class="badge '+(c==='Bullismo'?'b-bullismo':'b-bassa')+'">'+c+'</span>';
}

/* ========== RENDER STATICI ========== */
function renderQuick(){
  document.getElementById('quickList').innerHTML = QUICK.map(function(t){
    return '<div class="check-row pass tiny"><span class="check-icon">'+t[0]+'</span>'+t[1]+'</div>';
  }).join('');
}
function renderAdv(){
  document.getElementById('advChecklist').innerHTML = ADV.map(function(t,i){
    return '<div class="check-row tiny"><span class="check-icon" style="color:var(--muted);font-size:.78rem;font-weight:700;min-width:20px">'+(i+1)+'</span>'+t+'</div>';
  }).join('');
}

/* ========== SCORE PASSWORD ========== */
function evalPassword(val){
  var s=0;
  var l=val.length;
  if(l>=8) s+=10; if(l>=12) s+=15; if(l>=16) s+=10; if(l>=20) s+=5;
  if(/[A-Z]/.test(val)) s+=10; if(/[a-z]/.test(val)) s+=10;
  if(/[0-9]/.test(val)) s+=10; if(/[^\w\s]/.test(val)) s+=15;
  if(/\s/.test(val)) s+=5; if(!/(.)\\1{2,}/.test(val)) s+=5;
  if(!/password|1234|qwerty|admin|scuola|letmein|abc123/i.test(val)) s+=5;
  s=Math.min(s,100);
  document.getElementById('scoreBar').style.width=s+'%';
  var col=s<40?'var(--error)':s<70?'var(--warning)':'var(--success)';
  document.getElementById('scoreBar').style.background=col;
  var lbl=s<40?'Debole':s<60?'Sufficiente':s<80?'Buona':'Ottima';
  document.getElementById('scoreText').textContent='Punteggio: '+s+' / 100';
  document.getElementById('scoreLbl').textContent=lbl;
  document.getElementById('scoreChecks').innerHTML = CRITERIA.map(function(c){
    var ok=c[1](val);
    return '<div class="check-row '+(ok?'pass':'')+'">'
      +'<span class="check-icon">'+(ok?IC_CHECK:IC_DOT)+'</span>'+c[0]+'</div>';
  }).join('');
}

/* ========== SEGNALAZIONI ========== */
async function loadReports(){
  var r=await api('reports');
  var list=r.reports||[];
  document.getElementById('kpiReports').textContent=list.length;
  if(!list.length){
    document.getElementById('reportsList').innerHTML='<div class="item tiny muted">Nessuna segnalazione ancora.</div>';
    return;
  }
  document.getElementById('reportsList').innerHTML=list.slice(0,8).map(function(x){
    return '<div class="item">'
      +'<div class="item-head"><b>'+x.title+'</b><span class="badge '+badgeP(x.priority)+'">'+x.priority+'</span></div>'
      +'<div class="item-meta">'+catBadge(x.category)
      +'<span>'+x.name+'</span>'
      +'<code style="font-size:.75rem">'+x.tracking_code+'</code></div>'
      +'<div class="item-body">'+x.description.substring(0,120)+(x.description.length>120?'…':'')+'</div>'
      +'</div>';
  }).join('');
}

/* ========== SESSION ========== */
async function loadSession(){
  var s=await api('session');
  var user=s.user;
  var isAdmin=user&&user.role==='admin';
  document.getElementById('authBtn').classList.toggle('hidden',!!user);
  document.getElementById('logoutBtn').classList.toggle('hidden',!user);
  var dot=document.getElementById('sessionDot');
  var lbl=document.getElementById('sessionLabel');
  dot.className='session-dot'+(isAdmin?' admin':user?' on':'');
  lbl.textContent=isAdmin?'Admin: '+user.name:user?user.name:'Ospite';
  document.getElementById('homeSessionInfo').textContent=
    isAdmin?'Admin: '+user.name:user?user.name+' — account attivo':'modalità ospite';
  document.getElementById('homeLoginBtn').classList.toggle('hidden',!!user);
  document.getElementById('homeGoVault').classList.toggle('hidden',!user);
  document.getElementById('adminTab').classList.toggle('hidden',!isAdmin);
  if(isAdmin) loadAdminReports();
  var vf=document.getElementById('vaultForm');
  var vg=document.getElementById('vaultGuestMsg');
  vf.classList.toggle('hidden',!user);
  vg.classList.toggle('hidden',!!user);
  loadVault();
}

/* ========== VAULT ========== */
async function loadVault(){
  var v=await api('vault');
  var items=v.items||[];
  var n=items.length;
  document.getElementById('kpiVault').textContent=n||'—';
  document.getElementById('kpiVault2').textContent=n;
  if(!n){
    document.getElementById('vaultList').innerHTML='<div class="item tiny muted">Nessuna credenziale salvata.</div>';
    return;
  }
  document.getElementById('vaultList').innerHTML=items.map(function(i){
    return '<div class="vault-card">'
      +'<div class="vault-row"><b>'+i.site_name+'</b>'
      +'<button class="btn btn-danger btn-sm" data-vid="'+i.id+'">Elimina</button></div>'
      +'<div class="muted tiny">'+i.username+'</div>'
      +'<div class="vault-row"><span class="pw-mask" title="Clicca per mostrare">'+i.password_plain+'</span></div>'
      +(i.notes?'<div class="tiny muted">'+i.notes+'</div>':'')
      +'</div>';
  }).join('');
  document.querySelectorAll('[data-vid]').forEach(function(btn){
    btn.addEventListener('click',async function(){
      await api('delete_vault','POST',{id:btn.dataset.vid});
      loadVault();
    });
  });
  document.querySelectorAll('.pw-mask').forEach(function(el){
    el.addEventListener('click',function(){el.classList.toggle('shown');});
  });
}

/* ========== ADMIN ========== */
async function loadAdminReports(){
  var r=await api('reports');
  var list=r.reports||[];
  var tot=list.length;
  var ap=list.filter(function(x){return x.status==='Aperta';}).length;
  var rs=list.filter(function(x){return x.status==='Risolta';}).length;
  document.getElementById('adminTotal').textContent=tot;
  document.getElementById('adminAperte').textContent=ap;
  document.getElementById('adminRisolte').textContent=rs;
  if(!tot){
    document.getElementById('adminTbody').innerHTML='<tr><td colspan="8" class="muted tiny" style="padding:20px">Nessuna segnalazione.</td></tr>';
    return;
  }
  document.getElementById('adminTbody').innerHTML=list.map(function(x){
    var opts=['Aperta','In lavorazione','Risolta','Chiusa'].map(function(s){
      return '<option'+(s===x.status?' selected':'')+'>'+s+'</option>';
    }).join('');
    return '<tr>'
      +'<td>'+x.id+'</td>'
      +'<td>'+x.name+'</td>'
      +'<td>'+catBadge(x.category)+'</td>'
      +'<td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="'+x.title+'">'+x.title+'</td>'
      +'<td><span class="badge '+badgeP(x.priority)+'">'+x.priority+'</span></td>'
      +'<td><span class="badge '+badgeSt(x.status)+'">'+x.status+'</span></td>'
      +'<td><code style="font-size:.78rem">'+x.tracking_code+'</code></td>'
      +'<td style="display:flex;gap:4px">'
        +'<select class="status-sel" data-rid="'+x.id+'">'+opts+'</select>'
        +'<button class="btn btn-danger btn-sm" data-del="'+x.id+'">Del</button>'
      +'</td>'
      +'</tr>';
  }).join('');
  document.querySelectorAll('.status-sel').forEach(function(sel){
    sel.addEventListener('change',async function(){
      await api('report_status','POST',{id:sel.dataset.rid,status:sel.value});
      loadAdminReports();
    });
  });
  document.querySelectorAll('[data-del]').forEach(function(btn){
    btn.addEventListener('click',async function(){
      if(!confirm('Eliminare questa segnalazione?')) return;
      await api('delete_report','POST',{id:btn.dataset.del});
      loadAdminReports();
    });
  });
}

/* ========== TAB NAVIGATION ========== */
function switchTab(name){
  document.querySelectorAll('.nav-tab').forEach(function(t){
    t.classList.toggle('active',t.dataset.tab===name);
  });
  document.querySelectorAll('.section').forEach(function(s){
    s.classList.toggle('active',s.id==='sec-'+name);
    if(s.id==='sec-'+name) s.classList.remove('hidden');
  });
  if(name==='admin') loadAdminReports();
  if(name==='segnalazioni') loadReports();
  if(name==='strumenti') { loadVault(); }
}
document.querySelectorAll('.nav-tab').forEach(function(t){
  t.addEventListener('click',function(){switchTab(t.dataset.tab);});
});

/* ========== EVENTI FORM ========== */
var anonCheck=document.getElementById('anonCheck');
var nameInput=document.getElementById('nameInput');
anonCheck.addEventListener('change',function(){
  nameInput.disabled=anonCheck.checked;
  nameInput.placeholder=anonCheck.checked?'Non visibile (anonimo)':'Opzionale';
  if(anonCheck.checked) nameInput.value='';
});

document.getElementById('reportForm').addEventListener('submit',async function(e){
  e.preventDefault();
  var btn=e.target.querySelector('[type="submit"]');
  btn.disabled=true;
  var fd=Object.fromEntries(new FormData(e.target));
  if(anonCheck.checked) fd.anonymous='1';
  try {
    var r=await api('report_create','POST',fd);
    showMsg(document.getElementById('reportMsg'),
      r.ok?'Segnalazione inviata — codice: <code>'+r.tracking_code+'</code>':(r.message||'Errore'),
      r.ok?'ok':'err');
    if(r.ok){e.target.reset();anonCheck.checked=false;nameInput.disabled=false;loadReports();}
  } catch(err){
    showMsg(document.getElementById('reportMsg'),'Errore di rete. Riprova.','err');
  }
  btn.disabled=false;
});

document.getElementById('scoreInput').addEventListener('input',function(e){
  evalPassword(e.target.value);
});

document.getElementById('genRandom').addEventListener('click',async function(){
  var r=await api('generate_password');
  if(r.ok){
    document.getElementById('generatedPw').value=r.password;
    document.getElementById('genMsg').textContent='Password casuale pronta — clicca Copia';
    evalPassword(r.password);
  }
});
document.getElementById('genPhrase').addEventListener('click',async function(){
  var r=await fetch('api.php?action=generate_password&mode=phrase').then(function(x){return x.json();});
  if(r.ok){
    document.getElementById('generatedPw').value=r.password;
    document.getElementById('genMsg').textContent='Passphrase generata — facile da ricordare';
    evalPassword(r.password);
  }
});
document.getElementById('copyPw').addEventListener('click',function(){
  var v=document.getElementById('generatedPw').value;
  if(v) navigator.clipboard.writeText(v).then(function(){document.getElementById('genMsg').textContent='Copiata negli appunti!';});
});

function openAuth(){document.getElementById('authModal').classList.add('open');}
function closeAuth(){document.getElementById('authModal').classList.remove('open');}
document.getElementById('authBtn').addEventListener('click',openAuth);
document.getElementById('homeLoginBtn').addEventListener('click',openAuth);
document.getElementById('vaultLoginBtn').addEventListener('click',openAuth);
document.getElementById('closeAuth').addEventListener('click',closeAuth);
document.getElementById('authModal').addEventListener('click',function(e){
  if(e.target===this) closeAuth();
});

/* Modal tabs */
document.querySelectorAll('.modal-tab').forEach(function(t){
  t.addEventListener('click',function(){
    document.querySelectorAll('.modal-tab').forEach(function(x){x.classList.remove('active');});
    t.classList.add('active');
    document.getElementById('mtab-login').classList.toggle('hidden',t.dataset.mtab!=='login');
    document.getElementById('mtab-register').classList.toggle('hidden',t.dataset.mtab!=='register');
  });
});

document.getElementById('loginForm').addEventListener('submit',async function(e){
  e.preventDefault();
  var r=await api('login','POST',Object.fromEntries(new FormData(e.target)));
  showMsg(document.getElementById('authMsg'),r.ok?'Login effettuato!':(r.message||'Errore'),r.ok?'ok':'err');
  if(r.ok){closeAuth();loadSession();}
});
document.getElementById('registerForm').addEventListener('submit',async function(e){
  e.preventDefault();
  var r=await api('register','POST',Object.fromEntries(new FormData(e.target)));
  showMsg(document.getElementById('authMsg'),r.ok?'Registrazione completata!':(r.message||'Errore'),r.ok?'ok':'err');
  if(r.ok){closeAuth();loadSession();}
});
document.getElementById('logoutBtn').addEventListener('click',async function(){
  await api('logout');loadSession();
});

document.getElementById('vaultForm').addEventListener('submit',async function(e){
  e.preventDefault();
  var r=await api('save_password','POST',Object.fromEntries(new FormData(e.target)));
  if(r.ok){e.target.reset();loadVault();}
});

/* Tema */
document.getElementById('themeBtn').addEventListener('click',function(){
  var t=document.documentElement.getAttribute('data-theme');
  var next=t==='dark'?'light':'dark';
  document.documentElement.setAttribute('data-theme',next);
  var icon=document.getElementById('themeIcon');
  if(next==='dark'){
    icon.innerHTML='<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
  } else {
    icon.innerHTML='<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
  }
});

/* ========== INIT ========== */
renderQuick();
renderAdv();
evalPassword('');
loadReports();
loadSession();
</script>
</body>
</html>
