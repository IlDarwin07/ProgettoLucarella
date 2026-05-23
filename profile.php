<?php
require_once __DIR__ . '/includes/auth.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
if (($_SESSION['user']['role'] ?? '') === 'guest') { header('Location: guest.php'); exit; }
$user     = current_user();
$role     = $user['role'] ?? 'user';
$isAdmin  = ($role === 'admin');
$userName = $user['name'] ?? $user['email'] ?? 'Utente';
$userId   = $user['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profilo – SafeSchool Hub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root,[data-theme="light"]{
  --bg:#f5f4f0;--surface:#fff;--surface2:#f8f7f4;--surface3:#f0ede8;
  --border:rgba(40,37,29,.1);--border2:rgba(40,37,29,.06);
  --text:#1a1814;--text-muted:#6b6860;--text-faint:#aeaca6;
  --primary:#016469;--primary-hover:#024d51;
  --primary-bg:rgba(1,100,105,.08);--primary-light:rgba(1,100,105,.14);
  --warn:#b07d00;--warn-bg:rgba(176,125,0,.09);
  --error:#a12c7b;--error-bg:rgba(161,44,123,.08);
  --success:#3a7220;--success-bg:rgba(58,114,32,.08);
  --gold:#c08000;--gold-bg:rgba(192,128,0,.1);
  --radius:6px;--radius-lg:10px;--radius-xl:16px;
  --shadow-sm:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
  --shadow-md:0 4px 16px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.04);
  --font:'Inter',system-ui,sans-serif;--nav-h:56px;
}
[data-theme="dark"]{
  --bg:#141312;--surface:#1b1a18;--surface2:#1f1e1c;--surface3:#252320;
  --border:rgba(255,255,255,.09);--border2:rgba(255,255,255,.05);
  --text:#d8d6d2;--text-muted:#7a7874;--text-faint:#4e4d4b;
  --primary:#4a9298;--primary-hover:#3a7f85;
  --primary-bg:rgba(74,146,152,.1);--primary-light:rgba(74,146,152,.18);
  --warn:#d4a017;--warn-bg:rgba(212,160,23,.1);
  --error:#c45fa0;--error-bg:rgba(196,95,160,.1);
  --success:#5ea83a;--success-bg:rgba(94,168,58,.1);
  --gold:#e0a020;--gold-bg:rgba(224,160,32,.12);
  --shadow-sm:0 1px 3px rgba(0,0,0,.25);
  --shadow-md:0 4px 16px rgba(0,0,0,.35);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased;scroll-behavior:smooth}
body{min-height:100dvh;background:var(--bg);color:var(--text);font-family:var(--font);font-size:.9375rem;line-height:1.6}
img,svg{display:block}

/* NAV */
.nav{position:sticky;top:0;z-index:200;background:var(--surface);border-bottom:1px solid var(--border)}
.nav-top{height:var(--nav-h);display:flex;align-items:center;padding:0 1.25rem;gap:1rem}
.nav-brand{display:flex;align-items:center;gap:9px;text-decoration:none;color:var(--text);font-weight:700;font-size:.95rem;flex-shrink:0}
.brand-icon{width:30px;height:30px;flex-shrink:0}
.nav-right{display:flex;align-items:center;gap:.5rem;margin-left:auto}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:.45rem 1rem;border-radius:var(--radius);font-size:.82rem;font-weight:600;cursor:pointer;border:1px solid transparent;font-family:inherit;transition:background .15s,border-color .15s,color .15s;text-decoration:none;white-space:nowrap}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-primary:hover{background:var(--primary-hover)}
.btn-primary:disabled{opacity:.55;cursor:not-allowed}
.btn-ghost{background:transparent;color:var(--text-muted);border-color:var(--border)}
.btn-ghost:hover{background:var(--surface3);color:var(--text)}
.btn-sm{padding:.32rem .7rem;font-size:.78rem}
.btn-danger{background:var(--error-bg);color:var(--error);border-color:var(--error-bg)}
.btn-danger:hover{border-color:var(--error)}

/* LAYOUT */
.page-wrap{max-width:960px;margin:0 auto;padding:1.5rem 1.25rem;display:flex;flex-direction:column;gap:1.25rem}

