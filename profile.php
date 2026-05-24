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
.field-input[readonly]{opacity:.6;cursor:not-allowed}
select.field-input{cursor:pointer}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem}
.msg{padding:.55rem .8rem;border-radius:var(--radius);font-size:.82rem;margin-top:.6rem;display:none}
.msg.visible{display:block}
.msg-ok{background:var(--success-bg);color:var(--success)}
.msg-err{background:var(--error-bg);color:var(--error)}

/* ===== AVATAR / FOTO PROFILO ===== */
.profile-hero{
  display:flex;align-items:center;gap:1.25rem;
  padding:1.25rem 1.1rem;
  background:linear-gradient(135deg,var(--primary-bg) 0%,var(--surface2) 100%);
  border-bottom:1px solid var(--border);
}
.avatar-wrap{position:relative;flex-shrink:0}
.avatar-circle{
  width:72px;height:72px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:1.7rem;font-weight:700;
  border:3px solid var(--surface);box-shadow:var(--shadow-sm);
  overflow:hidden;background:var(--primary);color:#fff;
  transition:transform .2s;
}
.avatar-circle img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.avatar-edit-btn{
  position:absolute;bottom:0;right:0;
  width:24px;height:24px;border-radius:50%;
  background:var(--primary);color:#fff;
  display:flex;align-items:center;justify-content:center;
  border:2px solid var(--surface);cursor:pointer;
  transition:background .15s;z-index:2;
}
.avatar-edit-btn:hover{background:var(--primary-hover)}
#avatarFileInput{display:none}
.avatar-upload-hint{font-size:.7rem;color:var(--text-faint);margin-top:.25rem;text-align:center}

.avatar-colors{display:flex;gap:.35rem;flex-wrap:wrap;margin-top:.5rem}
.avatar-color-btn{
  width:20px;height:20px;border-radius:50%;border:2px solid transparent;
  cursor:pointer;transition:transform .15s;flex-shrink:0;
}
.avatar-color-btn:hover,.avatar-color-btn.selected{transform:scale(1.25);border-color:var(--text)}
.avatar-picker-popover{
  position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);
  background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);
  padding:.75rem;box-shadow:var(--shadow-md);z-index:100;min-width:220px;
  display:none;
}
.avatar-picker-popover.open{display:block}
.avatar-picker-title{font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem}
.avatar-picker-sep{height:1px;background:var(--border);margin:.6rem 0}

.profile-hero-info{flex:1;min-width:0}
.profile-name{font-size:1.15rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.profile-role{font-size:.78rem;color:var(--text-muted);margin-top:.1rem}
.profile-joined{font-size:.75rem;color:var(--text-faint);margin-top:.15rem}
.profile-meta-chips{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.4rem}
.meta-chip{display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;padding:.2rem .55rem;border-radius:99px;background:var(--surface3);color:var(--text-muted);border:1px solid var(--border2)}

/* SECURITY SCORE */
.score-wrap{display:flex;align-items:center;gap:1.25rem}
.score-ring-wrap{position:relative;width:76px;height:76px;flex-shrink:0}
.score-ring{position:absolute;inset:0;border-radius:50%;background:conic-gradient(var(--primary) 0deg,var(--primary) 180deg,transparent 180deg,transparent 360deg);display:flex;align-items:center;justify-content:center}
.score-ring-inner{width:58px;height:58px;border-radius:50%;background:var(--surface);display:flex;align-items:center;justify-content:center;flex-direction:column;box-shadow:var(--shadow-sm)}
.score-value{font-size:1.15rem;font-weight:700}
.score-label{font-size:.7rem;color:var(--text-muted);margin-top:-2px}

.score-bars{flex:1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem}
.score-bar-item{display:flex;flex-direction:column;gap:.15rem}
.score-bar-label{font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em}
.score-bar-track{height:7px;border-radius:99px;background:var(--surface3);overflow:hidden}
.score-bar-fill{height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--primary),var(--success))}
.score-bar-val{font-size:.75rem;color:var(--text-faint)}

