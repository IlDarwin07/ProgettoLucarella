<?php
require_once __DIR__ . '/includes/auth.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
if (($_SESSION['user']['role'] ?? '') === 'guest') { header('Location: guest.php'); exit; }
$user    = current_user();
$role    = $user['role'] ?? 'user';
$isAdmin = ($role === 'admin');
$userName = $user['name'] ?? $user['email'] ?? 'Utente';
?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SafeSchool Hub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ===== TOKENS ===== */
:root,[data-theme="light"]{
  --bg:#f5f4f0;
  --surface:#ffffff;
  --surface2:#f8f7f4;
  --surface3:#f0ede8;
  --border:rgba(40,37,29,.1);
  --border2:rgba(40,37,29,.06);
  --text:#1a1814;
  --text-muted:#6b6860;
  --text-faint:#aeaca6;
  --primary:#016469;
  --primary-hover:#024d51;
  --primary-bg:rgba(1,100,105,.08);
  --primary-light:rgba(1,100,105,.14);
  --warn:#b07d00;
  --warn-bg:rgba(176,125,0,.09);
  --error:#a12c7b;
  --error-bg:rgba(161,44,123,.08);
  --success:#3a7220;
  --success-bg:rgba(58,114,32,.08);
  --radius:6px;
  --radius-lg:10px;
  --shadow-sm:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
  --shadow-md:0 4px 16px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.04);
  --font:'Inter',system-ui,sans-serif;
  --nav-h:56px;
}
[data-theme="dark"]{
  --bg:#141312;
  --surface:#1b1a18;
  --surface2:#1f1e1c;
  --surface3:#252320;
  --border:rgba(255,255,255,.09);
  --border2:rgba(255,255,255,.05);
  --text:#d8d6d2;
  --text-muted:#7a7874;
  --text-faint:#4e4d4b;
  --primary:#4a9298;
  --primary-hover:#3a7f85;
  --primary-bg:rgba(74,146,152,.1);
  --primary-light:rgba(74,146,152,.18);
  --warn:#d4a017;
  --warn-bg:rgba(212,160,23,.1);
  --error:#c45fa0;
  --error-bg:rgba(196,95,160,.1);
  --success:#5ea83a;
  --success-bg:rgba(94,168,58,.1);
  --shadow-sm:0 1px 3px rgba(0,0,0,.25);
  --shadow-md:0 4px 16px rgba(0,0,0,.35);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased;scroll-behavior:smooth}
body{min-height:100dvh;background:var(--bg);color:var(--text);font-family:var(--font);font-size:.9375rem;line-height:1.6}
img,svg{display:block}

/* ===== NAV ===== */
.nav{
  position:sticky;top:0;z-index:200;
  background:var(--surface);
  border-bottom:1px solid var(--border);
}
.nav-top{
  height:var(--nav-h);
  display:flex;align-items:center;
  padding:0 1.25rem;
  gap:1rem;
}
.nav-brand{display:flex;align-items:center;gap:9px;text-decoration:none;color:var(--text);font-weight:700;font-size:.95rem;flex-shrink:0}
.brand-icon{width:30px;height:30px;flex-shrink:0}

/* Tab inline nella nav-top */
.nav-tabs{
  display:flex;align-items:center;gap:4px;
  overflow-x:auto;scrollbar-width:none;
  flex:1;
  -webkit-overflow-scrolling:touch;
}
.nav-tabs::-webkit-scrollbar{display:none}
.nav-tab{
  display:inline-flex;align-items:center;gap:5px;
  padding:.35rem .75rem;border-radius:var(--radius);
  font-size:.82rem;font-weight:500;color:var(--text-muted);
  cursor:pointer;border:none;background:transparent;font-family:inherit;
  white-space:nowrap;transition:background .15s,color .15s;
  text-decoration:none;flex-shrink:0;
}
.nav-tab:hover{background:var(--surface3);color:var(--text)}
.nav-tab.active{background:var(--primary-bg);color:var(--primary);font-weight:600}
.nav-right{display:flex;align-items:center;gap:.5rem;flex-shrink:0}
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:6px;
  padding:.45rem 1rem;border-radius:var(--radius);
  font-size:.82rem;font-weight:600;cursor:pointer;
  border:1px solid transparent;font-family:inherit;
  transition:background .15s,border-color .15s,color .15s;
  text-decoration:none;white-space:nowrap;
}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-primary:hover{background:var(--primary-hover);border-color:var(--primary-hover)}
.btn-primary:disabled{opacity:.55;cursor:not-allowed}
.btn-ghost{background:transparent;color:var(--text-muted);border-color:var(--border)}
.btn-ghost:hover{background:var(--surface3);color:var(--text)}
.btn-sm{padding:.32rem .7rem;font-size:.78rem}
.btn-danger{background:var(--error-bg);color:var(--error);border-color:var(--error-bg)}
.btn-danger:hover{border-color:var(--error)}

/* ===== LAYOUT ===== */
.page-wrap{max-width:960px;margin:0 auto;padding:1.5rem 1.25rem;display:flex;flex-direction:column;gap:1.25rem}

/* ===== SECTION VIEWS ===== */
.view{display:none;flex-direction:column;gap:1.25rem}
.view.active{display:flex}

/* ===== CARD ===== */
.card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius-lg);
  overflow:hidden;
}
.card-header{
  display:flex;align-items:center;gap:.6rem;
  padding:.9rem 1.1rem;
  border-bottom:1px solid var(--border);
}
.card-header-icon{color:var(--primary);flex-shrink:0}
.card-title{font-size:.88rem;font-weight:700;flex:1}
.card-badge{
  font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:99px;
  margin-left:auto;
}
.badge-warn{background:var(--warn-bg);color:var(--warn)}
.badge-primary{background:var(--primary-bg);color:var(--primary)}
.badge-success{background:var(--success-bg);color:var(--success)}
.badge-error{background:var(--error-bg);color:var(--error)}
.card-body{padding:1.1rem}