/* CARD */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}
.card-header{display:flex;align-items:center;gap:.6rem;padding:.9rem 1.1rem;border-bottom:1px solid var(--border)}
.card-header-icon{color:var(--primary);flex-shrink:0}
.card-title{font-size:.88rem;font-weight:700;flex:1}
.card-badge{font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:99px;margin-left:auto}
.badge-primary{background:var(--primary-bg);color:var(--primary)}
.badge-success{background:var(--success-bg);color:var(--success)}
.badge-gold{background:var(--gold-bg);color:var(--gold)}
.badge-warn{background:var(--warn-bg);color:var(--warn)}
.card-body{padding:1.1rem}

/* FORM */
.field{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.85rem}
.field:last-child{margin-bottom:0}
.field-label{font-size:.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em}
.field-input{padding:.5rem .7rem;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface2);color:var(--text);font-size:.88rem;font-family:inherit;transition:border .15s,box-shadow .15s}
.field-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-bg)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
.msg{padding:.55rem .8rem;border-radius:var(--radius);font-size:.82rem;margin-top:.6rem;display:none}
.msg.visible{display:block}
.msg-ok{background:var(--success-bg);color:var(--success)}
.msg-err{background:var(--error-bg);color:var(--error)}

/* AVATAR */
.profile-hero{display:flex;align-items:center;gap:1.25rem;padding:1.25rem 1.1rem;background:linear-gradient(135deg,var(--primary-bg) 0%,var(--surface2) 100%);border-bottom:1px solid var(--border)}
.avatar-circle{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;flex-shrink:0;cursor:pointer;border:3px solid var(--surface);box-shadow:var(--shadow-sm);transition:transform .2s}
.avatar-circle:hover{transform:scale(1.05)}
.avatar-colors{display:flex;gap:.35rem;flex-wrap:wrap;margin-top:.5rem}
.avatar-color-btn{width:20px;height:20px;border-radius:50%;border:2px solid transparent;cursor:pointer;transition:transform .15s;flex-shrink:0}
.avatar-color-btn:hover,.avatar-color-btn.selected{transform:scale(1.25);border-color:var(--text)}
.profile-hero-info{flex:1;min-width:0}
.profile-name{font-size:1.15rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.profile-role{font-size:.78rem;color:var(--text-muted);margin-top:.1rem}
.profile-joined{font-size:.75rem;color:var(--text-faint);margin-top:.15rem}

/* SECURITY SCORE */
.score-wrap{display:flex;align-items:center;gap:1.25rem}
.score-ring-wrap{position:relative;width:96px;height:96px;flex-shrink:0}
.score-ring{transform:rotate(-90deg);overflow:visible}
.score-ring-bg{fill:none;stroke:var(--surface3);stroke-width:7}
.score-ring-fill{fill:none;stroke-width:7;stroke-linecap:round;transition:stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1),stroke .5s}
.score-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0}
.score-value{font-size:1.5rem;font-weight:800;line-height:1;color:var(--primary)}
.score-label-small{font-size:.6rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em}
.score-details{flex:1}
.score-title{font-size:1rem;font-weight:700;margin-bottom:.15rem}
.score-subtitle{font-size:.82rem;color:var(--text-muted);margin-bottom:.6rem;line-height:1.4}
.score-items{display:flex;flex-direction:column;gap:.3rem}
.score-item{display:flex;align-items:center;gap:.5rem;font-size:.8rem}
.score-item-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.score-item-label{flex:1;color:var(--text-muted)}
.score-item-val{font-weight:600;font-size:.78rem}

/* STATS GRID */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.75rem}
.stat-card{background:var(--surface2);border:1px solid var(--border2);border-radius:var(--radius);padding:.9rem 1rem;display:flex;flex-direction:column;gap:.2rem}
.stat-label{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted)}
.stat-value{font-size:1.4rem;font-weight:800;color:var(--primary);line-height:1.1}
.stat-sub{font-size:.73rem;color:var(--text-faint)}

/* XP BAR */
.xp-bar-wrap{margin-top:.5rem}
.xp-bar-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:.35rem;font-size:.78rem}
.xp-bar-track{height:10px;border-radius:99px;background:var(--surface3);overflow:hidden}
.xp-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--primary),var(--gold));transition:width 1s cubic-bezier(.4,0,.2,1)}
.level-badge{display:inline-flex;align-items:center;gap:.35rem;background:var(--gold-bg);color:var(--gold);border:1px solid color-mix(in oklch,var(--gold) 30%,transparent);border-radius:99px;padding:.25rem .7rem;font-size:.78rem;font-weight:700}