/* STATS & BADGE */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.75rem;margin-bottom:.3rem}
.stat-pill{border-radius:var(--radius-lg);padding:.5rem .75rem;background:var(--surface2);border:1px solid var(--border2);display:flex;flex-direction:column;gap:.1rem}
.stat-label{font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em}
.stat-value{font-size:.95rem;font-weight:600}
.stat-note{font-size:.72rem;color:var(--text-faint)}

.badges-grid{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.35rem}
.badge-chip{display:inline-flex;align-items:center;gap:.25rem;font-size:.72rem;padding:.18rem .55rem;border-radius:99px;border:1px solid var(--border2);background:var(--surface2);color:var(--text-muted)}
.badge-dot{width:6px;height:6px;border-radius:50%;background:var(--primary)}

/* LEADERBOARD */
.leaderboard-list{display:flex;flex-direction:column;gap:.35rem;margin-top:.6rem}
.lb-row{display:flex;align-items:center;gap:.45rem;padding:.3rem .45rem;border-radius:var(--radius);background:var(--surface2);border:1px solid transparent}
.lb-row.me{border-color:var(--primary-light);background:var(--primary-bg)}
.lb-rank{width:20px;font-size:.78rem;font-weight:600;color:var(--text-faint)}
.lb-name{flex:1;font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lb-xp{font-size:.78rem;color:var(--text-muted)}

/* PROFILE FORM / PREFERENCES */
.section{display:flex;flex-direction:column;gap:.4rem;margin-bottom:1rem}
.section-title{font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted)}
.section-desc{font-size:.8rem;color:var(--text-faint)}

.actions-row{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.75rem}

/* FOOTER */
.footer-note{font-size:.72rem;color:var(--text-faint);text-align:center;margin-top:.25rem}

/* RESPONSIVE */
@media (max-width:720px){
  .profile-hero{flex-direction:column;align-items:flex-start}
  .avatar-wrap{align-self:flex-start}
  .score-wrap{flex-direction:column;align-items:flex-start}
  .score-ring-wrap{margin-bottom:.4rem}
}
@media (max-width:520px){
  .form-grid,.form-grid-3{grid-template-columns:1fr}
}
</style>
</head>
<body>
<header class="nav">
  <div class="nav-top">
    <a class="nav-brand" href="index.php">
      <svg class="brand-icon" viewBox="0 0 32 32" aria-hidden="true">
        <defs>
          <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#016469"/>
            <stop offset="1" stop-color="#0b8793"/>
          </linearGradient>
        </defs>
        <rect x="3" y="5" width="26" height="22" rx="7" fill="url(#g1)"/>
        <path d="M10 15.5h12M10 19.5h7" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>
        <circle cx="11" cy="11" r="1.4" fill="#fff"/>
      </svg>
      <span>SafeSchool Hub</span>
    </a>
    <div class="nav-right">
      <a class="btn btn-ghost btn-sm" href="index.php">Dashboard</a>
      <a class="btn btn-ghost btn-sm" href="logout.php">Esci</a>
    </div>
  </div>
</header>

