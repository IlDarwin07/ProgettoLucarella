<?php
require_once __DIR__ . '/includes/auth.php';
// Consenti sia ospiti che utenti registrati; blocca solo chi non ha sessione
if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
$role = $_SESSION['user']['role'] ?? 'guest';
$isGuest = ($role === 'guest');
?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SafeSchool Hub <?= $isGuest ? '— Ospite' : '' ?></title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f7f6f2;--surface:#ffffff;--surface2:#f3f0ec;
  --border:#dcd9d5;--text:#28251d;--muted:#7a7974;--faint:#bab9b4;
  --primary:#01696f;--primary-h:#0c4e54;--primary-bg:color-mix(in oklab,#01696f 10%,transparent);
  --error:#a12c7b;--warn:#d19900;--warn-bg:color-mix(in oklab,#d19900 10%,transparent);
  --success:#437a22;
  --radius:0.5rem;--shadow:0 4px 24px rgba(0,0,0,.08);
  --font:'Satoshi',system-ui,sans-serif;
}
[data-theme="dark"]{
  --bg:#171614;--surface:#1c1b19;--surface2:#201f1d;
  --border:#393836;--text:#cdccca;--muted:#797876;--faint:#5a5957;
  --primary:#4f98a3;--primary-h:#227f8b;--primary-bg:color-mix(in oklab,#4f98a3 12%,transparent);
  --error:#d163a7;--warn:#e8af34;--warn-bg:color-mix(in oklab,#e8af34 12%,transparent);
  --success:#6daa45;
  --shadow:0 4px 24px rgba(0,0,0,.35);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased}
body{min-height:100dvh;background:var(--bg);color:var(--text);font-family:var(--font);font-size:1rem}

/* ---- NAV ---- */
.nav{display:flex;align-items:center;justify-content:space-between;
  padding:.9rem 1.5rem;background:var(--surface);border-bottom:1px solid var(--border);
  position:sticky;top:0;z-index:100;gap:1rem}
.nav-logo{display:flex;align-items:center;gap:9px;font-weight:700;font-size:1rem;color:var(--text);text-decoration:none}
.nav-logo svg{color:var(--primary)}
.nav-right{display:flex;align-items:center;gap:.6rem}
.badge-guest{font-size:.72rem;font-weight:600;padding:.2rem .6rem;border-radius:99px;
  background:var(--warn-bg);color:var(--warn);border:1px solid color-mix(in oklab,var(--warn) 25%,transparent);
  white-space:nowrap}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;
  padding:.5rem 1rem;border-radius:var(--radius);font-size:.85rem;font-weight:600;
  cursor:pointer;border:none;font-family:inherit;transition:background .18s,opacity .18s;text-decoration:none}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-h)}
.btn-ghost{background:transparent;color:var(--muted);border:1px solid var(--border)}
.btn-ghost:hover{color:var(--text);border-color:color-mix(in oklab,var(--text) 30%,transparent)}
.btn-sm{padding:.38rem .75rem;font-size:.8rem}

/* ---- BANNER ---- */
.banner{
  display:flex;align-items:center;gap:.9rem;flex-wrap:wrap;
  padding:.85rem 1.5rem;background:var(--warn-bg);
  border-bottom:1px solid color-mix(in oklab,var(--warn) 25%,transparent);
  font-size:.86rem;color:var(--text)
}
.banner svg{color:var(--warn);flex-shrink:0}
.banner strong{color:var(--warn)}
.banner-actions{display:flex;gap:.5rem;margin-left:auto}

/* ---- LAYOUT ---- */
.main{max-width:900px;margin:0 auto;padding:2rem 1.5rem;display:grid;gap:1.75rem}

/* ---- CARDS / SECTIONS ---- */
.section{background:var(--surface);border:1px solid var(--border);border-radius:calc(var(--radius)*1.5);overflow:hidden}
.section-header{padding:1.1rem 1.4rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:.7rem}
.section-header svg{color:var(--primary)}
.section-title{font-size:1rem;font-weight:700}
.section-body{padding:1.25rem 1.4rem}

/* ---- FORM SEGNALAZIONE ---- */
.field{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.9rem}
label{font-size:.79rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em}
input,select,textarea{padding:.55rem .75rem;border:1px solid var(--border);border-radius:var(--radius);
  background:var(--bg);color:var(--text);font-size:.93rem;font-family:inherit;
  transition:border .18s,box-shadow .18s;resize:vertical}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--primary);
  box-shadow:0 0 0 3px color-mix(in oklab,var(--primary) 18%,transparent)}