/* ===== FORM ===== */
.field{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.85rem}
.field:last-child{margin-bottom:0}
fieldset{border:none;padding:0}
.field-label{
  font-size:.75rem;font-weight:600;
  color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;
}
.field-input{
  padding:.5rem .7rem;
  border:1px solid var(--border);
  border-radius:var(--radius);
  background:var(--surface2);
  color:var(--text);
  font-size:.88rem;
  font-family:inherit;
  transition:border .15s,box-shadow .15s;
}
.field-input:focus{
  outline:none;
  border-color:var(--primary);
  box-shadow:0 0 0 3px var(--primary-bg);
}
textarea.field-input{min-height:80px;resize:vertical}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
.form-row{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-top:.5rem}
.check-label{display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--text-muted);cursor:pointer}
.check-label input[type=checkbox]{accent-color:var(--primary);cursor:pointer}
.msg{padding:.55rem .8rem;border-radius:var(--radius);font-size:.82rem;margin-top:.6rem;display:none}
.msg.visible{display:block}
.msg-ok{background:var(--success-bg);color:var(--success)}
.msg-err{background:var(--error-bg);color:var(--error)}
.tracking-chip{
  display:inline-flex;align-items:center;gap:.5rem;
  background:var(--surface2);border:1px solid var(--border);
  border-radius:var(--radius);padding:.3rem .7rem;margin-top:.4rem;
}
.tracking-code{font-family:'Courier New',monospace;font-size:.95rem;font-weight:700;letter-spacing:.08em;color:var(--primary)}

/* ===== REPORTS LIST ===== */
.reports-stack{display:flex;flex-direction:column;gap:.5rem}
.report-row{
  display:flex;align-items:flex-start;gap:.75rem;
  padding:.75rem .85rem;
  border-radius:var(--radius);
  background:var(--surface2);
  border:1px solid var(--border2);
  transition:border-color .15s;
}
.report-row:hover{border-color:var(--border)}
.report-row-body{flex:1;min-width:0}
.report-row-title{font-weight:600;font-size:.88rem;margin-bottom:.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.report-row-meta{display:flex;flex-wrap:wrap;gap:.5rem;font-size:.75rem;color:var(--text-muted)}
.pill{display:inline-block;padding:.12rem .45rem;border-radius:99px;font-size:.7rem;font-weight:600}
.pill-alta{background:var(--error-bg);color:var(--error)}
.pill-media{background:var(--warn-bg);color:var(--warn)}
.pill-bassa{background:var(--success-bg);color:var(--success)}
.pill-status{background:var(--primary-bg);color:var(--primary)}
.empty-state{
  display:flex;flex-direction:column;align-items:center;
  padding:2.5rem 1rem;text-align:center;
  color:var(--text-muted);gap:.5rem;
}
.empty-state-icon{color:var(--text-faint);margin-bottom:.25rem}
.empty-state p{font-size:.85rem;max-width:28ch}

/* ===== VAULT ===== */
.vault-add-form{display:flex;flex-direction:column;gap:.6rem}
.vault-list{display:flex;flex-direction:column;gap:.4rem;margin-top:.5rem}
.vault-row{
  display:flex;align-items:center;gap:.6rem;
  padding:.55rem .75rem;
  border-radius:var(--radius);
  background:var(--surface2);
  border:1px solid var(--border2);
}
.vault-row-site{font-weight:600;font-size:.85rem;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vault-row-user{font-size:.78rem;color:var(--text-muted);flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vault-pw{font-family:'Courier New',monospace;font-size:.82rem;color:var(--text-faint);min-width:80px;letter-spacing:.05em}
.vault-actions{display:flex;gap:.3rem;flex-shrink:0}

/* ===== SICUREZZA DIGITALE ===== */
.sec-tabs{display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:1rem}
.sec-tab{
  display:inline-flex;align-items:center;gap:5px;
  padding:.3rem .75rem;border-radius:99px;
  font-size:.78rem;font-weight:600;
  cursor:pointer;border:1px solid var(--border);
  background:transparent;color:var(--text-muted);
  font-family:inherit;transition:background .15s,color .15s,border-color .15s;
}
.sec-tab:hover{background:var(--surface3)}
.sec-tab.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.sec-tab svg{flex-shrink:0}
.sec-panel{display:none;flex-direction:column;gap:.85rem}
.sec-panel.active{display:flex}
.tip-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.6rem}
.tip-card{
  background:var(--surface2);
  border:1px solid var(--border2);
  border-radius:var(--radius);
  padding:.85rem;
}
.tip-card-icon{font-size:1.3rem;margin-bottom:.4rem}
.tip-card-title{font-size:.82rem;font-weight:700;margin-bottom:.25rem}
.tip-card-desc{font-size:.78rem;color:var(--text-muted);line-height:1.45}
.alert-box{
  display:flex;gap:.7rem;align-items:flex-start;
  padding:.75rem .9rem;
  border-radius:var(--radius);
  font-size:.82rem;
}
.alert-warn{background:var(--warn-bg);color:var(--text);border:1px solid rgba(176,125,0,.18)}
.alert-info{background:var(--primary-bg);color:var(--text);border:1px solid var(--primary-light)}
.alert-success{background:var(--success-bg);color:var(--text);border:1px solid rgba(58,114,32,.2)}
.alert-icon{flex-shrink:0;margin-top:.05rem}
.strength-bar{height:6px;border-radius:99px;background:var(--surface3);overflow:hidden;margin:.4rem 0}
.strength-fill{height:100%;border-radius:99px;transition:width .3s,background .3s}
.strength-label{font-size:.75rem;font-weight:600}
.checklist{display:flex;flex-direction:column;gap:.35rem}
.checklist-item{display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:var(--text-muted)}
.checklist-item.ok{color:var(--success)}
.checklist-item.fail{color:var(--error)}
.checklist-dot{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0}
.quiz-wrap{display:flex;flex-direction:column;gap:.7rem}
.quiz-question{font-size:.9rem;font-weight:600;margin-bottom:.2rem}
.quiz-options{display:flex;flex-direction:column;gap:.4rem}
.quiz-opt{
  padding:.5rem .75rem;border-radius:var(--radius);
  border:1px solid var(--border);background:var(--surface2);
  font-size:.83rem;cursor:pointer;text-align:left;
  font-family:inherit;color:var(--text);
  transition:background .15s,border-color .15s;
}
.quiz-opt:hover{border-color:var(--primary);background:var(--primary-bg)}
.quiz-opt.correct{background:var(--success-bg);border-color:var(--success);color:var(--success)}
.quiz-opt.wrong{background:var(--error-bg);border-color:var(--error);color:var(--error)}
.quiz-feedback{font-size:.82rem;padding:.5rem .75rem;border-radius:var(--radius);display:none}
.quiz-feedback.visible{display:block}
.privacy-table{width:100%;border-collapse:collapse;font-size:.82rem}
.privacy-table th,.privacy-table td{padding:.5rem .7rem;border:1px solid var(--border);text-align:left}
.privacy-table th{background:var(--surface3);font-weight:600;color:var(--text-muted)}
.privacy-table tr:nth-child(even) td{background:var(--surface2)}

/* ===== ADMIN ===== */
.admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.75rem}
.stat-card{
  background:var(--surface2);border:1px solid var(--border2);border-radius:var(--radius);
  padding:.9rem 1rem;display:flex;flex-direction:column;gap:.25rem;
}
.stat-label{font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted)}
.stat-value{font-size:1.5rem;font-weight:700;color:var(--primary)}
.stat-sub{font-size:.75rem;color:var(--text-faint)}

