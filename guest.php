<?php
require_once __DIR__ . '/includes/auth.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
if (($_SESSION['user']['role'] ?? '') !== 'guest') { header('Location: index.php'); exit; }
$user = current_user();
$userName = $user['name'] ?? $user['email'] ?? 'Ospite';
?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SafeSchool Hub - Ospite</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
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
  --radius:8px;
  --radius-lg:12px;
  --shadow-sm:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
  --shadow-md:0 4px 16px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.04);
  --font:'Inter',system-ui,sans-serif;
  --nav-h:60px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased;scroll-behavior:smooth}
body{min-height:100dvh;background:var(--bg);color:var(--text);font-family:var(--font);font-size:.9375rem;line-height:1.6}
img,svg{display:block}
button,input,select,textarea{font:inherit}
a{text-decoration:none}
button{border:none}
.nav{position:sticky;top:0;z-index:200;height:var(--nav-h);display:flex;align-items:center;justify-content:space-between;padding:0 1rem;background:var(--surface);border-bottom:1px solid var(--border);gap:1rem}
.nav-brand{display:flex;align-items:center;gap:9px;color:var(--text);font-weight:700;font-size:.95rem;flex-shrink:0}
.brand-icon{width:30px;height:30px;flex-shrink:0}
.nav-center{flex:1;display:flex;align-items:center;gap:6px;overflow-x:auto;scrollbar-width:none}
.nav-center::-webkit-scrollbar{display:none}
.nav-tab{display:inline-flex;align-items:center;gap:6px;padding:.45rem .8rem;border-radius:var(--radius);font-size:.82rem;font-weight:600;color:var(--text-muted);cursor:pointer;background:transparent;white-space:nowrap;transition:background .15s,color .15s}
.nav-tab:hover{background:var(--surface3);color:var(--text)}
.nav-tab.active{background:var(--primary-bg);color:var(--primary)}
.nav-right{display:flex;align-items:center;gap:.6rem;flex-shrink:0}
.user-chip{display:inline-flex;align-items:center;gap:5px;font-size:.75rem;font-weight:600;padding:.25rem .65rem;border-radius:99px;background:var(--primary-bg);color:var(--primary);border:1px solid var(--primary-light);white-space:nowrap}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:.5rem 1rem;border-radius:var(--radius);font-size:.82rem;font-weight:600;cursor:pointer;border:1px solid transparent;transition:background .15s,border-color .15s,color .15s;text-decoration:none;white-space:nowrap}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-primary:hover{background:var(--primary-hover);border-color:var(--primary-hover)}
.btn-primary:disabled{opacity:.55;cursor:not-allowed}
.btn-ghost{background:transparent;color:var(--text-muted);border-color:var(--border)}
.btn-ghost:hover{background:var(--surface3);color:var(--text)}
.btn-sm{padding:.35rem .75rem;font-size:.78rem}
.page-wrap{max-width:980px;margin:0 auto;padding:1.25rem;display:flex;flex-direction:column;gap:1.25rem}
.view{display:none;flex-direction:column;gap:1.25rem}
.view.active{display:flex}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm)}
.card-header{display:flex;align-items:center;gap:.6rem;padding:.95rem 1.1rem;border-bottom:1px solid var(--border)}
.card-header-icon{color:var(--primary);flex-shrink:0}
.card-title{font-size:.9rem;font-weight:700;flex:1}
.card-badge{font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:99px;margin-left:auto}
.badge-warn{background:var(--warn-bg);color:var(--warn)}
.badge-primary{background:var(--primary-bg);color:var(--primary)}
.badge-success{background:var(--success-bg);color:var(--success)}
.card-body{padding:1.1rem}
.field{display:flex;flex-direction:column;gap:.35rem;margin-bottom:.85rem}
.field:last-child{margin-bottom:0}
.field-label{font-size:.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em}
.field-input{padding:.6rem .75rem;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface2);color:var(--text);font-size:.88rem;transition:border .15s,box-shadow .15s;width:100%}
.field-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-bg)}
textarea.field-input{min-height:96px;resize:vertical}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}
.form-row{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-top:.5rem}
.check-label{display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--text-muted);cursor:pointer}
.check-label input[type=checkbox]{accent-color:var(--primary);cursor:pointer}
.msg{padding:.6rem .8rem;border-radius:var(--radius);font-size:.82rem;margin-top:.7rem;display:none}
.msg.visible{display:block}
.msg-ok{background:var(--success-bg);color:var(--success)}
.msg-err{background:var(--error-bg);color:var(--error)}
.tracking-chip{display:inline-flex;align-items:center;gap:.5rem;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:.35rem .7rem;margin-top:.5rem}
.tracking-code{font-family:'Courier New',monospace;font-size:.95rem;font-weight:700;letter-spacing:.08em;color:var(--primary)}
.reports-stack{display:flex;flex-direction:column;gap:.5rem}
.report-row{display:flex;align-items:flex-start;gap:.75rem;padding:.8rem .9rem;border-radius:var(--radius);background:var(--surface2);border:1px solid var(--border2);transition:border-color .15s}
.report-row:hover{border-color:var(--border)}
.report-row-body{flex:1;min-width:0}
.report-row-title{font-weight:600;font-size:.88rem;margin-bottom:.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.report-row-meta{display:flex;flex-wrap:wrap;gap:.5rem;font-size:.75rem;color:var(--text-muted)}
.pill{display:inline-block;padding:.12rem .45rem;border-radius:99px;font-size:.7rem;font-weight:600}
.pill-alta{background:var(--error-bg);color:var(--error)}
.pill-media{background:var(--warn-bg);color:var(--warn)}
.pill-bassa{background:var(--success-bg);color:var(--success)}
.pill-status{background:var(--primary-bg);color:var(--primary)}
.empty-state{display:flex;flex-direction:column;align-items:center;padding:2.5rem 1rem;text-align:center;color:var(--text-muted);gap:.5rem}
.empty-state-icon{color:var(--text-faint);margin-bottom:.25rem}
.empty-state p{font-size:.85rem;max-width:28ch}
.vault-add-form{display:flex;flex-direction:column;gap:.6rem}
.vault-list{display:flex;flex-direction:column;gap:.4rem;margin-top:.5rem}
.vault-row{display:flex;align-items:center;gap:.6rem;padding:.6rem .75rem;border-radius:var(--radius);background:var(--surface2);border:1px solid var(--border2)}
.vault-row-site{font-weight:600;font-size:.85rem;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vault-row-user{font-size:.78rem;color:var(--text-muted);flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vault-pw{font-family:'Courier New',monospace;font-size:.82rem;color:var(--text-faint);min-width:80px;letter-spacing:.05em}
.vault-actions{display:flex;gap:.3rem;flex-shrink:0}
.sec-tabs{display:flex;gap:.45rem;flex-wrap:wrap;margin-bottom:1rem}
.sec-tab{display:inline-flex;align-items:center;gap:6px;padding:.38rem .8rem;border-radius:99px;font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text-muted);transition:background .15s,color .15s,border-color .15s}
.sec-tab:hover{background:var(--surface3)}
.sec-tab.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.sec-panel{display:none;flex-direction:column;gap:.85rem}
.sec-panel.active{display:flex}
.tip-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.65rem}
.tip-card{background:var(--surface2);border:1px solid var(--border2);border-radius:var(--radius);padding:.9rem}
.tip-card-icon{color:var(--primary);margin-bottom:.45rem}
.tip-card-title{font-size:.82rem;font-weight:700;margin-bottom:.25rem}
.tip-card-desc{font-size:.78rem;color:var(--text-muted);line-height:1.45}
.alert-box{display:flex;gap:.7rem;align-items:flex-start;padding:.8rem .9rem;border-radius:var(--radius);font-size:.82rem}
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
.quiz-opt{padding:.55rem .75rem;border-radius:var(--radius);border:1px solid var(--border);background:var(--surface2);font-size:.83rem;cursor:pointer;text-align:left;color:var(--text);transition:background .15s,border-color .15s}
.quiz-opt:hover{border-color:var(--primary);background:var(--primary-bg)}
.quiz-opt.correct{background:var(--success-bg);border-color:var(--success);color:var(--success)}
.quiz-opt.wrong{background:var(--error-bg);border-color:var(--error);color:var(--error)}
.quiz-feedback{font-size:.82rem;padding:.55rem .75rem;border-radius:var(--radius);display:none}
.quiz-feedback.visible{display:block}
.privacy-table{width:100%;border-collapse:collapse;font-size:.82rem}
.privacy-table th,.privacy-table td{padding:.55rem .7rem;border:1px solid var(--border);text-align:left}
.privacy-table th{background:var(--surface3);font-weight:600;color:var(--text-muted)}
.privacy-table tr:nth-child(even) td{background:var(--surface2)}
.divider{height:1px;background:var(--border);margin:.3rem 0}
.fab{position:fixed;bottom:1.5rem;right:1.5rem;width:42px;height:42px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;cursor:pointer;box-shadow:var(--shadow-md);opacity:0;pointer-events:none;transition:opacity .2s;z-index:100}
.fab.visible{opacity:1;pointer-events:all}
@media(max-width:720px){.form-grid{grid-template-columns:1fr}.page-wrap{padding:1rem}.nav{flex-wrap:wrap;height:auto;padding:.8rem 1rem}.nav-center{order:3;width:100%}.nav-right{margin-left:auto}.tip-grid{grid-template-columns:1fr 1fr}.vault-row{flex-wrap:wrap}}
@media(max-width:520px){.tip-grid{grid-template-columns:1fr}.nav-right span:last-of-type{display:none}.privacy-table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
</head>
<body>
<nav class="nav">
  <a href="guest.php" class="nav-brand">
    <svg class="brand-icon" viewBox="0 0 30 30" fill="none">
      <rect width="30" height="30" rx="7" fill="var(--primary)" opacity=".12"/>
      <path d="M15 4L24 8v8c0 5-4.5 8.5-9 10.5C5.5 24.5 1 21 1 16V8l9-4z" fill="var(--primary)" opacity=".2"/>
      <path d="M15 5.5L23 9v7c0 4.5-4 7.8-8 9.8-4-2-8-5.3-8-9.8V9l8-3.5z" stroke="var(--primary)" stroke-width="1.4" fill="none"/>
      <path d="M11 15l3 3 5-6" stroke="var(--primary)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    SafeSchool Hub
  </a>
  <div class="nav-center" role="tablist" aria-label="Navigazione sezioni">
    <button type="button" class="nav-tab active" role="tab" data-view="segnalazioni" onclick="switchView('segnalazioni',this)">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><circle cx="12" cy="17" r=".5" fill="currentColor"/></svg>
      Segnalazioni
    </button>
    <button type="button" class="nav-tab" role="tab" data-view="vault" onclick="switchView('vault',this)">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Vault password
    </button>
    <button type="button" class="nav-tab" role="tab" data-view="sicurezza" onclick="switchView('sicurezza',this)">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Sicurezza digitale
    </button>
  </div>
  <div class="nav-right">
    <span class="user-chip">
      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
      guest
    </span>
    <span style="font-size:.82rem;color:var(--text-muted)"><?= htmlspecialchars($userName) ?></span>
    <a href="logout.php" class="btn btn-ghost btn-sm">Esci</a>
  </div>
</nav>
<div class="page-wrap">
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
                <option>Bullismo</option><option>Sicurezza</option><option>Strutture</option><option>Comportamento</option><option selected>Altro</option>
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
              <select class="field-input" id="r-priority"><option>Bassa</option><option selected>Media</option><option>Alta</option></select>
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
            <label class="check-label"><input type="checkbox" id="r-anon"> Invia come anonimo</label>
          </div>
          <div class="msg" id="reportMsg" role="status"></div>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span class="card-title">Segnalazioni recenti</span>
        <button type="button" class="btn btn-ghost btn-sm" style="margin-left:auto" onclick="loadReports()" aria-label="Aggiorna lista">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5.49"/></svg>
          Aggiorna
        </button>
      </div>
      <div class="card-body"><div id="reportsList"><div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div><p>Caricamento in corso…</p></div></div></div>
    </div>
  </div>
  <div id="view-vault" class="view">
    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span class="card-title">Vault password personale</span>
        <span class="card-badge badge-success">Demo locale</span>
      </div>
      <div class="card-body">
        <div class="alert-box alert-info" style="margin-bottom:.9rem"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span>Le password nel vault ospite restano solo nel browser per questa sessione.</span></div>
        <form id="vaultForm" class="vault-add-form" novalidate>
          <div class="form-grid">
            <div class="field" style="margin:0"><label class="field-label" for="v-site">Sito / App *</label><input class="field-input" id="v-site" type="text" placeholder="Es. Instagram" required></div>
            <div class="field" style="margin:0"><label class="field-label" for="v-user">Username / Email *</label><input class="field-input" id="v-user" type="text" placeholder="nome@email.com" required></div>
            <div class="field" style="margin:0"><label class="field-label" for="v-pw">Password *</label><input class="field-input" id="v-pw" type="password" placeholder="Password" required></div>
            <div class="field" style="margin:0;justify-content:flex-end;padding-top:1.35rem"><button class="btn btn-primary" type="submit"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Aggiungi</button></div>
          </div>
        </form>
        <div class="divider" style="margin:.8rem 0"></div>
        <div id="vaultList"><div class="empty-state"><div class="empty-state-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><p>Nessuna credenziale salvata</p></div></div>
      </div>
    </div>
  </div>
  <div id="view-sicurezza" class="view">
    <div class="card">
      <div class="card-header">
        <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span class="card-title">Centro sicurezza digitale</span>
        <span class="card-badge badge-success">Gratuito per tutti</span>
      </div>
      <div class="card-body">
        <div class="sec-tabs" role="tablist" aria-label="Argomenti sicurezza">
          <button type="button" class="sec-tab active" role="tab" onclick="switchSecTab('password',this)"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>Password</button>
          <button type="button" class="sec-tab" role="tab" onclick="switchSecTab('phishing',this)"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>Phishing</button>
          <button type="button" class="sec-tab" role="tab" onclick="switchSecTab('2fa',this)"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>2FA</button>
          <button type="button" class="sec-tab" role="tab" onclick="switchSecTab('privacy',this)"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>Privacy online</button>
          <button type="button" class="sec-tab" role="tab" onclick="switchSecTab('quiz',this)"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>Test</button>
        </div>
        <div id="sectab-password" class="sec-panel active">
          <div class="alert-box alert-info"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span>Una password robusta è la prima linea di difesa del tuo account. Testa la tua qui sotto.</span></div>
          <div class="field"><label class="field-label" for="pw-test">Inserisci una password da testare</label><input class="field-input" id="pw-test" type="password" placeholder="Scrivi una password…" autocomplete="off" oninput="checkPassword(this.value)"><div class="strength-bar"><div class="strength-fill" id="strength-fill" style="width:0%;background:var(--error)"></div></div><span class="strength-label" id="strength-label" style="color:var(--text-faint)">Inserisci una password</span></div>
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
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h18"/><path d="M7 6h10"/><path d="M9 14h6"/><path d="M11 18h2"/></svg></div><div class="tip-card-title">Lunghezza > Complessità</div><div class="tip-card-desc">Una frase lunga è spesso più sicura di una password corta ma casuale.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2v6h-6"/><path d="M3 22v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.13-3.36L21 8"/><path d="M20.49 15A9 9 0 0 1 6.36 18.36L3 16"/></svg></div><div class="tip-card-title">Unica per ogni sito</div><div class="tip-card-desc">Non riutilizzare mai la stessa password su più piattaforme.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h10v4H7z"/><path d="M6 11h12v8H6z"/><path d="M10 15h4"/></svg></div><div class="tip-card-title">Usa un password manager</div><div class="tip-card-desc">Bitwarden o KeePass aiutano a generare e conservare password sicure.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg></div><div class="tip-card-title">Evita dati personali</div><div class="tip-card-desc">Nome, data di nascita e numeri noti sono facili da indovinare.</div></div>
          </div>
        </div>
        <div id="sectab-phishing" class="sec-panel">
          <div class="alert-box alert-warn"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><circle cx="12" cy="17" r=".5" fill="currentColor"/></svg><span>Il phishing resta una delle truffe digitali più comuni. Impara a riconoscerlo subito.</span></div>
          <div class="tip-grid">
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div><div class="tip-card-title">Mittente sospetto</div><div class="tip-card-desc">Controlla sempre l'indirizzo completo del mittente.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h7l-1 8 10-12h-7z"/></svg></div><div class="tip-card-title">Urgenza artificiale</div><div class="tip-card-desc">Messaggi troppo urgenti servono spesso a farti agire senza pensare.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3.92-3.92a5 5 0 0 0-7.07-7.07L11.19 5"/><path d="M14 11a5 5 0 0 0-7.54-.54L2.54 14.38a5 5 0 1 0 7.07 7.07L12.81 19"/></svg></div><div class="tip-card-title">Link ingannevoli</div><div class="tip-card-desc">Verifica sempre dove porta davvero un link prima di aprirlo.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><div class="tip-card-title">Allegati pericolosi</div><div class="tip-card-desc">Non aprire file sospetti da mittenti sconosciuti.</div></div>
          </div>
        </div>
        <div id="sectab-2fa" class="sec-panel">
          <div class="alert-box alert-success"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>L'autenticazione a due fattori aggiunge una protezione decisiva ai tuoi account.</span></div>
          <div class="tip-grid">
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></div><div class="tip-card-title">App Authenticator</div><div class="tip-card-desc">Preferisci app dedicate ai codici rispetto ai soli SMS.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 14a5 5 0 1 1 0-10h7a5 5 0 0 1 0 10H7z"/><path d="M12 14v6"/></svg></div><div class="tip-card-title">Chiavi hardware</div><div class="tip-card-desc">Le chiavi fisiche sono tra i metodi più sicuri disponibili.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M8 6h8"/><path d="M8 10h8"/></svg></div><div class="tip-card-title">Codici SMS</div><div class="tip-card-desc">Meglio di niente, ma meno sicuri rispetto alle app authenticator.</div></div>
            <div class="tip-card"><div class="tip-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></div><div class="tip-card-title">Codici di backup</div><div class="tip-card-desc">Conservali offline in un posto sicuro appena attivi il 2FA.</div></div>
          </div>
        </div>
        <div id="sectab-privacy" class="sec-panel">
          <div class="alert-box alert-info"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span>I tuoi dati personali online hanno valore. Gestire bene la privacy ti protegge.</span></div>
          <table class="privacy-table"><thead><tr><th>Piattaforma</th><th>Impostazione consigliata</th><th>Rischio se ignorata</th></tr></thead><tbody><tr><td>Instagram</td><td>Profilo privato + posizione disattivata</td><td>Stalking, furto identità</td></tr><tr><td>TikTok</td><td>Account privato + limita download</td><td>Raccolta dati eccessiva</td></tr><tr><td>Google</td><td>Disattiva cronologia posizione e web</td><td>Profilazione commerciale</td></tr><tr><td>WhatsApp</td><td>Foto profilo solo contatti + 2FA attivo</td><td>Accesso da sconosciuti</td></tr><tr><td>Browser</td><td>Blocca cookie terze parti</td><td>Tracciamento cross-site</td></tr></tbody></table>
        </div>
        <div id="sectab-quiz" class="sec-panel">
          <div class="alert-box alert-info"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span>Metti alla prova le tue conoscenze sulla sicurezza digitale.</span></div>
          <div id="quiz-wrap" class="quiz-wrap"></div>
          <div style="margin-top:.75rem"><button type="button" class="btn btn-primary btn-sm" onclick="initQuiz()"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5.49"/></svg>Ricomincia quiz</button></div>
        </div>
      </div>
    </div>
  </div>
</div>
<button type="button" class="fab" id="fabTop" aria-label="Torna su" onclick="window.scrollTo({top:0,behavior:'smooth'})"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg></button>
<script>
function switchView(name,el){document.querySelectorAll('.view').forEach(function(v){v.classList.remove('active');});document.querySelectorAll('.nav-tab').forEach(function(t){t.classList.remove('active');});var v=document.getElementById('view-'+name);if(v)v.classList.add('active');if(el)el.classList.add('active');window.scrollTo({top:0,behavior:'smooth'});}
function switchSecTab(name,el){document.querySelectorAll('.sec-panel').forEach(function(p){p.classList.remove('active');});document.querySelectorAll('.sec-tab').forEach(function(t){t.classList.remove('active');});var p=document.getElementById('sectab-'+name);if(p)p.classList.add('active');if(el)el.classList.add('active');}
function escHtml(s){return String(s).replace(/[&<>"']/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function showMsg(el,text,ok){el.className='msg visible '+(ok?'msg-ok':'msg-err');el.textContent=text;}
function checkPassword(pw){var checks={len:pw.length>=12,upper:/[A-Z]/.test(pw),lower:/[a-z]/.test(pw),num:/[0-9]/.test(pw),sym:/[^A-Za-z0-9]/.test(pw)};Object.keys(checks).forEach(function(k){var el=document.getElementById('chk-'+k);if(!el)return;el.classList.toggle('ok',checks[k]);el.classList.toggle('fail',!checks[k]&&pw.length>0);});var score=Object.values(checks).filter(Boolean).length;var fill=document.getElementById('strength-fill');var label=document.getElementById('strength-label');if(pw.length===0){fill.style.width='0%';label.textContent='Inserisci una password';label.style.color='var(--text-faint)';return;}var colors=['var(--error)','var(--error)','var(--warn)','var(--warn)','var(--success)'];var labels=['Molto debole','Debole','Sufficiente','Buona','Ottima'];fill.style.width=(score*20)+'%';fill.style.background=colors[score-1]||colors[0];label.textContent=labels[score-1]||labels[0];label.style.color=colors[score-1]||colors[0];}
var quizData=[{q:'Qual è la password più sicura tra le seguenti?',opts:['password123','P@ssw0rd!2024#','mario1990','scuola'],correct:1,feedback:'Corretta! Una password lunga con simboli, numeri e maiuscole è la scelta migliore.'},{q:"Ricevi un'email sospetta che ti chiede di accedere al tuo account. Cosa fai?",opts:['Clicco subito sul link','Rispondo con le mie credenziali','Vado direttamente sul sito ufficiale','La inoltro agli amici'],correct:2,feedback:'Esatto! Non cliccare mai i link sospetti, visita direttamente il sito ufficiale.'},{q:'Cosa significa 2FA?',opts:['2 fattori di attacco','Autenticazione a due fattori','2 file allegati','Second File Access'],correct:1,feedback:'Giusto! Il 2FA aggiunge un secondo livello di verifica oltre alla password.'}];
var quizState={current:0,score:0,answered:false};
function initQuiz(){quizState={current:0,score:0,answered:false};renderQuestion();}
function renderQuestion(){var wrap=document.getElementById('quiz-wrap');if(!wrap)return;if(quizState.current>=quizData.length){wrap.innerHTML='<div class="alert-box alert-success"><svg class="alert-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Quiz completato: '+quizState.score+' / '+quizData.length+' risposte corrette.</span></div>';return;}var item=quizData[quizState.current];wrap.innerHTML='<div class="quiz-question">'+escHtml(item.q)+'</div><div class="quiz-options">'+item.opts.map(function(opt,i){return '<button type="button" class="quiz-opt" onclick="answerQuiz('+i+')">'+escHtml(opt)+'</button>';}).join('')+'</div><div class="quiz-feedback" id="quiz-feedback"></div>';}
function answerQuiz(index){if(quizState.answered)return;quizState.answered=true;var item=quizData[quizState.current];var options=document.querySelectorAll('.quiz-opt');options.forEach(function(btn,i){if(i===item.correct)btn.classList.add('correct');if(i===index&&i!==item.correct)btn.classList.add('wrong');btn.disabled=true;});if(index===item.correct)quizState.score++;var feedback=document.getElementById('quiz-feedback');feedback.className='quiz-feedback visible '+(index===item.correct?'msg-ok':'msg-err');feedback.textContent=item.feedback;setTimeout(function(){quizState.current++;quizState.answered=false;renderQuestion();},1400);}
var vaultItems=[];
function renderVault(){var list=document.getElementById('vaultList');if(!list)return;if(!vaultItems.length){list.innerHTML='<div class="empty-state"><div class="empty-state-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><p>Nessuna credenziale salvata</p></div>';return;}list.innerHTML='<div class="vault-list">'+vaultItems.map(function(item,idx){return '<div class="vault-row"><div class="vault-row-site">'+escHtml(item.site)+'</div><div class="vault-row-user">'+escHtml(item.user)+'</div><div class="vault-pw">••••••••</div><div class="vault-actions"><button type="button" class="btn btn-ghost btn-sm" onclick="toggleVaultPw('+idx+',this)">Mostra</button><button type="button" class="btn btn-ghost btn-sm" onclick="removeVault('+idx+')">Elimina</button></div></div>';}).join('')+'</div>';}
function toggleVaultPw(index,btn){var row=btn.closest('.vault-row');var pw=row.querySelector('.vault-pw');var visible=btn.dataset.visible==='1';pw.textContent=visible?'••••••••':vaultItems[index].pw;btn.textContent=visible?'Mostra':'Nascondi';btn.dataset.visible=visible?'0':'1';}
function removeVault(index){vaultItems.splice(index,1);renderVault();}
document.getElementById('vaultForm').addEventListener('submit',function(e){e.preventDefault();var site=document.getElementById('v-site').value.trim();var user=document.getElementById('v-user').value.trim();var pw=document.getElementById('v-pw').value.trim();if(!site||!user||!pw)return;vaultItems.unshift({site:site,user:user,pw:pw});this.reset();renderVault();});
async function apiGet(url){var r=await fetch(url,{headers:{'Accept':'application/json'}});var t=await r.text();try{return JSON.parse(t);}catch(e){throw new Error('Risposta non valida');}}
async function loadReports(){var box=document.getElementById('reportsList');if(!box)return;box.innerHTML='<div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div><p>Caricamento in corso…</p></div>';try{var data=await apiGet('api.php?action=list_reports');var items=Array.isArray(data.reports)?data.reports:[];if(!items.length){box.innerHTML='<div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div><p>Nessuna segnalazione disponibile</p></div>';return;}box.innerHTML='<div class="reports-stack">'+items.slice(0,8).map(function(r){var priority=(r.priority||'Media').toLowerCase();var priorityClass=priority==='alta'?'pill-alta':priority==='bassa'?'pill-bassa':'pill-media';return '<div class="report-row"><div class="report-row-body"><div class="report-row-title">'+escHtml(r.title||'Segnalazione')+'</div><div class="report-row-meta"><span class="pill '+priorityClass+'">'+escHtml(r.priority||'Media')+'</span><span class="pill pill-status">'+escHtml(r.status||'Nuova')+'</span><span>'+escHtml(r.category||'Altro')+'</span><span>'+escHtml(r.created_at||'')+'</span></div></div></div>';}).join('')+'</div>';}catch(err){box.innerHTML='<div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div><p>Impossibile caricare le segnalazioni.</p></div>';}}
document.getElementById('reportForm').addEventListener('submit',async function(e){e.preventDefault();var msg=document.getElementById('reportMsg');var submit=document.getElementById('reportSubmit');var payload={title:document.getElementById('r-title').value.trim(),category:document.getElementById('r-category').value,description:document.getElementById('r-desc').value.trim(),priority:document.getElementById('r-priority').value,reporter_name:document.getElementById('r-anon').checked?'':document.getElementById('r-name').value.trim(),anonymous:document.getElementById('r-anon').checked?1:0};if(!payload.title||!payload.description){showMsg(msg,'Compila almeno titolo e descrizione.',false);return;}submit.disabled=true;try{var res=await fetch('api.php?action=create_report',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(payload)});var data=await res.json();if(!res.ok||!data.success)throw new Error(data.error||'Invio non riuscito');this.reset();showMsg(msg,'Segnalazione inviata con successo.',true);if(data.tracking_code){msg.innerHTML='Segnalazione inviata con successo.<div class="tracking-chip">Codice: <span class="tracking-code">'+escHtml(data.tracking_code)+'</span></div>';msg.className='msg visible msg-ok';}loadReports();}catch(err){showMsg(msg,err.message||'Errore durante l\'invio.',false);}finally{submit.disabled=false;}});
window.addEventListener('scroll',function(){document.getElementById('fabTop').classList.toggle('visible',window.scrollY>280);});
renderVault();initQuiz();loadReports();
</script>
</body>
</html>
