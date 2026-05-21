<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SafeSchool Hub &mdash; ITT Fermi</title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&f[]=cabinet-grotesk@700,800&display=swap" rel="stylesheet">
<style>
/* === TOKENS === */
:root,[data-theme="light"]{
  --bg:#faf9f4; --surface:#ffffff; --surface2:#f3f1e8; --border:#e0dcd0;
  --text:#1e1b14; --muted:#7a7263; --faint:#c0bab0;
  --primary:#c49a00; --primary-hover:#a07e00; --primary-fg:#1e1b14;
  --ok:#437a22; --warn:#c97a00; --danger:#a12c7b;
  --admin:#006494;
  --shadow:0 2px 12px rgba(30,27,20,.07),0 1px 3px rgba(30,27,20,.05);
  --shadow-lg:0 8px 32px rgba(30,27,20,.10);
  --radius:16px; --radius-sm:10px;
  --nav-h:64px;
}
[data-theme="dark"]{
  --bg:#0f0e0a; --surface:#181610; --surface2:#201e17; --border:#2e2b22;
  --text:#f0ead8; --muted:#9c9480; --faint:#4a4638;
  --primary:#f5c800; --primary-hover:#e0b400; --primary-fg:#0f0e0a;
  --ok:#7fd05b; --warn:#ffb14a; --danger:#ff77b7;
  --admin:#5591c7;
  --shadow:0 2px 12px rgba(0,0,0,.25),0 1px 3px rgba(0,0,0,.2);
  --shadow-lg:0 12px 40px rgba(0,0,0,.4);
}
/* === RESET BASE === */
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;-webkit-font-smoothing:antialiased}
body{font-family:Satoshi,system-ui,sans-serif;background:var(--bg);color:var(--text);
  min-height:100vh;transition:background .25s,color .25s;font-size:1rem;line-height:1.6}
h1,h2,h3,h4{font-family:'Cabinet Grotesk',sans-serif;line-height:1.15}
button,input,textarea,select{font:inherit;color:inherit}
a{color:var(--primary);text-decoration:none}
img{max-width:100%;height:auto}

/* === TOPBAR === */
.topbar{
  position:sticky;top:0;z-index:50;
  background:var(--surface);border-bottom:1px solid var(--border);
  height:var(--nav-h);display:flex;align-items:center;
  padding:0 24px;gap:16px;justify-content:space-between;
  box-shadow:var(--shadow);
}
.brand{display:flex;align-items:center;gap:12px;flex-shrink:0}
.logo-wrap{width:44px;height:44px;display:grid;place-items:center}
.brand-text h2{font-size:1.1rem;letter-spacing:-.01em}
.brand-text span{font-size:.78rem;color:var(--muted);display:block;margin-top:-2px}
.topbar-right{display:flex;align-items:center;gap:8px}

/* === MAIN NAV TABS === */
.main-nav{
  background:var(--surface);border-bottom:1px solid var(--border);
  display:flex;gap:0;padding:0 24px;overflow-x:auto;scrollbar-width:none;
}
.main-nav::-webkit-scrollbar{display:none}
.nav-tab{
  padding:0 20px;height:48px;display:flex;align-items:center;gap:8px;
  font-size:.92rem;font-weight:600;color:var(--muted);white-space:nowrap;
  border-bottom:2px solid transparent;cursor:pointer;
  transition:color .18s,border-color .18s;background:none;border-top:0;border-left:0;border-right:0;
}
.nav-tab:hover{color:var(--text)}
.nav-tab.active{color:var(--primary);border-bottom-color:var(--primary)}
.nav-tab .tab-icon{display:flex;align-items:center;opacity:.7}
.nav-tab .admin-dot{width:7px;height:7px;border-radius:50%;background:var(--admin);display:inline-block}

/* === LAYOUT === */
.wrap{max-width:1200px;margin:0 auto;padding:32px 24px}
.section{display:none}
.section.active{display:block}

/* === CARDS === */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow)}
.card-body{padding:24px}
.card-header{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px}
.card-header h3{font-size:1.05rem;display:flex;align-items:center;gap:8px}
.card-hint{font-size:.8rem;color:var(--muted);line-height:1.5;margin-top:4px}