textarea{min-height:90px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:.9rem}
.submit-row{display:flex;align-items:center;gap:.8rem;flex-wrap:wrap;margin-top:.3rem}
.anon-check{display:flex;align-items:center;gap:.4rem;font-size:.83rem;color:var(--muted);cursor:pointer}
.anon-check input{width:auto;padding:0}
.msg{padding:.6rem .85rem;border-radius:var(--radius);font-size:.84rem;margin-top:.6rem}
.msg.err{background:color-mix(in oklab,var(--error) 12%,transparent);color:var(--error)}
.msg.ok{background:color-mix(in oklab,var(--primary) 12%,transparent);color:var(--primary)}
.tracking-box{display:inline-flex;align-items:center;gap:.6rem;background:var(--surface2);
  border:1px solid var(--border);border-radius:var(--radius);padding:.5rem .9rem;margin-top:.5rem}
.tracking-code{font-family:monospace;font-size:1.05rem;font-weight:700;letter-spacing:.08em;color:var(--primary)}

/* ---- REPORTS LIST ---- */
.reports-list{display:flex;flex-direction:column;gap:.6rem}
.report-item{display:flex;align-items:flex-start;gap:.9rem;padding:.8rem;border-radius:var(--radius);
  background:var(--surface2);border:1px solid var(--border)}