/* BADGES GRID */
.badges-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.6rem}
.badge-card{background:var(--surface2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:.85rem .7rem;display:flex;flex-direction:column;align-items:center;text-align:center;gap:.35rem;transition:border-color .15s,box-shadow .15s}
.badge-card:not(.locked):hover{border-color:var(--primary);box-shadow:var(--shadow-sm)}
.badge-card.locked{opacity:.42;filter:grayscale(1)}
.badge-icon{font-size:1.8rem;line-height:1}
.badge-name{font-size:.75rem;font-weight:700;color:var(--text)}
.badge-desc{font-size:.7rem;color:var(--text-muted);line-height:1.35}
.badge-earned{font-size:.68rem;color:var(--success);font-weight:600}

/* LEADERBOARD */
.leaderboard{display:flex;flex-direction:column;gap:.35rem}
.lb-row{display:flex;align-items:center;gap:.7rem;padding:.55rem .75rem;border-radius:var(--radius);background:var(--surface2);border:1px solid var(--border2)}
.lb-row.me{background:var(--primary-bg);border-color:var(--primary-light)}
.lb-rank{font-size:.82rem;font-weight:800;color:var(--text-faint);min-width:22px;text-align:center}
.lb-rank.gold{color:#c08000}
.lb-rank.silver{color:#888}
.lb-rank.bronze{color:#a0622a}
.lb-avatar{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0}
.lb-name{flex:1;font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lb-xp{font-size:.78rem;font-weight:700;color:var(--primary)}

/* DIVIDER */
.divider{height:1px;background:var(--border);margin:.4rem 0}

/* ALERT */
.alert-box{display:flex;gap:.7rem;align-items:flex-start;padding:.75rem .9rem;border-radius:var(--radius);font-size:.82rem}
.alert-info{background:var(--primary-bg);color:var(--text);border:1px solid var(--primary-light)}
.alert-icon{flex-shrink:0;margin-top:.05rem}

/* LOADING */
@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:18px;height:18px;border:2px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .7s linear infinite;margin:0 auto}

/* RESPONSIVE */
@media(max-width:600px){
  .form-grid{grid-template-columns:1fr}
  .stats-grid{grid-template-columns:1fr 1fr}
  .score-wrap{flex-direction:column;align-items:flex-start}
  .profile-hero{flex-direction:column;text-align:center;align-items:center}
  .badges-grid{grid-template-columns:repeat(3,1fr)}
}
</style>
</head>
<body>

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
    <div class="nav-right">
      <a href="index.php" class="btn btn-ghost btn-sm">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Dashboard
      </a>
      <a href="logout.php" class="btn btn-ghost btn-sm">Esci</a>
    </div>
  </div>
</nav>

<div class="page-wrap">

  <!-- ===== HERO PROFILO ===== -->
  <div class="card">
    <div class="profile-hero" id="profileHero">
      <div class="avatar-circle" id="avatarCircle" onclick="document.getElementById('avatarPicker').classList.toggle('hidden')" title="Cambia colore avatar">?</div>
      <div class="profile-hero-info">
        <div class="profile-name" id="heroName">Caricamento...</div>
        <div class="profile-role" id="heroRole"></div>
        <div class="profile-joined" id="heroJoined"></div>
        <div id="avatarPicker" class="avatar-colors hidden" style="margin-top:.55rem">
          <span style="font-size:.7rem;color:var(--text-muted);margin-right:.25rem">Colore avatar:</span>
        </div>
      </div>
      <div id="levelBadgeWrap" style="flex-shrink:0"></div>
    </div>
    <div class="card-body" style="padding-top:.85rem;padding-bottom:.85rem">
      <div class="xp-bar-wrap">
        <div class="xp-bar-header">
          <span style="font-weight:600;font-size:.8rem" id="xpLabel">XP: 0 / 100</span>
          <span style="color:var(--text-muted);font-size:.75rem" id="xpNext">Prossimo livello: —</span>
        </div>
        <div class="xp-bar-track"><div class="xp-bar-fill" id="xpFill" style="width:0%"></div></div>
      </div>
    </div>
  </div>

  <!-- ===== SECURITY SCORE ===== -->
  <div class="card">
    <div class="card-header">
      <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      <span class="card-title">Security Score</span>
      <span class="card-badge badge-primary" id="scoreBadge">Calcolo...</span>
    </div>
    <div class="card-body">
      <div class="score-wrap">
        <div class="score-ring-wrap">
          <svg class="score-ring" width="96" height="96" viewBox="0 0 96 96">
            <circle class="score-ring-bg" cx="48" cy="48" r="40"/>
            <circle class="score-ring-fill" id="scoreRingFill" cx="48" cy="48" r="40"
              stroke-dasharray="251.2" stroke-dashoffset="251.2" stroke="var(--primary)"/>
          </svg>
          <div class="score-center">
            <span class="score-value" id="scoreNum">0</span>
            <span class="score-label-small">/100</span>
          </div>
        </div>
        <div class="score-details">
          <div class="score-title" id="scoreTitle">Calcolo in corso...</div>
          <div class="score-subtitle" id="scoreSubtitle"></div>
          <div class="score-items" id="scoreItems"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== STATISTICHE ===== -->
  <div class="card">
    <div class="card-header">
      <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      <span class="card-title">Le tue statistiche</span>
    </div>
    <div class="card-body">
      <div class="stats-grid" id="statsGrid">
        <div class="stat-card"><span class="stat-label">XP totali</span><span class="stat-value" id="st-xp">—</span><span class="stat-sub">punti esperienza</span></div>
        <div class="stat-card"><span class="stat-label">Segnalazioni</span><span class="stat-value" id="st-reports">—</span><span class="stat-sub">inviate da te</span></div>
        <div class="stat-card"><span class="stat-label">Password vault</span><span class="stat-value" id="st-vault">—</span><span class="stat-sub">credenziali salvate</span></div>
        <div class="stat-card"><span class="stat-label">Badge ottenuti</span><span class="stat-value" id="st-badges">—</span><span class="stat-sub">su <?= count(array_filter($GLOBALS['_badges'] ?? [], fn($b) => true)) ?> disponibili</span></div>
        <div class="stat-card"><span class="stat-label">Quiz superati</span><span class="stat-value" id="st-quiz">—</span><span class="stat-sub">su 5 domande</span></div>
        <div class="stat-card"><span class="stat-label">Giorni attivo</span><span class="stat-value" id="st-days">—</span><span class="stat-sub">dalla registrazione</span></div>
      </div>
    </div>
  </div>

  <!-- ===== BADGE / GAMIFICATION ===== -->
  <div class="card">
    <div class="card-header">
      <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
      <span class="card-title">Progressi &amp; Badge</span>
      <span class="card-badge badge-gold">
        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        Gamification
      </span>
    </div>
    <div class="card-body">
      <div class="badges-grid" id="badgesGrid">
        <div style="grid-column:1/-1;text-align:center;padding:1.5rem"><div class="spinner"></div></div>
      </div>
    </div>
  </div>

  <!-- ===== CLASSIFICA ===== -->
  <div class="card">
    <div class="card-header">
      <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 11 12 6 7 11"/><polyline points="17 18 12 13 7 18"/></svg>
      <span class="card-title">Classifica scuola</span>
      <span class="card-badge badge-success">Top studenti sicuri</span>
    </div>
    <div class="card-body">
      <div id="leaderboardWrap"><div style="text-align:center;padding:1rem"><div class="spinner"></div></div></div>
    </div>
  </div>

  <!-- ===== DATI PERSONALI ===== -->
  <div class="card">
    <div class="card-header">
      <svg class="card-header-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <span class="card-title">Dati personali</span>
    </div>
    <div class="card-body">
      <form id="profileForm" novalidate>
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="p-name">Nome completo</label>
            <input class="field-input" id="p-name" type="text" placeholder="Mario Rossi" required>
          </div>
          <div class="field">
            <label class="field-label" for="p-email">Email</label>
            <input class="field-input" id="p-email" type="email" placeholder="mario@scuola.it" readonly style="opacity:.6;cursor:not-allowed">
          </div>
          <div class="field">
            <label class="field-label" for="p-class">Classe / Sezione</label>
            <input class="field-input" id="p-class" type="text" placeholder="Es. 5A Informatica">
          </div>
          <div class="field">
            <label class="field-label" for="p-phone">Telefono <span style="font-weight:400;text-transform:none;letter-spacing:0">(opz.)</span></label>
            <input class="field-input" id="p-phone" type="tel" placeholder="+39 333 1234567">
          </div>
        </div>
        <div class="field">
          <label class="field-label" for="p-bio">Bio breve</label>
          <input class="field-input" id="p-bio" type="text" placeholder="Es. Appassionato di cybersecurity e sviluppo web…">
        </div>
        <div style="margin-top:.75rem;display:flex;gap:.5rem;align-items:center">
          <button class="btn btn-primary" type="submit" id="profileSave">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Salva modifiche
          </button>
          <span class="msg" id="profileMsg" style="margin:0"></span>
        </div>
      </form>
      <div class="divider" style="margin:1rem 0"></div>
      <p style="font-size:.78rem;font-weight:700;color:var(--text-muted);margin-bottom:.5rem">Cambia password</p>
      <form id="pwChangeForm" novalidate>
        <div class="form-grid">
          <div class="field" style="margin:0">
            <label class="field-label" for="pw-old">Password attuale</label>
            <input class="field-input" id="pw-old" type="password" autocomplete="current-password">
          </div>
          <div class="field" style="margin:0">
            <label class="field-label" for="pw-new">Nuova password</label>
            <input class="field-input" id="pw-new" type="password" autocomplete="new-password">
          </div>
        </div>
        <div style="margin-top:.6rem;display:flex;gap:.5rem;align-items:center">
          <button class="btn btn-ghost btn-sm" type="submit">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Aggiorna password
          </button>
          <span class="msg" id="pwMsg" style="margin:0"></span>
        </div>
      </form>
    </div>
  </div>

</div><!-- /page-wrap -->

<script>
const ME_ID = <?= (int)$userId ?>;
const ME_NAME = <?= json_encode($userName) ?>;

function esc(s){return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function showMsg(el,text,ok){el.className='msg visible '+(ok?'msg-ok':'msg-err');el.textContent=text;if(ok)setTimeout(()=>el.className='msg',3500);}

/* ===== AVATAR COLORS ===== */
const AVATAR_COLORS = [
  {bg:'#016469',fg:'#fff'},  // teal
  {bg:'#a12c7b',fg:'#fff'},  // magenta
  {bg:'#3a7220',fg:'#fff'},  // green
  {bg:'#b07d00',fg:'#fff'},  // gold
  {bg:'#006494',fg:'#fff'},  // blue
  {bg:'#7a39bb',fg:'#fff'},  // purple
  {bg:'#a13544',fg:'#fff'},  // red
  {bg:'#555',fg:'#fff'},     // gray
];
let currentColor = AVATAR_COLORS[0];

function buildAvatarPicker(){
  const picker = document.getElementById('avatarPicker');
  AVATAR_COLORS.forEach((c,i)=>{
    const btn = document.createElement('button');
    btn.type='button';
    btn.className='avatar-color-btn'+(i===0?' selected':'');
    btn.style.background=c.bg;
    btn.title=c.bg;
    btn.dataset.idx=i;
    btn.addEventListener('click',()=>{
      document.querySelectorAll('.avatar-color-btn').forEach(b=>b.classList.remove('selected'));
      btn.classList.add('selected');
      currentColor=AVATAR_COLORS[i];
      applyAvatar();
      saveAvatarColor(i);
    });
    picker.appendChild(btn);
  });
}

function applyAvatar(initials, colorIdx){
  const el=document.getElementById('avatarCircle');
  if(colorIdx!==undefined){
    currentColor=AVATAR_COLORS[colorIdx]||AVATAR_COLORS[0];
    document.querySelectorAll('.avatar-color-btn').forEach((b,i)=>b.classList.toggle('selected',i===colorIdx));
  }
  el.style.background=currentColor.bg;
  el.style.color=currentColor.fg;
  if(initials)el.textContent=initials;
}

async function saveAvatarColor(idx){
  await fetch('api.php?action=profile_update',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({avatar_color:idx})
  });
}

/* ===== DEFINIZIONE BADGE ===== */
const BADGE_DEFS = [
  {id:'first_report',    icon:'📢', name:'Prima Voce',       desc:'Invia la prima segnalazione',                 xp:50},
  {id:'five_reports',    icon:'📋', name:'Giornalista',      desc:'Invia 5 segnalazioni',                       xp:150},
  {id:'ten_reports',     icon:'🗞️', name:'Redattore',        desc:'Invia 10 segnalazioni',                      xp:300},
  {id:'vault_start',     icon:'🔐', name:'Custode',          desc:'Salva la prima password nel vault',           xp:50},
  {id:'vault_pro',       icon:'🏦', name:'Vault Pro',        desc:'Salva 5+ credenziali nel vault',              xp:200},
  {id:'quiz_complete',   icon:'🎓', name:'Scholar',          desc:'Completa il quiz sicurezza',                  xp:100},
  {id:'quiz_perfect',    icon:'🏆', name:'Esperto',          desc:'Completa il quiz con punteggio pieno',        xp:250},
  {id:'strong_password', icon:'💪', name:'Password Forte',   desc:'Usa il generatore di password sicura',        xp:75},
  {id:'profile_complete',icon:'✅', name:'Profilo Completo', desc:'Compila tutti i campi del profilo',           xp:80},
  {id:'early_adopter',   icon:'🚀', name:'Early Adopter',    desc:'Tra i primi iscritti alla piattaforma',       xp:100},
  {id:'week_streak',     icon:'🔥', name:'7 Giorni Attivo',  desc:'Accedi per 7 giorni consecutivi',             xp:200},
  {id:'security_100',    icon:'🛡️', name:'Sicurezza 100%',   desc:'Raggiungi il Security Score massimo',         xp:500},
];

/* ===== LIVELLI ===== */
const LEVELS = [
  {min:0,    max:99,   name:'Novizio',    color:'#888'},
  {min:100,  max:299,  name:'Studente',   color:'#3a7220'},
  {min:300,  max:699,  name:'Guardiano',  color:'#006494'},
  {min:700,  max:1199, name:'Sentinella', color:'#016469'},
  {min:1200, max:2499, name:'Esperto',    color:'#a12c7b'},
  {min:2500, max:Infinity, name:'Maestro', color:'#b07d00'},
];

function getLevel(xp){
  return LEVELS.find(l=>xp>=l.min&&xp<=l.max)||LEVELS[0];
}

/* ===== SCORE CALCULATION ===== */
function calcScore(stats){
  // Peso: vault(30), segnalazioni(25), badge(25), quiz(20)
  const vaultScore  = Math.min(30, Math.round((stats.vault_count  / 5) * 30));
  const reportScore = Math.min(25, Math.round((stats.report_count / 5) * 25));
  const badgeScore  = Math.min(25, Math.round((stats.badge_count  / BADGE_DEFS.length) * 25));
  const quizScore   = Math.min(20, Math.round((stats.quiz_score   / 5) * 20));
  const total = vaultScore + reportScore + badgeScore + quizScore;
  return {total, vaultScore, reportScore, badgeScore, quizScore};
}

function renderScore(stats){
  const {total, vaultScore, reportScore, badgeScore, quizScore} = calcScore(stats);
  // Ring
  const circ = 2 * Math.PI * 40;
  const offset = circ - (circ * total / 100);
  const ring = document.getElementById('scoreRingFill');
  const num  = document.getElementById('scoreNum');
  const scoreColor = total>=80?'var(--success)':total>=50?'var(--warn)':'var(--error)';
  setTimeout(()=>{
    ring.style.strokeDashoffset=offset;
    ring.style.stroke=scoreColor;
    num.textContent=total;
    num.style.color=scoreColor;
  },300);
  // Badge testo
  const scoreLabel = total>=90?'Eccellente':total>=75?'Molto Buono':total>=50?'Sufficiente':total>=25?'Da migliorare':'Inizia ora';
  document.getElementById('scoreBadge').textContent=scoreLabel+' · '+total+'/100';
  document.getElementById('scoreTitle').textContent=scoreLabel+'!';
  document.getElementById('scoreSubtitle').textContent=
    total>=80?'Stai proteggendo al meglio i tuoi account. Continua così!'
    :total>=50?'Buon lavoro, ma puoi migliorare completando i badge mancanti.'
    :'Completa le attività qui sotto per aumentare il tuo punteggio.';
  // Items
  const items=[
    {label:'Vault password',  val:vaultScore+'/30',  color:vaultScore >=24?'var(--success)':vaultScore>=12?'var(--warn)':'var(--error)'},
    {label:'Segnalazioni',    val:reportScore+'/25', color:reportScore>=20?'var(--success)':reportScore>=10?'var(--warn)':'var(--error)'},
    {label:'Badge ottenuti',  val:badgeScore+'/25',  color:badgeScore >=20?'var(--success)':badgeScore>=10?'var(--warn)':'var(--error)'},
    {label:'Quiz sicurezza',  val:quizScore+'/20',   color:quizScore  >=16?'var(--success)':quizScore>=8 ?'var(--warn)':'var(--error)'},
  ];
  document.getElementById('scoreItems').innerHTML=items.map(item=>
    `<div class="score-item"><span class="score-item-dot" style="background:${item.color}"></span><span class="score-item-label">${item.label}</span><span class="score-item-val" style="color:${item.color}">${item.val}</span></div>`
  ).join('');
}

/* ===== RENDER STATS ===== */
function renderStats(stats, earned, joined){
  document.getElementById('st-xp').textContent     = stats.xp.toLocaleString('it');
  document.getElementById('st-reports').textContent = stats.report_count;
  document.getElementById('st-vault').textContent   = stats.vault_count;
  document.getElementById('st-badges').textContent  = earned.length+' / '+BADGE_DEFS.length;
  document.getElementById('st-quiz').textContent    = stats.quiz_score+' / 5';
  if(joined){
    const days = Math.floor((Date.now()-new Date(joined).getTime())/(1000*60*60*24));
    document.getElementById('st-days').textContent = days;
  }
}

/* ===== RENDER BADGES ===== */
function renderBadges(earned){
  const grid=document.getElementById('badgesGrid');
  const earnedIds=new Set(earned.map(b=>b.id));
  grid.innerHTML=BADGE_DEFS.map(b=>{
    const has=earnedIds.has(b.id);
    const earnedDate=earned.find(e=>e.id===b.id)?.earned_at||'';
    return `<div class="badge-card${has?'':' locked'}" title="${esc(b.desc)} (+${b.xp} XP)">
      <div class="badge-icon">${b.icon}</div>
      <div class="badge-name">${esc(b.name)}</div>
      <div class="badge-desc">${esc(b.desc)}</div>
      ${has?`<div class="badge-earned">✓ +${b.xp} XP</div>`:'<div style="font-size:.68rem;color:var(--text-faint)">Bloccato</div>'}
    </div>`;
  }).join('');
}

/* ===== RENDER XP BAR ===== */
function renderXP(xp){
  const lvl=getLevel(xp);
  const nextLvlIdx=LEVELS.indexOf(lvl)+1;
  const nextLvl=LEVELS[nextLvlIdx];
  const progress=nextLvl?((xp-lvl.min)/(nextLvl.min-lvl.min)*100):100;
  setTimeout(()=>document.getElementById('xpFill').style.width=Math.min(100,progress)+'%',400);
  document.getElementById('xpLabel').textContent='XP: '+xp.toLocaleString('it');
  document.getElementById('xpNext').textContent=nextLvl?'Prossimo: '+nextLvl.name+' ('+nextLvl.min+' XP)':'Livello massimo!';
  document.getElementById('levelBadgeWrap').innerHTML=
    `<div class="level-badge" style="color:${lvl.color};border-color:${lvl.color}40;background:${lvl.color}18">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      ${esc(lvl.name)}
    </div>`;
}

/* ===== RENDER LEADERBOARD ===== */
function renderLeaderboard(lb){
  const wrap=document.getElementById('leaderboardWrap');
  if(!lb||!lb.length){wrap.innerHTML='<div style="text-align:center;color:var(--text-muted);font-size:.85rem;padding:1rem">Nessun dato disponibile</div>';return;}
  const medals=['gold','silver','bronze'];
  wrap.innerHTML='<div class="leaderboard">'+lb.slice(0,10).map((u,i)=>{
    const isMe=u.id===ME_ID;
    const initials=(u.name||'?').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();
    const bg=AVATAR_COLORS[u.avatar_color||0].bg;
    const fg=AVATAR_COLORS[u.avatar_color||0].fg;
    return `<div class="lb-row${isMe?' me':''}">
      <span class="lb-rank${medals[i]?' '+medals[i]:''}">${
        i===0?'🥇':i===1?'🥈':i===2?'🥉':'#'+(i+1)
      }</span>
      <div class="lb-avatar" style="background:${bg};color:${fg}">${esc(initials)}</div>
      <span class="lb-name">${esc(u.name||'Utente')}${isMe?' <span style="color:var(--primary);font-size:.72rem">(tu)</span>':''}</span>
      <span class="lb-xp">${(u.xp||0).toLocaleString('it')} XP</span>
    </div>`;
  }).join('')+'</div>';
}

/* ===== LOAD EVERYTHING ===== */
async function loadProfile(){
  try{
    const r=await fetch('api.php?action=profile_get',{headers:{Accept:'application/json'}});
    const d=await r.json();
    if(!d.ok) throw new Error(d.message);
    const p=d.profile;
    const stats=d.stats;
    const earned=d.earned_badges||[];
    const lb=d.leaderboard||[];

    // Hero
    const initials=(p.name||'?').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();
    document.getElementById('heroName').textContent=p.name||'Utente';
    document.getElementById('heroRole').textContent=
      (p.role==='admin'?'👑 Amministratore':'🎓 Studente')+(p.class_section?' · '+p.class_section:'');
    const joined=new Date(p.created_at||Date.now());
    document.getElementById('heroJoined').textContent='Iscritto il '+joined.toLocaleDateString('it-IT',{day:'2-digit',month:'long',year:'numeric'});
    applyAvatar(initials, p.avatar_color||0);

    // XP
    renderXP(stats.xp);
    // Score
    renderScore(stats);
    // Stats
    renderStats(stats, earned, p.created_at);
    // Badges
    renderBadges(earned);
    // Leaderboard
    renderLeaderboard(lb);

    // Popola form dati personali
    document.getElementById('p-name').value  = p.name||'';
    document.getElementById('p-email').value = p.email||'';
    document.getElementById('p-class').value = p.class_section||'';
    document.getElementById('p-phone').value = p.phone||'';
    document.getElementById('p-bio').value   = p.bio||'';
  } catch(e){
    console.error(e);
  }
}

/* ===== SAVE PROFILE ===== */
document.getElementById('profileForm').addEventListener('submit',async function(e){
  e.preventDefault();
  const msg=document.getElementById('profileMsg');
  const btn=document.getElementById('profileSave');
  btn.disabled=true;
  try{
    const payload={
      name:document.getElementById('p-name').value.trim(),
      class_section:document.getElementById('p-class').value.trim(),
      phone:document.getElementById('p-phone').value.trim(),
      bio:document.getElementById('p-bio').value.trim(),
    };
    const r=await fetch('api.php?action=profile_update',{
      method:'POST',
      headers:{'Content-Type':'application/json',Accept:'application/json'},
      body:JSON.stringify(payload)
    });
    const d=await r.json();
    if(!d.ok) throw new Error(d.message||'Errore');
    showMsg(msg,'Profilo aggiornato!',true);
    // aggiorna hero name
    document.getElementById('heroName').textContent=payload.name||ME_NAME;
    document.getElementById('heroRole').textContent=
      '🎓 Studente'+(payload.class_section?' · '+payload.class_section:'');
    await loadProfile();
  } catch(err){
    showMsg(msg,err.message,false);
  } finally{
    btn.disabled=false;
  }
});

/* ===== CHANGE PASSWORD ===== */
document.getElementById('pwChangeForm').addEventListener('submit',async function(e){
  e.preventDefault();
  const msg=document.getElementById('pwMsg');
  const oldPw=document.getElementById('pw-old').value;
  const newPw=document.getElementById('pw-new').value;
  if(!oldPw||!newPw){showMsg(msg,'Compila entrambi i campi.',false);return;}
  if(newPw.length<8){showMsg(msg,'La nuova password deve avere almeno 8 caratteri.',false);return;}
  try{
    const r=await fetch('api.php?action=change_password',{
      method:'POST',
      headers:{'Content-Type':'application/json',Accept:'application/json'},
      body:JSON.stringify({old_password:oldPw,new_password:newPw})
    });
    const d=await r.json();
    if(!d.ok) throw new Error(d.message||'Errore');
    showMsg(msg,'Password aggiornata con successo!',true);
    this.reset();
  } catch(err){
    showMsg(msg,err.message,false);
  }
});

/* ===== INIT ===== */
buildAvatarPicker();
loadProfile();

// Chiudi color picker cliccando fuori
document.addEventListener('click',function(e){
  if(!e.target.closest('#avatarCircle')&&!e.target.closest('#avatarPicker')){
    document.getElementById('avatarPicker').classList.add('hidden');
  }
});
document.getElementById('avatarPicker').addEventListener('click',e=>e.stopPropagation());
document.querySelectorAll('.hidden').forEach(el=>el.style.display='none');
document.querySelectorAll('.avatar-colors').forEach(el=>{
  el.hidden=true;
  el.style.display='';
});
document.getElementById('avatarCircle').addEventListener('click',()=>{
  const p=document.getElementById('avatarPicker');
  p.hidden=!p.hidden;
});
</script>
</body>
</html>