<main class="page-wrap" id="pageRoot" aria-live="polite">

  <section class="card" id="profileCard" aria-busy="true">
    <div class="profile-hero">
      <div class="avatar-wrap">
        <div class="avatar-circle" id="avatarCircle">
          <span id="avatarInitials"><?= htmlspecialchars(mb_strtoupper(mb_substr($userName,0,2,'UTF-8'),'UTF-8')) ?></span>
        </div>
      </div>
      <div class="profile-hero-info">
        <div class="profile-name" id="profileName"><?= htmlspecialchars($userName) ?></div>
        <div class="profile-role" id="profileRole">Caricamento ruolo…</div>
        <div class="profile-joined" id="profileJoined">Caricamento dati profilo…</div>
        <div class="profile-meta-chips">
          <span class="meta-chip" id="metaClass">Classe/sezione: —</span>
          <span class="meta-chip" id="metaReports">Segnalazioni: —</span>
          <span class="meta-chip" id="metaVault">Credenziali salvate: —</span>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="score-wrap">
        <div class="score-ring-wrap">
          <div class="score-ring" id="scoreRing">
            <div class="score-ring-inner">
              <div class="score-value" id="scoreValue">0</div>
              <div class="score-label">XP</div>
            </div>
          </div>
        </div>
        <div class="score-bars">
          <div class="score-bar-item">
            <div class="score-bar-label">Segnalazioni</div>
            <div class="score-bar-track"><div class="score-bar-fill" id="barReports" style="width:0%"></div></div>
            <div class="score-bar-val" id="valReports">0 inviate</div>
          </div>
          <div class="score-bar-item">
            <div class="score-bar-label">Vault</div>
            <div class="score-bar-track"><div class="score-bar-fill" id="barVault" style="width:0%"></div></div>
            <div class="score-bar-val" id="valVault">0 credenziali</div>
          </div>
          <div class="score-bar-item">
            <div class="score-bar-label">Quiz sicurezza</div>
            <div class="score-bar-track"><div class="score-bar-fill" id="barQuiz" style="width:0%"></div></div>
            <div class="score-bar-val" id="valQuiz">Quiz non ancora svolto</div>
          </div>
        </div>
      </div>

      <div class="stats-grid" id="statsGrid">
        <div class="stat-pill">
          <div class="stat-label">XP Totali</div>
          <div class="stat-value" id="statXp">0</div>
          <div class="stat-note">Si accumulano con segnalazioni e attività</div>
        </div>
        <div class="stat-pill">
          <div class="stat-label">Segnalazioni inviate</div>
          <div class="stat-value" id="statReports">0</div>
          <div class="stat-note">Più segnali, più punti</div>
        </div>
        <div class="stat-pill">
          <div class="stat-label">Credenziali nel vault</div>
          <div class="stat-value" id="statVault">0</div>
          <div class="stat-note">Solo tu puoi leggerle</div>
        </div>
        <div class="stat-pill">
          <div class="stat-label">Badge ottenuti</div>
          <div class="stat-value" id="statBadges">0</div>
          <div class="stat-note">Obiettivi di sicurezza raggiunti</div>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Badge di sicurezza</div>
        <div class="section-desc">Ogni badge rappresenta un traguardo legato all'uso responsabile della piattaforma.</div>
        <div class="badges-grid" id="badgesGrid"></div>
      </div>

      <div class="section">
        <div class="section-title">Classifica XP</div>
        <div class="section-desc">I primi 10 utenti (esclusi gli account ospite) ordinati per punti esperienza.</div>
        <div class="leaderboard-list" id="leaderboardList"></div>
      </div>
    </div>
  </section>

  <section class="card" aria-labelledby="secProfileEdit">
    <div class="card-header">
      <div class="card-header-icon">👤</div>
      <h2 class="card-title" id="secProfileEdit">Dati profilo</h2>
    </div>
    <div class="card-body">
      <div class="section">
        <div class="section-desc">Aggiorna i tuoi dati di contatto: verranno usati solo per migliorare l'esperienza sulla piattaforma.</div>
      </div>
      <form id="profileForm" autocomplete="off">
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="nameInput">Nome e cognome</label>
            <input class="field-input" id="nameInput" name="name" type="text" maxlength="120" required>
          </div>
          <div class="field">
            <label class="field-label" for="emailInput">Email (non modificabile)</label>
            <input class="field-input" id="emailInput" type="email" readonly>
          </div>
        </div>
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="classInput">Classe e sezione</label>
            <input class="field-input" id="classInput" name="class_section" type="text" maxlength="50" placeholder="Es. 4^I INF">
          </div>
          <div class="field">
            <label class="field-label" for="phoneInput">Telefono (opzionale)</label>
            <input class="field-input" id="phoneInput" name="phone" type="text" maxlength="30" placeholder="Es. 333 1234567">
          </div>
        </div>
        <div class="field">
          <label class="field-label" for="bioInput">Breve descrizione</label>
          <textarea class="field-input" id="bioInput" name="bio" rows="3" maxlength="200" placeholder="Chi sei, cosa studi, interessi…"></textarea>
        </div>
        <div class="actions-row">
          <button type="submit" class="btn btn-primary btn-sm">Salva modifiche</button>
          <span class="footer-note" id="profileSaveHint">Le modifiche sono salvate solo nel sistema scolastico.</span>
        </div>
        <div class="msg msg-ok" id="profileOk">Profilo aggiornato correttamente.</div>
        <div class="msg msg-err" id="profileErr">Errore durante il salvataggio del profilo.</div>
      </form>
    </div>
  </section>

  <section class="card" aria-labelledby="secSecurity">
    <div class="card-header">
      <div class="card-header-icon">🔒</div>
      <h2 class="card-title" id="secSecurity">Sicurezza account</h2>
    </div>
    <div class="card-body">
      <div class="section">
        <div class="section-desc">Cambia la tua password di accesso. Si consiglia di usare almeno 12 caratteri e di includere lettere, numeri e simboli.</div>
      </div>
      <form id="passwordForm" autocomplete="off">
        <div class="form-grid">
          <div class="field">
            <label class="field-label" for="oldPw">Password attuale</label>
            <input class="field-input" id="oldPw" name="old_password" type="password" required>
          </div>
          <div class="field">
            <label class="field-label" for="newPw">Nuova password</label>
            <input class="field-input" id="newPw" name="new_password" type="password" required>
          </div>
        </div>
        <div class="actions-row">
          <button type="submit" class="btn btn-danger btn-sm">Aggiorna password</button>
        </div>
        <div class="msg msg-ok" id="pwOk">Password aggiornata correttamente.</div>
        <div class="msg msg-err" id="pwErr">Errore durante l'aggiornamento della password.</div>
      </form>

      <div class="footer-note">Le credenziali nel vault restano cifrate anche se aggiorni la password di accesso.</div>
    </div>
  </section>