/* ===== DIVIDER ===== */
.divider{height:1px;background:var(--border);margin:.25rem 0}

/* ===== FAB ===== */
.fab{
  position:fixed;bottom:1.5rem;right:1.5rem;
  width:40px;height:40px;border-radius:50%;
  background:var(--primary);color:#fff;
  display:grid;place-items:center;
  cursor:pointer;border:none;
  box-shadow:var(--shadow-md);
  opacity:0;pointer-events:none;
  transition:opacity .2s;
  z-index:100;
}
.fab.visible{opacity:1;pointer-events:all}

/* ===== RESPONSIVE ===== */
@media(max-width:640px){
  .form-grid{grid-template-columns:1fr}
  .page-wrap{padding:1rem .9rem}
  .tip-grid{grid-template-columns:1fr 1fr}
  .vault-row{flex-wrap:wrap}
  .nav-tab span.tab-label{display:none}
}
@media(max-width:400px){
  .tip-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <div class="nav-top">
    <a href="index.php" class="nav-brand">
      <svg class="brand-icon" viewBox="0 0 30 30" fill="none">
        <rect width="30" height="30" rx="7" fill="var(--primary)" opacity=".12"/>
        <path d="M15 4L24 8v8c0 5-4.5 8.5-9 10.5C5.5 24.5 1 21 1 16V8l9-4z" fill="var(--primary)" opacity=".2"/>
        <path d="M15 5.5L23 9v7c0 4.5-4 7.8-8 9.8-4-2-8-5.3-8-9.8V9l8-3.5z" stroke="var(--primary)" stroke-width="1.4" fill="none"/>
        <path d="M11 15l3 3 5-6" stroke="var(--primary)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      SafeSchool Hub
    </a>

    <!-- Tab accanto al titolo -->
    <div class="nav-tabs" role="tablist" aria-label="Navigazione sezioni">
      <button class="nav-tab active" role="tab" data-view="segnalazioni" onclick="switchView('segnalazioni',this)">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><circle cx="12" cy="17" r=".5" fill="currentColor"/></svg>
        <span class="tab-label">Segnalazioni</span>
      </button>
      <button class="nav-tab" role="tab" data-view="vault" onclick="switchView('vault',this)">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span class="tab-label">Vault password</span>
      </button>
      <button class="nav-tab" role="tab" data-view="sicurezza" onclick="switchView('sicurezza',this)">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span class="tab-label">Sicurezza digitale</span>
      </button>
      <?php if($isAdmin): ?>
      <button class="nav-tab" role="tab" data-view="admin" onclick="switchView('admin',this)">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        <span class="tab-label">Admin</span>
      </button>
      <?php endif; ?>
    </div>

    <div class="nav-right">
      <a href="logout.php" class="btn btn-ghost btn-sm">Esci</a>
    </div>
  </div>
</nav>

<div class="page-wrap">

  <!-- ========== SEGNALAZIONI ========== -->
  <div id="view-segnalazioni" class="view active">

    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><circle cx="12" cy="17" r=".5" fill="currentColor"/></svg>
        <span class="card-title">Nuova segnalazione</span>
        <span class="card-badge badge-primary">Anonima consentita</span>
      </div>
      <div class="card-body">
        <form id="reportForm" novalidate>
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="r-title">Titolo *</label>
              <input class="field-input" id="r-title" type="text" placeholder="Es. Bullismo in corridoio" required>
            </div>
            <div class="field">
              <label class="field-label" for="r-category">Categoria</label>
              <select class="field-input" id="r-category">
                <option>Bullismo</option>
                <option>Sicurezza</option>
                <option>Strutture</option>
                <option>Comportamento</option>
                <option selected>Altro</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="r-desc">Descrizione *</label>
            <textarea class="field-input" id="r-desc" placeholder="Descrivi la situazione nel dettaglio…" required></textarea>
          </div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label" for="r-priority">Priorità</label>
              <select class="field-input" id="r-priority">
                <option>Bassa</option>
                <option selected>Media</option>
                <option>Alta</option>
              </select>
            </div>
            <div class="field">
              <label class="field-label" for="r-name">Il tuo nome <span style="font-weight:400;text-transform:none;letter-spacing:0">(opzionale)</span></label>
              <input class="field-input" id="r-name" type="text" placeholder="Lascia vuoto per rimanere anonimo" value="<?= htmlspecialchars($userName) ?>">
            </div>
          </div>
          <div class="form-row">
            <button class="btn btn-primary" type="submit" id="reportSubmit">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Invia segnalazione
            </button>
            <label class="check-label">
              <input type="checkbox" id="r-anon"> Invia come anonimo
            </label>
          </div>
          <div class="msg" id="reportMsg" role="status"></div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span class="card-title">Segnalazioni recenti</span>
        <button class="btn btn-ghost btn-sm" style="margin-left:auto" onclick="loadReports()" aria-label="Aggiorna lista">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5.49"/></svg>
          Aggiorna
        </button>
      </div>
      <div class="card-body">
        <div id="reportsList">
          <div class="empty-state">
            <div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div>
            <p>Caricamento in corso…</p>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /view-segnalazioni -->

  <!-- ========== VAULT ========== -->
  <div id="view-vault" class="view">
    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span class="card-title">Vault password personale</span>
        <span class="card-badge badge-success">Cifrato in locale</span>
      </div>
      <div class="card-body">
        <div class="alert-box alert-info" style="margin-bottom:.9rem">
          <svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <span>Le password sono salvate solo in questa sessione (demo). In produzione verranno cifrate lato server.</span>
        </div>
        <form id="vaultForm" class="vault-add-form" novalidate>
          <div class="form-grid">
            <div class="field" style="margin:0">
              <label class="field-label" for="v-site">Sito / App *</label>
              <input class="field-input" id="v-site" type="text" placeholder="Es. Instagram" required>
            </div>
            <div class="field" style="margin:0">
              <label class="field-label" for="v-user">Username / Email *</label>
              <input class="field-input" id="v-user" type="text" placeholder="nome@email.com" required>
            </div>
            <div class="field" style="margin:0">
              <label class="field-label" for="v-pw">Password *</label>
              <input class="field-input" id="v-pw" type="password" placeholder="Password" required>
            </div>
            <div class="field" style="margin:0;justify-content:flex-end;padding-top:1.35rem">
              <button class="btn btn-primary" type="submit">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Aggiungi
              </button>
            </div>
          </div>
        </form>
        <div class="divider" style="margin:.8rem 0"></div>
        <div id="vaultList">
          <div class="empty-state">
            <div class="empty-state-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <p>Nessuna credenziale salvata</p>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /view-vault -->

  <!-- ========== SICUREZZA DIGITALE ========== -->
  <div id="view-sicurezza" class="view">
    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span class="card-title">Centro sicurezza digitale</span>
        <span class="card-badge badge-success">Gratuito per tutti</span>
      </div>
      <div class="card-body">
        <div class="sec-tabs" role="tablist" aria-label="Argomenti sicurezza">
          <button class="sec-tab active" role="tab" onclick="switchSecTab('password',this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
            Password
          </button>
          <button class="sec-tab" role="tab" onclick="switchSecTab('phishing',this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Phishing
          </button>
          <button class="sec-tab" role="tab" onclick="switchSecTab('2fa',this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            2FA
          </button>
          <button class="sec-tab" role="tab" onclick="switchSecTab('privacy',this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Privacy online
          </button>
          <button class="sec-tab" role="tab" onclick="switchSecTab('quiz',this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Test
          </button>
        </div>

        <!-- PASSWORD -->
        <div id="sectab-password" class="sec-panel active">
          <div class="alert-box alert-info">
            <svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Una password robusta è la prima linea di difesa del tuo account. Testa la tua qui sotto.</span>
          </div>
          <div class="field">
            <label class="field-label" for="pw-test">Inserisci una password da testare</label>
            <input class="field-input" id="pw-test" type="password" placeholder="Scrivi una password…" autocomplete="off" oninput="checkPassword(this.value)">
            <div class="strength-bar"><div class="strength-fill" id="strength-fill" style="width:0%;background:var(--error)"></div></div>
            <span class="strength-label" id="strength-label" style="color:var(--text-faint)">Inserisci una password</span>
          </div>
          <div class="checklist" id="pw-checklist">
            <div class="checklist-item" id="chk-len"><span class="checklist-dot"></span> Almeno 12 caratteri</div>
            <div class="checklist-item" id="chk-upper"><span class="checklist-dot"></span> Una lettera maiuscola</div>
            <div class="checklist-item" id="chk-lower"><span class="checklist-dot"></span> Una lettera minuscola</div>
            <div class="checklist-item" id="chk-num"><span class="checklist-dot"></span> Un numero</div>
            <div class="checklist-item" id="chk-sym"><span class="checklist-dot"></span> Un simbolo speciale (!@#$…)</div>
          </div>
          <div class="divider"></div>
          <p style="font-size:.82rem;font-weight:700;margin-bottom:.5rem">Consigli pratici</p>
          <div class="tip-grid">
            <div class="tip-card"><div class="tip-card-icon">📏</div><div class="tip-card-title">Lunghezza > Complessità</div><div class="tip-card-desc">Una frase lunga come «CoffeIn@Mattino2024!» è più sicura di «x$8Q».</div></div>
            <div class="tip-card"><div class="tip-card-icon">🔄</div><div class="tip-card-title">Unica per ogni sito</div><div class="tip-card-desc">Non riutilizzare mai la stessa password su più piattaforme.</div></div>
            <div class="tip-card"><div class="tip-card-icon">🗝️</div><div class="tip-card-title">Usa un password manager</div><div class="tip-card-desc">Bitwarden o KeePass generano e memorizzano password sicure per te.</div></div>
            <div class="tip-card"><div class="tip-card-icon">🚻</div><div class="tip-card-title">Evita dati personali</div><div class="tip-card-desc">Nome, data di nascita, numero di telefono sono facilmente indovinabili.</div></div>
          </div>
        </div>

        <!-- PHISHING -->
        <div id="sectab-phishing" class="sec-panel">
          <div class="alert-box alert-warn">
            <svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><circle cx="12" cy="17" r=".5" fill="currentColor"/></svg>
            <span>Il phishing è la causa del <strong>90% delle violazioni informatiche</strong>. Impara a riconoscerlo.</span>
          </div>
          <div class="tip-grid">
            <div class="tip-card"><div class="tip-card-icon">📧</div><div class="tip-card-title">Mittente sospetto</div><div class="tip-card-desc">Controlla sempre l'indirizzo completo: «noreply@goog1e.com» non è Google.</div></div>
            <div class="tip-card"><div class="tip-card-icon">⚡</div><div class="tip-card-title">Urgenza artificiale</div><div class="tip-card-desc">«Il tuo account verrà disattivato tra 24 ore!» è un classico segnale di allarme.</div></div>
            <div class="tip-card"><div class="tip-card-icon">🔗</div><div class="tip-card-title">Link ingannevoli</div><div class="tip-card-desc">Passa il mouse sul link prima di cliccare: l'URL reale appare in basso nel browser.</div></div>
            <div class="tip-card"><div class="tip-card-icon">📎</div><div class="tip-card-title">Allegati pericolosi</div><div class="tip-card-desc">Non aprire .exe, .zip o .docm da mittenti sconosciuti senza verifica.</div></div>
          </div>
          <div class="divider"></div>
          <p style="font-size:.82rem;font-weight:700;margin-bottom:.5rem">Come verificare un'email sospetta</p>
          <div class="checklist">
            <div class="checklist-item"><span class="checklist-dot"></span> Controlla il dominio mittente (non solo il nome visualizzato)</div>
            <div class="checklist-item"><span class="checklist-dot"></span> Non cliccare link: vai direttamente al sito ufficiale</div>
            <div class="checklist-item"><span class="checklist-dot"></span> Segnala l'email come phishing al provider</div>
            <div class="checklist-item"><span class="checklist-dot"></span> Se riguarda la scuola, avvisa il referente IT</div>
          </div>
        </div>

        <!-- 2FA -->
        <div id="sectab-2fa" class="sec-panel">
          <div class="alert-box alert-success">
            <svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span>L'autenticazione a due fattori blocca il <strong>99,9% degli attacchi automatici</strong> agli account.</span>
          </div>
          <div class="tip-grid">
            <div class="tip-card"><div class="tip-card-icon">📱</div><div class="tip-card-title">App Authenticator</div><div class="tip-card-desc">Google Authenticator o Authy generano codici temporanei sicuri, meglio dell'SMS.</div></div>
            <div class="tip-card"><div class="tip-card-icon">🔑</div><div class="tip-card-title">Chiavi hardware</div><div class="tip-card-desc">YubiKey è la soluzione più sicura: un dispositivo fisico USB da inserire al login.</div></div>
            <div class="tip-card"><div class="tip-card-icon">📲</div><div class="tip-card-title">Codici SMS</div><div class="tip-card-desc">Meno sicuri delle app (SIM swap), ma comunque molto meglio di nessun 2FA.</div></div>
            <div class="tip-card"><div class="tip-card-icon">💾</div><div class="tip-card-title">Codici di backup</div><div class="tip-card-desc">Salva sempre i codici di ripristino in un posto sicuro offline quando attivi il 2FA.</div></div>
          </div>
          <div class="divider"></div>
          <p style="font-size:.82rem;font-weight:700;margin-bottom:.5rem">Dove attivare il 2FA subito</p>
          <div class="checklist">
            <div class="checklist-item"><span class="checklist-dot" style="background:var(--success)"></span> Account Google / Gmail</div>
            <div class="checklist-item"><span class="checklist-dot" style="background:var(--success)"></span> Instagram e Facebook</div>
            <div class="checklist-item"><span class="checklist-dot" style="background:var(--success)"></span> Account Microsoft / Teams</div>
            <div class="checklist-item"><span class="checklist-dot" style="background:var(--success)"></span> GitHub e ambienti di sviluppo</div>
            <div class="checklist-item"><span class="checklist-dot" style="background:var(--success)"></span> Password manager (priorità assoluta!)</div>
          </div>
        </div>

        <!-- PRIVACY -->
        <div id="sectab-privacy" class="sec-panel">
          <div class="alert-box alert-info">
            <svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>I tuoi dati personali online valgono. Conoscere le impostazioni di privacy ti protegge.</span>
          </div>
          <table class="privacy-table">
            <thead><tr><th>Piattaforma</th><th>Impostazione consigliata</th><th>Rischio se ignorata</th></tr></thead>
            <tbody>
              <tr><td>Instagram</td><td>Profilo privato + disattiva posizione</td><td>Stalking, furto identità</td></tr>
              <tr><td>TikTok</td><td>Account privato + limita download</td><td>Raccolta dati intensiva</td></tr>
              <tr><td>Google</td><td>Disattiva cronologia posizione e web</td><td>Profilazione commerciale</td></tr>
              <tr><td>WhatsApp</td><td>Foto profilo solo contatti + 2FA attivo</td><td>Accesso da sconosciuti</td></tr>
              <tr><td>Browser</td><td>Blocca cookie terze parti (Firefox/Brave)</td><td>Tracciamento cross-site</td></tr>
            </tbody>
          </table>
          <div class="divider"></div>
          <div class="tip-grid">
            <div class="tip-card"><div class="tip-card-icon">🌐</div><div class="tip-card-title">VPN pubblica</div><div class="tip-card-desc">Usa una VPN quando ti connetti a reti Wi-Fi pubbliche (scuola, bar, stazione).</div></div>
            <div class="tip-card"><div class="tip-card-icon">🍪</div><div class="tip-card-title">Gestisci i cookie</div><div class="tip-card-desc">Accetta solo i cookie necessari: quelli di marketing ti tracciano su tutti i siti.</div></div>
            <div class="tip-card"><div class="tip-card-icon">📍</div><div class="tip-card-title">Geolocalizzazione</div><div class="tip-card-desc">Concedi l'accesso alla posizione solo alle app che ne hanno realmente bisogno.</div></div>
            <div class="tip-card"><div class="tip-card-icon">🗑️</div><div class="tip-card-title">Digital footprint</div><div class="tip-card-desc">Elimina periodicamente account non usati: sono vettori di attacco silenziosi.</div></div>
          </div>
        </div>

        <!-- QUIZ -->
        <div id="sectab-quiz" class="sec-panel">
          <div class="alert-box alert-info">
            <svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Metti alla prova le tue conoscenze sulla sicurezza digitale. Quante ne sai?</span>
          </div>
          <div id="quiz-wrap" class="quiz-wrap"></div>
          <div style="margin-top:.75rem">
            <button class="btn btn-primary btn-sm" onclick="initQuiz()">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5.49"/></svg>
              Ricomincia quiz
            </button>
          </div>
        </div>

      </div>
    </div>
  </div><!-- /view-sicurezza -->

  <!-- ========== ADMIN ========== -->
  <?php if($isAdmin): ?>
  <div id="view-admin" class="view">
    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        <span class="card-title">Pannello amministratore</span>
        <span class="card-badge badge-error">Admin only</span>
      </div>
      <div class="card-body">
        <div class="admin-grid" style="margin-bottom:1rem">
          <div class="stat-card">
            <span class="stat-label">Segnalazioni totali</span>
            <span class="stat-value" id="admin-total">—</span>
            <span class="stat-sub">caricate dall'API</span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Alta priorità</span>
            <span class="stat-value" id="admin-alta" style="color:var(--error)">—</span>
            <span class="stat-sub">richiedono attenzione</span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Anonime</span>
            <span class="stat-value" id="admin-anon" style="color:var(--warn)">—</span>
            <span class="stat-sub">segnalazioni anonime</span>
          </div>
        </div>
        <div class="divider" style="margin:.5rem 0 1rem"></div>
        <p style="font-size:.85rem;font-weight:700;margin-bottom:.5rem">Tutte le segnalazioni</p>
        <div id="admin-reports-list">
          <div class="empty-state"><p>Clicca Aggiorna per caricare i dati.</p></div>
        </div>
        <div style="margin-top:.75rem">
          <button class="btn btn-primary btn-sm" onclick="loadAdminReports()">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5.49"/></svg>
            Aggiorna dati
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div><!-- /page-wrap -->

<button class="fab" id="fabTop" aria-label="Torna su" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<script>
/* ===== VIEW SWITCHER ===== */
function switchView(name,el){
  document.querySelectorAll('.view').forEach(function(v){v.classList.remove('active');});
  document.querySelectorAll('.nav-tab').forEach(function(t){t.classList.remove('active');});
  var v=document.getElementById('view-'+name);
  if(v)v.classList.add('active');
  if(el)el.classList.add('active');
  window.scrollTo({top:0,behavior:'smooth'});
}

/* ===== SECURITY SUB-TABS ===== */
function switchSecTab(name,el){
  document.querySelectorAll('.sec-panel').forEach(function(p){p.classList.remove('active');});
  document.querySelectorAll('.sec-tab').forEach(function(t){t.classList.remove('active');});
  var p=document.getElementById('sectab-'+name);
  if(p)p.classList.add('active');
  if(el)el.classList.add('active');
}

/* ===== HTML ESCAPE ===== */
function escHtml(s){return String(s).replace(/[&<>"']/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

/* ===== PASSWORD STRENGTH ===== */
function checkPassword(pw){
  var checks={len:pw.length>=12,upper:/[A-Z]/.test(pw),lower:/[a-z]/.test(pw),num:/[0-9]/.test(pw),sym:/[^A-Za-z0-9]/.test(pw)};
  Object.keys(checks).forEach(function(k){
    var el=document.getElementById('chk-'+k);
    if(!el)return;
    el.classList.toggle('ok',checks[k]);
    el.classList.toggle('fail',!checks[k]&&pw.length>0);
  });
  var score=Object.values(checks).filter(Boolean).length;
  var fill=document.getElementById('strength-fill');
  var label=document.getElementById('strength-label');
  if(pw.length===0){fill.style.width='0%';label.textContent='Inserisci una password';label.style.color='var(--text-faint)';return;}
  var colors=['var(--error)','var(--error)','var(--warn)','var(--warn)','var(--success)'];
  var labels=['Molto debole','Debole','Sufficiente','Buona','Ottima'];
  fill.style.width=(score*20)+'%';
  fill.style.background=colors[score-1]||colors[0];
  label.textContent=labels[score-1]||labels[0];
  label.style.color=colors[score-1]||colors[0];
}

/* ===== QUIZ ===== */
var quizData=[
  {q:'Qual è la password più sicura tra le seguenti?',opts:['password123','P@ssw0rd!2024#','mario1990','scuola'],correct:1,feedback:'Corretta! Una password lunga con simboli, numeri e maiuscole è la scelta migliore.'},
  {q:"Ricevi un'email da «supporto@g00gle.com» che ti chiede di accedere al tuo account. Cosa fai?",opts:['Clicco subito sul link','Rispondo con le mie credenziali','Ignoro e vado direttamente su google.com','Inoltro a tutti gli amici'],correct:2,feedback:'Esatto! Non cliccare mai sui link nelle email sospette. Vai sempre direttamente al sito ufficiale.'},
  {q:'Cosa significa "2FA"?',opts:['2 fattori di attacco','Autenticazione a due fattori','2 file allegati','Second File Access'],correct:1,feedback:'Giusto! Il 2FA aggiunge un secondo livello di verifica oltre alla password.'},
  {q:'Su quale rete è più rischioso accedere al tuo account bancario?',opts:['Rete di casa','Wi-Fi scuola con password','Wi-Fi pubblico senza password','Rete mobile 4G'],correct:2,feedback:'Corretto! Le reti Wi-Fi pubbliche non cifrate possono essere intercettate.'},
  {q:'Quale strumento è consigliato per gestire password diverse per ogni sito?',opts:['Un foglio di carta','Lo stesso browser','Un password manager come Bitwarden','Un file .txt sul desktop'],correct:2,feedback:'Perfetto! Un password manager cifra e gestisce automaticamente credenziali uniche per ogni sito.'}
];
var quizState={current:0,score:0,answered:false};
function initQuiz(){quizState={current:0,score:0,answered:false};renderQuestion();}
function renderQuestion(){
  var wrap=document.getElementById('quiz-wrap');
  if(!wrap)return;
  if(quizState.current>=quizData.length){
    wrap.innerHTML='<div class="alert-box alert-success"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Quiz completato: '+quizState.score+' / '+quizData.length+' risposte corrette.</span></div>';
    return;
  }
  var item=quizData[quizState.current];
  wrap.innerHTML='<div class="quiz-question">'+escHtml(item.q)+'</div><div class="quiz-options">'+item.opts.map(function(opt,i){return '<button type="button" class="quiz-opt" onclick="answerQuiz('+i+')">'+escHtml(opt)+'</button>';}).join('')+'</div><div class="quiz-feedback" id="quiz-feedback"></div>';
}
function answerQuiz(index){
  if(quizState.answered)return;
  quizState.answered=true;
  var item=quizData[quizState.current];
  var options=document.querySelectorAll('.quiz-opt');
  options.forEach(function(btn,i){
    if(i===item.correct)btn.classList.add('correct');
    if(i===index&&i!==item.correct)btn.classList.add('wrong');
    btn.disabled=true;
  });
  if(index===item.correct)quizState.score++;
  var feedback=document.getElementById('quiz-feedback');
  feedback.className='quiz-feedback visible '+(index===item.correct?'msg-ok':'msg-err');
  feedback.textContent=item.feedback;
  setTimeout(function(){quizState.current++;quizState.answered=false;renderQuestion();},1400);
}

/* ===== VAULT (sessione locale) ===== */
var vaultItems=[];
function renderVault(){
  var list=document.getElementById('vaultList');
  if(!list)return;
  if(!vaultItems.length){
    list.innerHTML='<div class="empty-state"><div class="empty-state-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><p>Nessuna credenziale salvata</p></div>';
    return;
  }
  list.innerHTML='<div class="vault-list">'+vaultItems.map(function(item,idx){return '<div class="vault-row"><div class="vault-row-site">'+escHtml(item.site)+'</div><div class="vault-row-user">'+escHtml(item.user)+'</div><div class="vault-pw">••••••••</div><div class="vault-actions"><button type="button" class="btn btn-ghost btn-sm" onclick="toggleVaultPw('+idx+',this)">Mostra</button><button type="button" class="btn btn-ghost btn-sm" onclick="removeVault('+idx+')">Elimina</button></div></div>';}).join('')+'</div>';
}
function toggleVaultPw(index,btn){
  var row=btn.closest('.vault-row');
  var pw=row.querySelector('.vault-pw');
  var visible=btn.dataset.visible==='1';
  pw.textContent=visible?'••••••••':vaultItems[index].pw;
  btn.textContent=visible?'Mostra':'Nascondi';
  btn.dataset.visible=visible?'0':'1';
}
function removeVault(index){vaultItems.splice(index,1);renderVault();}
document.getElementById('vaultForm').addEventListener('submit',function(e){
  e.preventDefault();
  var site=document.getElementById('v-site').value.trim();
  var user=document.getElementById('v-user').value.trim();
  var pw=document.getElementById('v-pw').value.trim();
  if(!site||!user||!pw)return;
  vaultItems.unshift({site:site,user:user,pw:pw});
  this.reset();
  renderVault();
});

/* ===== SHOW MSG ===== */
function showMsg(el,text,ok){
  el.className='msg visible '+(ok?'msg-ok':'msg-err');
  el.textContent=text;
}

/* ===== REPORTS ===== */
async function apiGet(url){
  var r=await fetch(url,{headers:{'Accept':'application/json'}});
  var t=await r.text();
  try{return JSON.parse(t);}catch(e){throw new Error('Risposta non valida');}
}
async function loadReports(){
  var box=document.getElementById('reportsList');
  if(!box)return;
  box.innerHTML='<div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div><p>Caricamento in corso…</p></div>';
  try{
    var data=await apiGet('api.php?action=list_reports');
    var items=Array.isArray(data.reports)?data.reports:[];
    if(!items.length){
      box.innerHTML='<div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div><p>Nessuna segnalazione disponibile</p></div>';
      return;
    }
    box.innerHTML='<div class="reports-stack">'+items.slice(0,10).map(function(r){
      var priority=(r.priority||'Media').toLowerCase();
      var priorityClass=priority==='alta'?'pill-alta':priority==='bassa'?'pill-bassa':'pill-media';
      return '<div class="report-row"><div class="report-row-body"><div class="report-row-title">'+escHtml(r.title||'Segnalazione')+'</div><div class="report-row-meta"><span class="pill '+priorityClass+'">'+escHtml(r.priority||'Media')+'</span><span class="pill pill-status">'+escHtml(r.status||'Nuova')+'</span><span>'+escHtml(r.category||'Altro')+'</span><span>'+escHtml(r.created_at||'')+'</span></div></div></div>';
    }).join('')+'</div>';
  }catch(err){
    box.innerHTML='<div class="empty-state"><p>Impossibile caricare le segnalazioni.</p></div>';
  }
}
document.getElementById('reportForm').addEventListener('submit',async function(e){
  e.preventDefault();
  var msg=document.getElementById('reportMsg');
  var submit=document.getElementById('reportSubmit');
  var payload={
    title:document.getElementById('r-title').value.trim(),
    category:document.getElementById('r-category').value,
    description:document.getElementById('r-desc').value.trim(),
    priority:document.getElementById('r-priority').value,
    reporter_name:document.getElementById('r-anon').checked?'':document.getElementById('r-name').value.trim(),
    anonymous:document.getElementById('r-anon').checked?1:0
  };
  if(!payload.title||!payload.description){showMsg(msg,'Compila almeno titolo e descrizione.',false);return;}
  submit.disabled=true;
  try{
    var res=await fetch('api.php?action=create_report',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(payload)});
    var data=await res.json();
    if(!res.ok||!data.ok)throw new Error(data.message||'Invio non riuscito');
    this.reset();
    showMsg(msg,'Segnalazione inviata con successo.',true);
    if(data.tracking_code){
      msg.innerHTML='Segnalazione inviata.<div class="tracking-chip">Codice: <span class="tracking-code">'+escHtml(data.tracking_code)+'</span></div>';
      msg.className='msg visible msg-ok';
    }
    loadReports();
  }catch(err){
    showMsg(msg,err.message||'Errore durante l\'invio.',false);
  }finally{
    submit.disabled=false;
  }
});

/* ===== ADMIN ===== */
async function loadAdminReports(){
  try{
    var data=await apiGet('api.php?action=admin_reports');
    var items=Array.isArray(data.reports)?data.reports:[];
    document.getElementById('admin-total').textContent=items.length;
    document.getElementById('admin-alta').textContent=items.filter(function(r){return r.priority==='Alta';}).length;
    document.getElementById('admin-anon').textContent=items.filter(function(r){return r.name==='Anonimo';}).length;
    var list=document.getElementById('admin-reports-list');
    if(!items.length){list.innerHTML='<div class="empty-state"><p>Nessuna segnalazione.</p></div>';return;}
    list.innerHTML='<div class="reports-stack">'+items.map(function(r){
      var priority=(r.priority||'Media').toLowerCase();
      var priorityClass=priority==='alta'?'pill-alta':priority==='bassa'?'pill-bassa':'pill-media';
      return '<div class="report-row"><div class="report-row-body"><div class="report-row-title">'+escHtml(r.title)+'</div><div class="report-row-meta"><span class="pill '+priorityClass+'">'+escHtml(r.priority)+'</span><span class="pill pill-status">'+escHtml(r.status||'Nuova')+'</span><span>'+escHtml(r.category)+'</span><span>'+escHtml(r.name||'Anonimo')+'</span><span>'+escHtml(r.created_at||'')+'</span></div></div><div style="display:flex;gap:.3rem;flex-shrink:0"><button class="btn btn-ghost btn-sm" onclick="changeStatus('+r.id+',\'In revisione\')">In revisione</button><button class="btn btn-ghost btn-sm" onclick="changeStatus('+r.id+',\'Risolta\')">Risolta</button><button class="btn btn-danger btn-sm" onclick="deleteReport('+r.id+')">Elimina</button></div></div>';
    }).join('')+'</div>';
  }catch(err){
    document.getElementById('admin-reports-list').innerHTML='<div class="empty-state"><p>Errore caricamento.</p></div>';
  }
}
async function changeStatus(id,status){
  await fetch('api.php?action=report_status',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id,status:status})});
  loadAdminReports();
}
async function deleteReport(id){
  if(!confirm('Eliminare questa segnalazione?'))return;
  await fetch('api.php?action=delete_report',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id})});
  loadAdminReports();
}

/* ===== FAB ===== */
window.addEventListener('scroll',function(){
  document.getElementById('fabTop').classList.toggle('visible',window.scrollY>280);
});

/* ===== INIT ===== */
renderVault();
initQuiz();
loadReports();
</script>
</body>
</html>