/* === TWO COL === */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
@media(max-width:860px){.two-col,.three-col{grid-template-columns:1fr}}

/* === KPI STRIP === */
.kpi-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
.kpi{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px 18px;box-shadow:var(--shadow)}
.kpi-label{font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em}
.kpi-val{font-size:2rem;font-weight:800;font-family:'Cabinet Grotesk',sans-serif;color:var(--primary);margin-top:4px}
@media(max-width:700px){.kpi-strip{grid-template-columns:1fr 1fr}}

/* === BUTTONS === */
.btn{border:0;border-radius:var(--radius-sm);padding:10px 18px;font-weight:700;cursor:pointer;
  transition:background .18s,transform .12s,box-shadow .15s;font-size:.92rem;display:inline-flex;align-items:center;gap:6px}
.btn:active{transform:scale(.97)}
.btn-primary{background:var(--primary);color:var(--primary-fg)}
.btn-primary:hover{background:var(--primary-hover)}
.btn-ghost{background:transparent;border:1px solid var(--border);color:var(--text)}
.btn-ghost:hover{background:var(--surface2)}
.btn-danger{background:rgba(161,44,123,.1);color:var(--danger);border:1px solid rgba(161,44,123,.2)}
.btn-admin{background:rgba(0,100,148,.12);color:var(--admin);border:1px solid rgba(0,100,148,.25);font-weight:700}
.btn-admin:hover{background:rgba(0,100,148,.2)}
.btn-sm{padding:6px 12px;font-size:.82rem;border-radius:8px}

/* === INPUTS === */
.field{display:grid;gap:5px}
.label{font-size:.78rem;font-weight:700;color:var(--muted);letter-spacing:.05em;text-transform:uppercase}
.input{width:100%;padding:11px 14px;border-radius:var(--radius-sm);border:1px solid var(--border);
  background:var(--surface2);color:var(--text);transition:border-color .18s;font-size:.95rem}
.input:focus{outline:none;border-color:var(--primary);background:var(--surface)}
form.grid-form{display:grid;gap:14px}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:600px){.row2{grid-template-columns:1fr}}

/* === BADGES === */
.badge{display:inline-flex;padding:3px 9px;border-radius:999px;font-size:.76rem;font-weight:700}
.b-alta{background:rgba(161,44,123,.12);color:var(--danger)}
.b-media{background:rgba(201,122,0,.12);color:var(--warn)}
.b-bassa{background:rgba(67,122,34,.1);color:var(--ok)}
.b-bullismo{background:rgba(161,44,123,.15);color:var(--danger);border:1px solid rgba(161,44,123,.3)}
.b-admin{background:rgba(0,100,148,.12);color:var(--admin);border:1px solid rgba(0,100,148,.25)}
.b-aperta{background:rgba(201,122,0,.1);color:var(--warn)}
.b-risolta{background:rgba(67,122,34,.1);color:var(--ok)}
.b-chiusa{background:rgba(122,114,99,.12);color:var(--muted)}

/* === LIST ITEMS === */
.list{display:grid;gap:10px}
.item{padding:16px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface)}
.item-head{display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap}
.item-meta{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:6px;font-size:.8rem;color:var(--muted)}
.item-body{font-size:.88rem;color:var(--muted);margin-top:8px;line-height:1.5}

/* === SCORE BAR === */
.score-wrap{background:var(--surface2);border-radius:999px;height:8px;overflow:hidden;margin:12px 0 4px}
.score-bar{height:100%;width:0%;background:linear-gradient(90deg,var(--danger),var(--warn) 50%,var(--ok));border-radius:999px;transition:width .4s}
.score-info{display:flex;justify-content:space-between;font-size:.8rem;color:var(--muted)}

/* === CHECKLIST === */
.check-row{display:flex;align-items:center;gap:10px;padding:9px 13px;border-radius:var(--radius-sm);
  border:1px solid var(--border);font-size:.88rem}
.check-row.pass{border-color:rgba(67,122,34,.3);background:rgba(67,122,34,.04);color:var(--ok)}
.check-icon{display:flex;align-items:center;flex-shrink:0}

/* === VAULT === */
.vault-card{padding:14px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);
  background:var(--surface);display:grid;gap:6px}