</main>

<script>
(function(){
  const $ = (sel) => document.querySelector(sel);
  const page = $('#pageRoot');

  async function loadProfile(){
    try{
      const res = await fetch('api.php?action=profile_get');
      const data = await res.json();
      if(!data.ok){
        alert('Errore caricamento profilo: ' + (data.message || 'sconosciuto'));
        return;
      }
      const p = data.profile;
      const s = data.stats;
      $('#profileRole').textContent = p.role === 'admin' ? 'Amministratore' : 'Studente';
      $('#profileJoined').textContent = 'Iscritto dal ' + (p.created_at || '—');
      $('#metaClass').textContent = 'Classe/sezione: ' + (p.class_section || '—');
      $('#metaReports').textContent = 'Segnalazioni: ' + s.report_count;
      $('#metaVault').textContent = 'Credenziali salvate: ' + s.vault_count;
      $('#scoreValue').textContent = s.xp;
      $('#statXp').textContent = s.xp;
      $('#statReports').textContent = s.report_count;
      $('#statVault').textContent = s.vault_count;
      $('#statBadges').textContent = s.badge_count;
      $('#valReports').textContent = s.report_count + ' inviate';
      $('#valVault').textContent = s.vault_count + ' credenziali';
      $('#valQuiz').textContent = s.quiz_score > 0 ? ('Punteggio quiz: ' + s.quiz_score + '/12') : 'Quiz non ancora svolto';
      $('#barReports').style.width = Math.min(100, s.report_count * 10) + '%';
      $('#barVault').style.width = Math.min(100, s.vault_count * 10) + '%';
      $('#barQuiz').style.width = Math.min(100, s.quiz_score * 8) + '%';

      const ring = $('#scoreRing');
      const deg = Math.min(320, s.xp / 10 * 12);
      ring.style.background = 'conic-gradient(var(--primary) 0deg, var(--primary) ' + deg + 'deg, rgba(0,0,0,0.08) ' + deg + 'deg, transparent 360deg)';

      const nameInput = $('#nameInput');
      const emailInput = $('#emailInput');
      const classInput = $('#classInput');
      const phoneInput = $('#phoneInput');
      const bioInput = $('#bioInput');
      nameInput.value = p.name || '';
      emailInput.value = p.email || '';
      classInput.value = p.class_section || '';
      phoneInput.value = p.phone || '';
      bioInput.value = p.bio || '';
      $('#profileName').textContent = p.name || emailInput.value || 'Utente';

      const badgesGrid = $('#badgesGrid');
      badgesGrid.innerHTML = '';
      (data.earned_badges || []).forEach(b => {
        const el = document.createElement('div');
        el.className = 'badge-chip';
        const dot = document.createElement('span');
        dot.className = 'badge-dot';
        const label = document.createElement('span');
        label.textContent = b.id.replace('_',' ');
        el.appendChild(dot);
        el.appendChild(label);
        badgesGrid.appendChild(el);
      });

      const lbList = $('#leaderboardList');
      lbList.innerHTML = '';
      (data.leaderboard || []).forEach((row,idx) => {
        const el = document.createElement('div');
        el.className = 'lb-row' + (row.id === <?= (int)$userId ?> ? ' me' : '');
        const rank = document.createElement('div');
        rank.className = 'lb-rank';
        rank.textContent = '#' + (idx+1);
        const nm = document.createElement('div');
        nm.className = 'lb-name';
        nm.textContent = row.name || 'Utente';
        const xp = document.createElement('div');
        xp.className = 'lb-xp';
        xp.textContent = row.xp + ' XP';
        el.appendChild(rank); el.appendChild(nm); el.appendChild(xp);
        lbList.appendChild(el);
      });

      document.getElementById('profileCard').setAttribute('aria-busy','false');
    }catch(err){
      alert('Errore di rete o parsing profilo: ' + err.message);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadProfile();

    const profileForm = document.getElementById('profileForm');
    const profileOk = document.getElementById('profileOk');
    const profileErr = document.getElementById('profileErr');

    profileForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      profileOk.classList.remove('visible');
      profileErr.classList.remove('visible');
      const payload = {
        name: document.getElementById('nameInput').value.trim(),
        class_section: document.getElementById('classInput').value.trim(),
        phone: document.getElementById('phoneInput').value.trim(),
        bio: document.getElementById('bioInput').value.trim(),
      };
      try{
        const res = await fetch('api.php?action=profile_update', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if(data.ok){
          profileOk.classList.add('visible');
          document.getElementById('profileName').textContent = payload.name || document.getElementById('emailInput').value;
          loadProfile();
        } else {
          profileErr.textContent = data.message || 'Errore durante il salvataggio del profilo.';
          profileErr.classList.add('visible');
        }
      }catch(err){
        profileErr.textContent = 'Errore di rete: ' + err.message;
        profileErr.classList.add('visible');
      }
    });

    const pwForm = document.getElementById('passwordForm');
    const pwOk = document.getElementById('pwOk');
    const pwErr = document.getElementById('pwErr');
    pwForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      pwOk.classList.remove('visible');
      pwErr.classList.remove('visible');
      const payload = {
        old_password: document.getElementById('oldPw').value,
        new_password: document.getElementById('newPw').value,
      };
      try{
        const res = await fetch('api.php?action=change_password', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if(data.ok){
          pwOk.classList.add('visible');
          pwForm.reset();
        } else {
          pwErr.textContent = data.message || 'Errore durante l\'aggiornamento della password.';
          pwErr.classList.add('visible');
        }
      }catch(err){
        pwErr.textContent = 'Errore di rete: ' + err.message;
        pwErr.classList.add('visible');
      }
    });
  });
})();
</script>
</body>
</html>