.report-item-body{flex:1;min-width:0}
.report-item-title{font-weight:600;font-size:.93rem;margin-bottom:.2rem;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.report-item-meta{font-size:.78rem;color:var(--muted);display:flex;gap:.7rem;flex-wrap:wrap}
.pill{display:inline-block;padding:.15rem .55rem;border-radius:99px;font-size:.72rem;font-weight:600}
.pill-alta{background:color-mix(in oklab,var(--error) 14%,transparent);color:var(--error)}
.pill-media{background:var(--warn-bg);color:var(--warn)}
.pill-bassa{background:color-mix(in oklab,var(--success) 12%,transparent);color:var(--success)}
.pill-status{background:var(--primary-bg);color:var(--primary)}
.empty-state{text-align:center;padding:2.5rem 1rem;color:var(--muted)}
.empty-state svg{margin:0 auto .75rem;color:var(--faint)}

/* ---- LOCKED SECTIONS ---- */
.locked-wrap{position:relative}
.locked-overlay{
  position:absolute;inset:0;z-index:10;
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.8rem;
  background:color-mix(in oklab,var(--bg) 75%,transparent);
  backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
  padding:2rem;text-align:center;
  border-radius:calc(var(--radius)*1.5)
}
.locked-overlay svg{color:var(--faint)}
.locked-overlay p{font-size:.9rem;color:var(--muted);max-width:26ch}
.locked-preview{opacity:.35;pointer-events:none;user-select:none;filter:blur(1px)}

/* ---- FAKE VAULT ROWS (preview) ---- */
.vault-row{display:flex;align-items:center;gap:.9rem;padding:.7rem .9rem;
  border-radius:var(--radius);background:var(--surface2);border:1px solid var(--border)}
.vault-icon{width:32px;height:32px;border-radius:var(--radius);background:var(--primary-bg);
  display:grid;place-items:center;flex-shrink:0}
.vault-info{flex:1;min-width:0}
.vault-site{font-weight:600;font-size:.9rem}
.vault-user{font-size:.78rem;color:var(--muted)}
.vault-pw{font-family:monospace;font-size:.85rem;letter-spacing:.12em;color:var(--faint)}

/* ---- THEME BTN ---- */
.theme-btn{background:var(--surface);border:1px solid var(--border);border-radius:50%;
  width:34px;height:34px;display:grid;place-items:center;cursor:pointer;color:var(--muted);
  transition:background .18s;flex-shrink:0}
.theme-btn:hover{background:var(--border)}

@media(max-width:600px){
  .form-row{grid-template-columns:1fr}
  .nav{padding:.75rem 1rem}
  .main{padding:1.25rem 1rem}
  .banner{padding:.75rem 1rem}
  .banner-actions{margin-left:0;width:100%}
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <a href="guest.php" class="nav-logo">
    <svg width="24" height="24" viewBox="0 0 32 32" fill="none">
      <rect x="2" y="2" width="28" height="28" rx="7" fill="var(--primary)" opacity=".12"/>
      <path d="M16 6 L26 10 L26 18 C26 23 21 27 16 29 C11 27 6 23 6 18 L6 10 Z" fill="var(--primary)" opacity=".25"/>
      <path d="M16 8 L24 11.5 L24 18 C24 22 20 25.5 16 27.5 C12 25.5 8 22 8 18 L8 11.5 Z" stroke="var(--primary)" stroke-width="1.5" fill="none"/>
      <path d="M12 16 L15 19 L20 13" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    SafeSchool Hub
  </a>
  <div class="nav-right">
    <?php if($isGuest): ?>
    <span class="badge-guest">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline-block;vertical-align:middle;margin-right:3px">
        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
      </svg>
      Ospite
    </span>
    <?php endif; ?>
    <button class="theme-btn" id="themeBtn" aria-label="Cambia tema">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="themeIcon">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
      </svg>
    </button>
    <a href="logout.php" class="btn btn-ghost btn-sm">Esci</a>
  </div>
</nav>

<?php if($isGuest): ?>
<!-- BANNER OSPITE -->
<div class="banner">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/>
  </svg>
  <span><strong>Modalità ospite</strong> — Puoi inviare segnalazioni anonime.
  Registrati per accedere a Vault password, storico segnalazioni e altro.</span>
  <div class="banner-actions">
    <a href="register.php" class="btn btn-primary btn-sm">Crea account</a>
    <a href="login.php" class="btn btn-ghost btn-sm">Accedi</a>
  </div>
</div>
<?php endif; ?>

<main class="main">

  <!-- NUOVA SEGNALAZIONE -->
  <div class="section">
    <div class="section-header">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/><circle cx="12" cy="17" r=".5" fill="currentColor"/>
      </svg>
      <span class="section-title">Nuova segnalazione</span>
    </div>
    <div class="section-body">
      <form id="reportForm">
        <div class="form-row">
          <div class="field">
            <label for="r-title">Titolo *</label>
            <input id="r-title" type="text" placeholder="Es. Bullismo in corridoio" required>
          </div>
          <div class="field">
            <label for="r-category">Categoria</label>
            <select id="r-category">
              <option>Bullismo</option>
              <option>Sicurezza</option>
              <option>Strutture</option>
              <option>Comportamento</option>
              <option selected>Altro</option>
            </select>
          </div>
        </div>
        <div class="field">
          <label for="r-desc">Descrizione *</label>
          <textarea id="r-desc" placeholder="Descrivi la situazione nel dettaglio…" required></textarea>
        </div>
        <div class="form-row">
          <div class="field">
            <label for="r-priority">Priorità</label>
            <select id="r-priority">
              <option>Bassa</option>
              <option selected>Media</option>
              <option>Alta</option>
            </select>
          </div>
          <div class="field">
            <label for="r-name">Il tuo nome</label>
            <input id="r-name" type="text" placeholder="Lascia vuoto per rimanere anonimo">
          </div>
        </div>
        <div class="submit-row">
          <button class="btn btn-primary" type="submit" id="reportSubmit">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
            Invia segnalazione
          </button>
          <label class="anon-check">
            <input type="checkbox" id="r-anon"> Invia come anonimo
          </label>
        </div>
        <div id="reportMsg"></div>
      </form>
    </div>
  </div>

  <!-- ULTIME SEGNALAZIONI PUBBLICHE -->
  <div class="section">
    <div class="section-header">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
      </svg>
      <span class="section-title">Ultime segnalazioni</span>
    </div>
    <div class="section-body">
      <div id="reportsList"><div class="empty-state">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/>
        </svg>
        <p>Caricamento…</p>
      </div></div>
    </div>
  </div>

  <!-- VAULT PASSWORD (bloccato per ospiti) -->
  <div class="section">
    <div class="section-header">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
      <span class="section-title">Vault password</span>
      <?php if($isGuest): ?>
      <span class="pill" style="background:var(--warn-bg);color:var(--warn);margin-left:auto;font-size:.72rem;font-weight:600;padding:.15rem .55rem;border-radius:99px">Solo per registrati</span>
      <?php endif; ?>
    </div>
    <div class="section-body <?= $isGuest ? 'locked-wrap' : '' ?>">
      <?php if($isGuest): ?>
      <div class="locked-overlay">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="11" width="18" height="11" rx="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        <p>Crea un account gratuito per salvare e cifrare le tue password</p>
        <a href="register.php" class="btn btn-primary btn-sm">Registrati gratis</a>
      </div>
      <!-- Preview sfocata -->
      <div class="locked-preview" style="display:flex;flex-direction:column;gap:.5rem">
        <div class="vault-row">
          <div class="vault-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></div>
          <div class="vault-info"><div class="vault-site">Google</div><div class="vault-user">mario.rossi@gmail.com</div></div>
          <div class="vault-pw">••••••••••••</div>
        </div>
        <div class="vault-row">
          <div class="vault-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/></svg></div>
          <div class="vault-info"><div class="vault-site">Registro Elettronico</div><div class="vault-user">m.rossi</div></div>
          <div class="vault-pw">••••••••••••</div>
        </div>
        <div class="vault-row">
          <div class="vault-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></div>
          <div class="vault-info"><div class="vault-site">GitHub</div><div class="vault-user">mrossi_dev</div></div>
          <div class="vault-pw">••••••••••••</div>
        </div>
      </div>
      <?php else: ?>
      <p style="color:var(--muted);font-size:.9rem">Accedi alla dashboard completa per gestire il vault.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- PANNELLO ADMIN (bloccato per tutti tranne admin) -->
  <?php if($isGuest): ?>
  <div class="section">
    <div class="section-header">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
      <span class="section-title">Pannello amministratore</span>
      <span class="pill" style="background:color-mix(in oklab,var(--error) 12%,transparent);color:var(--error);margin-left:auto;font-size:.72rem;font-weight:600;padding:.15rem .55rem;border-radius:99px">Admin only</span>
    </div>
    <div class="section-body locked-wrap">
      <div class="locked-overlay">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <p>Questa sezione è riservata agli amministratori</p>
      </div>
      <div class="locked-preview" style="padding:.5rem 0">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-bottom:1rem">
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:.9rem;text-align:center">
            <div style="font-size:1.4rem;font-weight:700;color:var(--primary)">12</div>
            <div style="font-size:.78rem;color:var(--muted)">Segnalazioni</div>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:.9rem;text-align:center">
            <div style="font-size:1.4rem;font-weight:700;color:var(--warn)">3</div>
            <div style="font-size:.78rem;color:var(--muted)">In attesa</div>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:.9rem;text-align:center">
            <div style="font-size:1.4rem;font-weight:700;color:var(--success)">9</div>
            <div style="font-size:.78rem;color:var(--muted)">Risolte</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

</main>

<script>
// Theme toggle
(function(){
  var btn = document.getElementById('themeBtn');
  var icon = document.getElementById('themeIcon');
  var d = matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light';
  document.documentElement.setAttribute('data-theme', d);
  btn.addEventListener('click', function(){
    d = d==='dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', d);
    icon.innerHTML = d==='dark'
      ? '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>'
      : '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
  });
})();

// Carica segnalazioni
async function loadReports() {
  try {
    var r = await fetch('api.php?action=reports').then(x => x.json());
    var list = document.getElementById('reportsList');
    if (!r.ok || !r.reports.length) {
      list.innerHTML = '<div class="empty-state"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><p>Nessuna segnalazione ancora</p></div>';
      return;
    }
    var prioClass = { Alta: 'pill-alta', Media: 'pill-media', Bassa: 'pill-bassa' };
    list.innerHTML = '<div class="reports-list">' + r.reports.map(function(rep){
      var date = new Date(rep.created_at).toLocaleDateString('it-IT');
      return '<div class="report-item">'+
        '<div class="report-item-body">'+
          '<div class="report-item-title">' + escHtml(rep.title) + '</div>'+
          '<div class="report-item-meta">'+
            '<span>' + escHtml(rep.category) + '</span>'+
            '<span class="pill ' + (prioClass[rep.priority]||'') + '">' + escHtml(rep.priority) + '</span>'+
            '<span class="pill pill-status">' + escHtml(rep.status) + '</span>'+
            '<span>' + date + '</span>'+
            '<span style="color:var(--faint)">da ' + escHtml(rep.name) + '</span>'+
          '</div>'+
        '</div>'+
      '</div>';
    }).join('') + '</div>';
  } catch(e) {
    document.getElementById('reportsList').innerHTML = '<div class="empty-state"><p>Errore caricamento segnalazioni</p></div>';
  }
}

function escHtml(s) {
  return String(s).replace(/[&<>"']/g, function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
  });
}

// Invia segnalazione
document.getElementById('reportForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var btn = document.getElementById('reportSubmit');
  var msg = document.getElementById('reportMsg');
  btn.disabled = true;
  btn.textContent = 'Invio in corso…';

  var anon = document.getElementById('r-anon').checked;
  var name = document.getElementById('r-name').value.trim();

  try {
    var r = await fetch('api.php?action=report_create', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        title:       document.getElementById('r-title').value,
        category:    document.getElementById('r-category').value,
        description: document.getElementById('r-desc').value,
        priority:    document.getElementById('r-priority').value,
        name:        anon ? 'Anonimo' : (name || 'Anonimo'),
        anonymous:   anon
      })
    }).then(x => x.json());

    if (r.ok) {
      msg.innerHTML = '<div class="msg ok">Segnalazione inviata! Codice di tracciamento: <span class="tracking-box"><span class="tracking-code">' + escHtml(r.tracking_code) + '</span></span></div>';
      document.getElementById('reportForm').reset();
      loadReports();
    } else {
      msg.innerHTML = '<div class="msg err">' + escHtml(r.message || 'Errore invio') + '</div>';
    }
  } catch(err) {
    msg.innerHTML = '<div class="msg err">Errore di rete. Riprova.</div>';
  }
  btn.disabled = false;
  btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Invia segnalazione';
});

loadReports();
</script>
</body>
</html>