.vault-row{display:flex;justify-content:space-between;align-items:center;gap:8px}
.pw-mask{font-family:monospace;font-size:.9rem;background:var(--surface2);padding:5px 10px;
  border-radius:8px;flex:1;filter:blur(3.5px);transition:filter .2s;cursor:pointer;border:1px solid var(--border)}
.pw-mask:hover,.pw-mask.shown{filter:none}

/* === ANON TOGGLE === */
.anon-row{display:flex;align-items:center;gap:10px;padding:10px 14px;
  border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--surface2);
  cursor:pointer;user-select:none;font-size:.9rem}
.anon-row input{width:16px;height:16px;accent-color:var(--primary);cursor:pointer}

/* === INTRO BOX === */
.intro-box{background:linear-gradient(135deg,var(--surface) 0%,var(--surface2) 100%);
  border:1px solid var(--border);border-radius:var(--radius);padding:28px 32px;margin-bottom:28px}
.intro-box h1{font-size:clamp(1.6rem,2.8vw,2.6rem);margin-bottom:10px}
.intro-box p{color:var(--muted);max-width:68ch;line-height:1.7}
.feature-chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:16px}
.chip{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;
  border-radius:999px;background:var(--surface);border:1px solid var(--border);
  font-size:.82rem;font-weight:600;color:var(--text)}
.chip-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}

/* === ADMIN TABLE === */
.admin-table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.85rem}
thead th{padding:10px 14px;text-align:left;font-size:.75rem;font-weight:700;
  color:var(--muted);text-transform:uppercase;letter-spacing:.05em;
  background:var(--surface2);border-bottom:1px solid var(--border)}
tbody tr{border-bottom:1px solid var(--border);transition:background .12s}
tbody tr:hover{background:var(--surface2)}
tbody td{padding:11px 14px;vertical-align:top}
.status-select{padding:5px 8px;border-radius:8px;border:1px solid var(--border);
  background:var(--surface2);font-size:.82rem;color:var(--text);cursor:pointer}

/* === MODAL === */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(8px);
  display:none;align-items:center;justify-content:center;padding:20px;z-index:200}
.modal-overlay.open{display:flex}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
  max-width:600px;width:100%;padding:28px;max-height:90vh;overflow-y:auto}
.modal-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}

/* === MSG === */
.msg{padding:10px 14px;border-radius:var(--radius-sm);font-size:.9rem;margin-top:10px}
.msg.ok{background:rgba(67,122,34,.1);color:var(--ok)}
.msg.err{background:rgba(161,44,123,.1);color:var(--danger)}

/* === DIVIDER === */
.divider{height:1px;background:var(--border);margin:20px 0}

/* === MISC === */
.muted{color:var(--muted)}
.tiny{font-size:.82rem}
.hidden{display:none!important}
.mt8{margin-top:8px} .mt16{margin-top:16px} .mt24{margin-top:24px}
.gap8{gap:8px} .flex{display:flex} .flex-between{display:flex;justify-content:space-between;align-items:center}

/* === SESSION PILL === */
.session-pill{
  display:flex;align-items:center;gap:8px;
  padding:6px 14px;border-radius:999px;border:1px solid var(--border);
  background:var(--surface2);font-size:.82rem;font-weight:600
}
.session-dot{width:8px;height:8px;border-radius:50%;background:var(--faint)}
.session-dot.on{background:var(--ok)}
.session-dot.admin{background:var(--admin)}

/* === ICON HELPERS === */
.icon{display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
svg.ic{stroke:currentColor;fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round}
</style>
</head>
<body>

<!-- ===== TOPBAR ===== -->
<header class="topbar">
  <div class="brand">
    <div class="logo-wrap">
      <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" width="44" height="44" aria-label="ITT Enrico Fermi">
        <path d="M22 3L5 10V22C5 31.5 12.5 39.5 22 42C31.5 39.5 39 31.5 39 22V10L22 3Z"
              fill="var(--primary)" opacity=".15" stroke="var(--primary)" stroke-width="1.5"/>
        <circle cx="22" cy="22" r="2.5" fill="var(--primary)"/>
        <ellipse cx="22" cy="22" rx="10" ry="4" stroke="var(--primary)" stroke-width="1.3"
                 fill="none" transform="rotate(0 22 22)"/>
        <ellipse cx="22" cy="22" rx="10" ry="4" stroke="var(--primary)" stroke-width="1.3"
                 fill="none" transform="rotate(60 22 22)"/>
        <ellipse cx="22" cy="22" rx="10" ry="4" stroke="var(--primary)" stroke-width="1.3"
                 fill="none" transform="rotate(120 22 22)"/>
        <text x="22" y="12" text-anchor="middle" font-family="Cabinet Grotesk,sans-serif"
              font-weight="800" font-size="7" fill="var(--primary)">F</text>
      </svg>
    </div>
    <div class="brand-text">
      <h2>SafeSchool Hub</h2>
      <span>ITT E. Fermi &mdash; Francavilla Fontana</span>
    </div>
  </div>
  <div class="topbar-right">
    <div class="session-pill" id="sessionPill">
      <span class="session-dot" id="sessionDot"></span>
      <span id="sessionLabel">Ospite</span>
    </div>
    <!-- Sun/Moon icon — toggled by JS -->
    <button class="btn btn-ghost btn-sm" id="themeBtn" aria-label="Cambia tema">
      <svg class="ic" width="18" height="18" viewBox="0 0 24 24" id="themeIcon">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
      </svg>
    </button>
    <button class="btn btn-ghost btn-sm" id="authBtn">
      <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
      Accedi
    </button>
    <button class="btn btn-primary btn-sm hidden" id="logoutBtn">
      <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Logout
    </button>
  </div>
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
  <button class="nav-tab" data-tab="password-check">
    <span class="tab-icon"><svg class="ic" width="15" height="15" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
    Analisi password
  </button>
  <button class="nav-tab" data-tab="strumenti">
    <span class="tab-icon"><svg class="ic" width="15" height="15" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
    Strumenti
  </button>
  <button class="nav-tab" data-tab="vault">
    <span class="tab-icon"><svg class="ic" width="15" height="15" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
    Il mio vault
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
      <span class="chip"><span class="chip-dot" style="background:var(--danger)"></span>Segnalazioni anonime</span>
      <span class="chip"><span class="chip-dot" style="background:var(--warn)"></span>Analisi password</span>
      <span class="chip"><span class="chip-dot" style="background:var(--ok)"></span>Generatore sicuro</span>
      <span class="chip"><span class="chip-dot" style="background:var(--primary)"></span>Vault personale</span>
      <span class="chip"><span class="chip-dot" style="background:var(--admin)"></span>Pannello admin</span>
    </div>
  </div>

  <div class="kpi-strip">
    <div class="kpi"><div class="kpi-label">Segnalazioni totali</div><div class="kpi-val" id="kpiReports">&mdash;</div></div>
    <div class="kpi"><div class="kpi-label">Criteri valutati</div><div class="kpi-val">12</div></div>
    <div class="kpi"><div class="kpi-label">Parole deboli bloccate</div><div class="kpi-val">16</div></div>
    <div class="kpi"><div class="kpi-label">Elementi vault</div><div class="kpi-val" id="kpiVault">&mdash;</div></div>
  </div>

  <div class="two-col">
    <!-- Come funziona -->
    <div class="card">
      <div class="card-header">
        <h3>
          <svg class="ic" width="17" height="17" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          Come funziona
        </h3>
      </div>
      <div class="card-body">
        <div class="list">
          <div class="item"><div class="item-head"><b>1. Segnalazioni</b></div>
            <div class="item-body">Chiunque, anche senza account, può inviare una segnalazione: bullismo, problemi tecnici, privacy o sicurezza di rete. Puoi farlo in modo completamente <strong>anonimo</strong>. Ricevi un codice di tracciamento univoco.</div>
          </div>
          <div class="item"><div class="item-head"><b>2. Analisi password</b></div>
            <div class="item-body">Testa la tua password su 12 criteri: lunghezza, varietà di caratteri, assenza di parole deboli, ripetizioni. La password non viene mai inviata al server.</div>
          </div>
          <div class="item"><div class="item-head"><b>3. Generatore sicuro</b></div>
            <div class="item-body">Genera password casuali da 18 caratteri o passphrase facili da ricordare. Puoi copiarle subito negli appunti.</div>
          </div>
          <div class="item"><div class="item-head"><b>4. Vault personale</b></div>
            <div class="item-body">Con un account puoi salvare le credenziali di Moodle, Registro, GitHub, Drive nel tuo vault privato su database MySQL.</div>
          </div>
        </div>
      </div>
    </div>
    <!-- Consigli rapidi -->
    <div class="card">
      <div class="card-header">
        <h3>
          <svg class="ic" width="17" height="17" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          Consigli rapidi
        </h3>
      </div>
      <div class="card-body">
        <div class="list" id="quickList"></div>
        <div class="divider"></div>
        <div class="flex-between">
          <span class="tiny muted">Stai usando</span>
          <span class="tiny" id="homeSessionInfo">modalità ospite</span>
        </div>
        <div class="mt16">
          <button class="btn btn-primary" id="homeLoginBtn" style="width:100%">
            <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Accedi o crea account
          </button>
          <button class="btn btn-ghost hidden" id="homeGoVault" style="width:100%;margin-top:8px" onclick="switchTab('vault')">
            Apri il mio vault
            <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>
        </div>
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

<!-- ===== ANALISI PASSWORD ===== -->
<div class="section" id="sec-password-check">
<div class="wrap">
  <div style="margin-bottom:20px">
    <h2 style="font-size:1.5rem;display:flex;align-items:center;gap:10px">
      <svg class="ic" width="20" height="20" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Analisi password
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
          <p class="tiny muted">Prova anche il <strong>generatore</strong> nella sezione Strumenti per ottenere una password già sicura al 100%.</p>
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
</div>

<!-- ===== STRUMENTI ===== -->
<div class="section" id="sec-strumenti">
<div class="wrap">
  <div style="margin-bottom:20px">
    <h2 style="font-size:1.5rem;display:flex;align-items:center;gap:10px">
      <svg class="ic" width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Strumenti
    </h2>
    <p class="tiny muted mt8">Generatore di password sicure e checklist buone pratiche. Pubblici, nessun login richiesto.</p>
  </div>
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
        <p class="tiny muted">Dopo aver generato una password, puoi testarla subito nella sezione <strong>Analisi password</strong> oppure salvarla nel tuo <strong>Vault</strong>.</p>
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
</div>
</div>

<!-- ===== VAULT ===== -->
<div class="section" id="sec-vault">
<div class="wrap">
  <div style="margin-bottom:20px">
    <h2 style="font-size:1.5rem;display:flex;align-items:center;gap:10px">
      <svg class="ic" width="20" height="20" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
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
          <div class="field"><label class="label">Servizio</label><input class="input" name="site_name" placeholder="es. Moodle, Registro, GitHub, Drive"></div>
          <div class="row2">
            <div class="field"><label class="label">Username / Email</label><input class="input" name="username" placeholder="username o email"></div>
            <div class="field"><label class="label">Password</label><input class="input" type="password" name="password_plain" placeholder="password del servizio"></div>
          </div>
          <div class="field"><label class="label">Note</label><textarea class="input" name="notes" rows="2" placeholder="Note opzionali"></textarea></div>
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

  <div class="three-col" style="margin-bottom:24px">
    <div class="kpi"><div class="kpi-label">Totale segnalazioni</div><div class="kpi-val" id="adminTotal">&mdash;</div></div>
    <div class="kpi"><div class="kpi-label">Aperte</div><div class="kpi-val" id="adminAperte" style="color:var(--warn)">&mdash;</div></div>
    <div class="kpi"><div class="kpi-label">Risolte</div><div class="kpi-val" id="adminRisolte" style="color:var(--ok)">&mdash;</div></div>
  </div>

  <div class="card">
    <div class="card-header"><h3>Tutte le segnalazioni</h3></div>
    <div class="card-body">
      <div class="admin-table-wrap">
        <table id="adminTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Categoria</th>
              <th>Titolo</th>
              <th>Da</th>
              <th>Priorità</th>
              <th>Stato</th>
              <th>Data</th>
              <th>Azioni</th>
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

</main>

<!-- ===== AUTH MODAL ===== -->
<div class="modal-overlay" id="authModal">
  <div class="modal-box">
    <div class="modal-head">
      <h2>Accesso</h2>
      <button class="btn btn-ghost btn-sm" id="closeAuth" aria-label="Chiudi">
        <svg class="ic" width="16" height="16" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="row2">
      <form id="loginForm" class="grid-form">
        <h3 style="margin-bottom:4px">Accedi</h3>
        <div class="field"><label class="label">Email</label><input class="input" name="email" type="email" placeholder="email@scuola.it"></div>
        <div class="field"><label class="label">Password</label><input class="input" name="password" type="password" placeholder="password"></div>
        <button class="btn btn-primary" type="submit">
          <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Accedi
        </button>
      </form>
      <form id="registerForm" class="grid-form">
        <h3 style="margin-bottom:4px">Crea account</h3>
        <div class="field"><label class="label">Nome</label><input class="input" name="name" placeholder="Nome completo"></div>
        <div class="field"><label class="label">Email</label><input class="input" name="email" type="email" placeholder="email@scuola.it"></div>
        <div class="field"><label class="label">Password</label><input class="input" name="password" type="password" placeholder="min. 50/100 sicurezza"></div>
        <button class="btn btn-primary" type="submit">
          <svg class="ic" width="15" height="15" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
          Registrati
        </button>
      </form>
    </div>
    <div id="authMsg"></div>
  </div>
</div>

<script>
/* ========== DATI STATICI ========== */
/* SVG check e dot per consigli rapidi */
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
      +'<td><code style="font-size:.78rem">'+x.tracking_code+'</code></td>'
      +'<td>'+catBadge(x.category)+'</td>'
      +'<td><b>'+x.title+'</b><div class="tiny muted">'+x.description.substring(0,60)+'…</div></td>'
      +'<td class="tiny">'+x.name+(x.user_email?'<br><span class="muted">'+x.user_email+'</span>':'')+'</td>'
      +'<td><span class="badge '+badgeP(x.priority)+'">'+x.priority+'</span></td>'
      +'<td><select class="status-select" data-rid="'+x.id+'">'+opts+'</select></td>'
      +'<td class="tiny muted">'+x.created_at.substring(0,10)+'</td>'
      +'<td><button class="btn btn-danger btn-sm" data-del="'+x.id+'">Elimina</button></td>'
      +'</tr>';
  }).join('');
  document.querySelectorAll('.status-select').forEach(function(sel){
    sel.addEventListener('change',async function(){
      await api('update_report_status','POST',{id:sel.dataset.rid,status:sel.value});
      loadAdminReports();
    });
  });
  document.querySelectorAll('[data-del]').forEach(function(btn){
    btn.addEventListener('click',async function(){
      if(!confirm('Eliminare questa segnalazione?')) return;
      await api('delete_report','POST',{id:btn.dataset.del});
      loadAdminReports(); loadReports();
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
  var fd=Object.fromEntries(new FormData(e.target));
  if(anonCheck.checked) fd.anonymous='1';
  var r=await api('add_report','POST',fd);
  showMsg(document.getElementById('reportMsg'),
    r.ok?'Segnalazione inviata — codice: <code>'+r.tracking+'</code>':(r.message||'Errore'),
    r.ok?'ok':'err');
  if(r.ok){e.target.reset();anonCheck.checked=false;nameInput.disabled=false;loadReports();}
});

document.getElementById('scoreInput').addEventListener('input',function(e){
  evalPassword(e.target.value);
});

document.getElementById('genRandom').addEventListener('click',async function(){
  var r=await api('generate_password');
  if(r.ok){document.getElementById('generatedPw').value=r.password;
    document.getElementById('genMsg').textContent='Password casuale pronta — clicca Copia';
    evalPassword(r.password);}
});
document.getElementById('genPhrase').addEventListener('click',async function(){
  var r=await fetch('api.php?action=generate_password&mode=phrase').then(function(x){return x.json();});
  if(r.ok){document.getElementById('generatedPw').value=r.password;
    document.getElementById('genMsg').textContent='Passphrase generata — facile da ricordare';}
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

/* Tema: aggiorna anche l'icona */
document.getElementById('themeBtn').addEventListener('click',function(){
  var t=document.documentElement.getAttribute('data-theme');
  var next=t==='dark'?'light':'dark';
  document.documentElement.setAttribute('data-theme',next);
  var icon=document.getElementById('themeIcon');
  if(next==='dark'){
    /* Moon icon */
    icon.innerHTML='<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
  } else {
    /* Sun icon */
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
