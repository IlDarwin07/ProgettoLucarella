<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SafeSchool Hub</title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&f[]=cabinet-grotesk@700,800&display=swap" rel="stylesheet">
<style>
:root,[data-theme="light"]{
  --bg:#fdfcf7;
  --surface:#ffffff;
  --surface2:#f5f3ea;
  --border:#e2ddd0;
  --text:#1e1b14;
  --muted:#7a7263;
  --faint:#bbb5a8;
  --primary:#c49a00;
  --primary-hover:#a07e00;
  --primary-text:#1e1b14;
  --ok:#437a22;
  --warn:#c97a00;
  --danger:#a12c7b;
  --shadow:0 6px 24px rgba(30,27,20,.08);
  --radius:20px;
}
[data-theme="dark"]{
  --bg:#0f0e0a;
  --surface:#181610;
  --surface2:#201e17;
  --border:#2e2b22;
  --text:#f0ead8;
  --muted:#9c9480;
  --faint:#4a4638;
  --primary:#f5c800;
  --primary-hover:#e0b400;
  --primary-text:#0f0e0a;
  --ok:#7fd05b;
  --warn:#ffb14a;
  --danger:#ff77b7;
  --shadow:0 18px 48px rgba(0,0,0,.4);
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Satoshi,system-ui,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;transition:background .25s,color .25s}
[data-theme="dark"] body{background:radial-gradient(ellipse at 75% 0%,rgba(245,200,0,.06) 0%,transparent 38%),var(--bg)}
h1,h2,h3,h4{font-family:'Cabinet Grotesk',sans-serif;line-height:1.1}
button,input,textarea,select{font:inherit;color:inherit}
a{color:var(--primary)}
.wrap{max-width:1280px;margin:0 auto;padding:28px 20px}
/* TOPBAR */
.topbar{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:32px;flex-wrap:wrap}
.brand{display:flex;align-items:center;gap:14px}
.logo{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--primary),#ffe566);display:grid;place-items:center;font-family:'Cabinet Grotesk',sans-serif;font-weight:800;font-size:.95rem;color:#1e1b14;flex-shrink:0}
.actions{display:flex;gap:10px;flex-wrap:wrap}
/* CARD */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow)}
.p{padding:24px}
/* HERO */
.hero-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:20px;margin-bottom:32px}
.hero h1{font-size:clamp(1.9rem,3.2vw,3.5rem);margin-bottom:14px}
.sub{color:var(--muted);line-height:1.7;max-width:72ch}
.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:22px}
.kpi{background:var(--surface2);border:1px solid var(--border);border-radius:16px;padding:16px}
.kpi .num{font-size:2rem;font-weight:800;font-family:'Cabinet Grotesk',sans-serif;margin-top:6px;color:var(--primary)}
/* BUTTONS */
.btn{border:0;border-radius:12px;padding:11px 18px;font-weight:700;cursor:pointer;transition:background .18s,transform .12s,box-shadow .15s}
.btn:active{transform:scale(.97)}
.btn-primary{background:var(--primary);color:var(--primary-text)}
.btn-primary:hover{background:var(--primary-hover)}
.btn-ghost{background:transparent;border:1px solid var(--border);color:var(--text)}
.btn-ghost:hover{background:var(--surface2)}
.btn-danger{background:rgba(161,44,123,.1);color:var(--danger);border:1px solid rgba(161,44,123,.2)}
.btn-sm{padding:7px 13px;font-size:.88rem;border-radius:10px}
/* INPUTS */
.input{width:100%;padding:12px 14px;border-radius:13px;border:1px solid var(--border);background:var(--surface2);color:var(--text);transition:border-color .18s}
.input:focus{outline:none;border-color:var(--primary)}
.label{font-size:.8rem;font-weight:700;color:var(--muted);letter-spacing:.05em;text-transform:uppercase;margin-bottom:5px;display:block}
form{display:grid;gap:14px}
.field{display:grid;gap:4px}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
/* BADGES */
.badge{display:inline-flex;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:700}
.alta{background:rgba(161,44,123,.12);color:var(--danger)}
.media{background:rgba(201,122,0,.12);color:var(--warn)}
.bassa{background:rgba(67,122,34,.1);color:var(--ok)}
.bullismo{background:rgba(161,44,123,.18);color:var(--danger);border:1px solid rgba(161,44,123,.3)}
/* SECTION TITLE */
.st{display:flex;justify-content:space-between;align-items:center;margin:32px 0 16px}
.st h2{font-size:clamp(1.3rem,2vw,1.8rem)}
/* GRIDS */
.main-grid{display:grid;grid-template-columns:1.2fr .9fr;gap:20px}
.tools-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.stack{display:grid;gap:16px}
.list{display:grid;gap:12px}
.item{padding:16px;border:1px solid var(--border);border-radius:16px;background:var(--surface)}
/* SCORE */
.score-wrap{background:var(--surface2);border-radius:999px;height:10px;overflow:hidden}
.score-bar{height:100%;width:0%;background:linear-gradient(90deg,var(--danger),var(--warn) 50%,var(--ok));border-radius:999px;transition:width .4s}
/* VAULT */
.vault-card{padding:16px;border:1px solid var(--border);border-radius:16px;background:var(--surface2);display:grid;gap:8px}
.vault-header{display:flex;justify-content:space-between;align-items:center;gap:8px}
.pw-field{display:flex;gap:8px;align-items:center}
.pw-text{font-family:monospace;font-size:.92rem;background:var(--surface);padding:6px 12px;border-radius:10px;flex:1;word-break:break-all;filter:blur(4px);transition:filter .2s;cursor:pointer;border:1px solid var(--border)}
.pw-text:hover,.pw-text.shown{filter:none}
/* CHECKLIST */
.check-row{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:13px;border:1px solid var(--border);font-size:.9rem}
.check-row.pass{border-color:rgba(67,122,34,.3);background:rgba(67,122,34,.05)}
/* ANON TOGGLE */
.anon-row{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:13px;border:1px solid var(--border);background:var(--surface2);cursor:pointer;user-select:none}
.anon-row input{width:18px;height:18px;accent-color:var(--primary);cursor:pointer}
/* MODAL */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;padding:20px;z-index:100}
.modal-overlay.open{display:flex}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);max-width:580px;width:100%;padding:28px}
/* MISC */
.muted{color:var(--muted)}
.tiny{font-size:.85rem}
.hidden{display:none}
.msg{padding:10px 14px;border-radius:12px;font-size:.9rem;margin-top:10px}
.msg.ok{background:rgba(67,122,34,.1);color:var(--ok)}
.msg.err{background:rgba(161,44,123,.1);color:var(--danger)}
@media(max-width:900px){.hero-grid,.main-grid,.tools-grid,.kpis,.row2{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="brand">
      <div class="logo">SSH</div>
      <div>
        <h2 style="font-size:1.3rem">SafeSchool Hub</h2>
        <div class="muted tiny">Segnalazioni, sicurezza e strumenti digitali scolastici</div>
      </div>
    </div>
    <div class="actions">
      <button class="btn btn-ghost" id="themeBtn">&#9788; Tema</button>
      <button class="btn btn-ghost" id="authBtn">Accedi / Registrati</button>
      <button class="btn btn-primary hidden" id="logoutBtn">Logout</button>
    </div>
  </div>

  <!-- HERO -->
  <div class="hero-grid">
    <div class="card p">
      <h1>Una piattaforma scolastica sicura, utile anche senza login.</h1>
      <p class="sub">Chiunque pu&ograve; inviare segnalazioni &mdash; anche in modo anonimo &mdash; e usare gli strumenti pubblici. Chi crea un account ottiene un <strong>password manager scolastico</strong> personale su database MySQL.</p>
      <div class="kpis">
        <div class="kpi"><div class="muted tiny">Segnalazioni</div><div class="num" id="kpiReports">0</div></div>
        <div class="kpi"><div class="muted tiny">Criteri sicurezza</div><div class="num">12</div></div>
        <div class="kpi"><div class="muted tiny">Parole chiave</div><div class="num">16</div></div>
        <div class="kpi"><div class="muted tiny">Vault personale</div><div class="num" id="kpiVault">&mdash;</div></div>
      </div>
    </div>
    <div class="card p">
      <h3>Sessione corrente</h3>
      <div style="height:10px"></div>
      <p class="sub" id="sessionBox">Modalit&agrave; ospite &mdash; puoi usare segnalazioni, generatore password e calcolatore sicurezza.</p>
      <div style="height:16px"></div>
      <div class="list" id="quickList"></div>
    </div>
  </div>

  <!-- SEGNALAZIONI -->
  <div class="st"><h2>Segnalazioni pubbliche</h2><span class="muted tiny">Accessibili senza login &mdash; anche anonime</span></div>
  <div class="main-grid">
    <div class="card p">
      <form id="reportForm">
        <!-- Anonimo toggle -->
        <label class="anon-row">
          <input type="checkbox" id="anonCheck" name="anonymous" value="1">
          <span><strong>Invia in modo anonimo</strong> &mdash; il tuo nome non sar&agrave; visibile</span>
        </label>
        <div class="row2" id="nameRow">
          <div class="field"><label class="label">Nome / Classe</label><input class="input" name="name" id="nameInput" placeholder="Opzionale"></div>
          <div class="field"><label class="label">Categoria</label>
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
        <div class="field"><label class="label">Titolo *</label><input class="input" name="title" placeholder="Descrivi brevemente il problema" required></div>
        <div class="field"><label class="label">Descrizione *</label><textarea class="input" name="description" rows="4" placeholder="Dettagli, quando &egrave; successo, dove&hellip;" required></textarea></div>
        <div class="row2">
          <div class="field"><label class="label">Priorit&agrave;</label>
            <select class="input" name="priority">
              <option>Media</option><option>Alta</option><option>Bassa</option>
            </select>
          </div>
          <button class="btn btn-primary" type="submit" style="align-self:end">Invia segnalazione</button>
        </div>
      </form>
      <div id="reportMsg"></div>
    </div>
    <div class="card p">
      <h3>Ultime segnalazioni</h3>
      <div style="height:12px"></div>
      <div id="reportsList" class="list"></div>
    </div>
  </div>

  <!-- STRUMENTI -->
  <div class="st"><h2>Strumenti sicurezza</h2><span class="muted tiny">Pubblici &mdash; nessun login richiesto</span></div>
  <div class="tools-grid">
    <div class="stack">
      <!-- CALCOLATORE -->
      <div class="card p">
        <h3>Calcolatore sicurezza password</h3>
        <p class="sub tiny" style="margin:8px 0 16px">Valuta 12 criteri: lunghezza, variet&agrave; caratteri, passphrase, ripetizioni e parole deboli.</p>
        <div class="field">
          <label class="label">Inserisci o incolla una password</label>
          <input class="input" id="scoreInput" type="password" placeholder="La password non viene salvata n&eacute; inviata">
        </div>
        <div style="height:14px"></div>
        <div class="score-wrap"><div class="score-bar" id="scoreBar"></div></div>
        <div style="height:8px"></div>
        <div id="scoreText" class="muted tiny">Punteggio: 0 / 100</div>
        <div style="height:16px"></div>
        <div id="scoreChecks" class="list"></div>
      </div>
      <!-- GENERATORE -->
      <div class="card p">
        <h3>Generatore password sicure</h3>
        <div style="height:12px"></div>
        <div class="row2">
          <button class="btn btn-primary" id="genRandom" type="button">Genera casuale</button>
          <button class="btn btn-ghost" id="genPhrase" type="button">Genera passphrase</button>
        </div>
        <div style="height:12px"></div>
        <div class="pw-field">
          <input class="input" id="generatedPw" readonly placeholder="La password apparir&agrave; qui">
          <button class="btn btn-ghost btn-sm" id="copyPw" type="button">Copia</button>
        </div>
        <div id="genMsg" class="muted tiny" style="margin-top:8px"></div>
      </div>
    </div>
    <div class="stack">
      <!-- CHECKLIST -->
      <div class="card p">
        <h3>Checklist sicurezza avanzata</h3>
        <div style="height:12px"></div>
        <div id="advChecklist" class="list"></div>
      </div>
      <!-- VAULT -->
      <div class="card p">
        <h3>Password Manager scolastico</h3>
        <p class="sub tiny" style="margin:8px 0 14px">Salva credenziali di Moodle, Registro, GitHub e altri servizi scolastici. Solo per utenti autenticati.</p>
        <div id="vaultHint" class="muted tiny">Accedi per salvare le tue credenziali nel vault personale.</div>
        <form id="vaultForm" class="hidden">
          <div class="field"><label class="label">Servizio</label><input class="input" name="site_name" placeholder="es. Moodle, Registro, GitHub, Drive"></div>
          <div class="row2">
            <div class="field"><label class="label">Username / Email</label><input class="input" name="username" placeholder="username o email"></div>
            <div class="field"><label class="label">Password</label><input class="input" type="password" name="password_plain" placeholder="password del servizio"></div>
          </div>
          <div class="field"><label class="label">Note</label><textarea class="input" name="notes" rows="2" placeholder="Note opzionali"></textarea></div>
          <button class="btn btn-primary" type="submit">Salva nel vault</button>
        </form>
        <div style="height:14px"></div>
        <div id="vaultList" class="list"></div>
      </div>
    </div>
  </div>
</div>

<!-- AUTH MODAL -->
<div class="modal-overlay" id="authModal">
  <div class="modal-box">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px">
      <h2>Accesso utenti</h2>
      <button class="btn btn-ghost btn-sm" id="closeAuth">Chiudi</button>
    </div>
    <div class="row2">
      <form id="loginForm">
        <h3>Login</h3>
        <div style="height:10px"></div>
        <div class="field"><label class="label">Email</label><input class="input" name="email" type="email" placeholder="email@scuola.it"></div>
        <div class="field"><label class="label">Password</label><input class="input" name="password" type="password" placeholder="password"></div>
        <button class="btn btn-primary" type="submit">Accedi</button>
      </form>
      <form id="registerForm">
        <h3>Registrati</h3>
        <div style="height:10px"></div>
        <div class="field"><label class="label">Nome</label><input class="input" name="name" placeholder="Nome completo"></div>
        <div class="field"><label class="label">Email</label><input class="input" name="email" type="email" placeholder="email@scuola.it"></div>
        <div class="field"><label class="label">Password</label><input class="input" name="password" type="password" placeholder="min. 50/100 sicurezza"></div>
        <button class="btn btn-primary" type="submit">Crea account</button>
      </form>
    </div>
    <div id="authMsg"></div>
  </div>
</div>

<script>
const QUICK=[
  'Usa almeno 12 caratteri',
  'Attiva 2FA sulla mail scolastica',
  'Non condividere password in chat',
  'Fai logout dai PC condivisi'
];
const ADV=[
  'Non riutilizzare la stessa password su piu servizi',
  'Attiva la verifica in due passaggi sulla mail scolastica',
  'Non condividere password in chat di classe o WhatsApp',
  'Blocca lo schermo nei laboratori informatici',
  'Fai logout dai PC condivisi dopo ogni sessione',
  'Non cliccare link sospetti nelle email',
  'Usa passphrase lunghe per account importanti',
  'Conserva backup di codici e recovery key',
  'Controlla i permessi di app e estensioni browser',
  'Non salvare password in file .txt non cifrati',
  'Usa password diverse per scuola, social e giochi',
  'Cambia subito password dopo un accesso sospetto'
];

async function api(action, method, data) {
  method = method || 'GET';
  data   = data   || null;
  return fetch('api.php?action='+action, {
    method: method,
    headers: {'Content-Type':'application/json'},
    body: data ? JSON.stringify(data) : null
  }).then(function(r){ return r.json(); });
}

function msgEl(el, text, type) {
  el.innerHTML = '<div class="msg '+type+'">'+text+'</div>';
}
function badgeCls(p) {
  return p==='Alta' ? 'alta' : (p==='Bassa' ? 'bassa' : 'media');
}
function catCls(c) {
  return c==='Bullismo' ? 'bullismo' : '';
}
function scoreLabel(s) {
  return s<40 ? 'Debole' : s<60 ? 'Media' : s<80 ? 'Buona' : 'Ottima';
}

function renderQuick() {
  document.getElementById('quickList').innerHTML =
    QUICK.map(function(c){ return '<div class="check-row pass tiny">&bull; '+c+'</div>'; }).join('');
}
function renderAdv() {
  document.getElementById('advChecklist').innerHTML =
    ADV.map(function(c){ return '<div class="check-row tiny">&bull; '+c+'</div>'; }).join('');
}

async function loadReports() {
  var r = await api('reports');
  var list = r.reports || [];
  document.getElementById('kpiReports').textContent = list.length;
  document.getElementById('reportsList').innerHTML = list.length
    ? list.map(function(x){
        var catBadge = x.category==='Bullismo'
          ? '<span class="badge bullismo">'+x.category+'</span>'
          : '<span class="muted tiny">'+x.category+'</span>';
        return '<div class="item">'
          +'<div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap">'
          +'<b>'+x.title+'</b>'
          +'<span class="badge '+badgeCls(x.priority)+'">'+x.priority+'</span>'
          +'</div>'
          +'<div style="display:flex;gap:8px;margin-top:5px;flex-wrap:wrap;align-items:center">'
          +catBadge
          +'<span class="muted tiny">&bull; '+x.name+' &bull; <code>'+x.tracking_code+'</code></span>'
          +'</div>'
          +'<div class="tiny" style="margin-top:8px">'+x.description+'</div>'
          +'</div>';
      }).join('')
    : '<div class="item tiny muted">Nessuna segnalazione ancora.</div>';
}

async function loadSession() {
  var s = await api('session');
  var user = s.user;
  document.getElementById('authBtn').classList.toggle('hidden', !!user);
  document.getElementById('logoutBtn').classList.toggle('hidden', !user);
  document.getElementById('sessionBox').innerHTML = user
    ? 'Connesso come <strong>'+user.name+'</strong> ('+user.email+'). Hai accesso al password manager personale.'
    : 'Modalita ospite &mdash; puoi usare segnalazioni, generatore password e calcolatore sicurezza.';
  document.getElementById('vaultForm').classList.toggle('hidden', !user);
  document.getElementById('vaultHint').classList.toggle('hidden', !!user);
  await loadVault();
}

async function loadVault() {
  var v = await api('vault');
  var items = v.items || [];
  document.getElementById('kpiVault').textContent = items.length;
  if (!items.length) {
    document.getElementById('vaultList').innerHTML = '<div class="item tiny muted">Nessuna credenziale nel vault.</div>';
    return;
  }
  document.getElementById('vaultList').innerHTML = items.map(function(i){
    return '<div class="vault-card">'
      +'<div class="vault-header"><b>'+i.site_name+'</b>'
      +'<button class="btn btn-danger btn-sm" data-id="'+i.id+'">Elimina</button></div>'
      +'<div class="muted tiny">'+i.username+'</div>'
      +'<div class="pw-field"><span class="pw-text" title="Clicca per mostrare">'+i.password_plain+'</span></div>'
      +(i.notes ? '<div class="tiny">'+i.notes+'</div>' : '')
      +'</div>';
  }).join('');
  document.querySelectorAll('[data-id]').forEach(function(btn){
    btn.addEventListener('click', async function(){
      await api('delete_vault','POST',{id:btn.dataset.id});
      loadVault();
    });
  });
  document.querySelectorAll('.pw-text').forEach(function(el){
    el.addEventListener('click', function(){ el.classList.toggle('shown'); });
  });
}

function evalPasswordLocal(val) {
  var s = 0;
  var len = val.length;
  if (len>=8)  s+=10;
  if (len>=12) s+=15;
  if (len>=16) s+=10;
  if (len>=20) s+=5;
  if (/[A-Z]/.test(val))        s+=10;
  if (/[a-z]/.test(val))        s+=10;
  if (/[0-9]/.test(val))        s+=10;
  if (/[^\w\s]/.test(val))      s+=15;
  if (/\s/.test(val))           s+=5;
  if (!/(.)\1{2,}/.test(val))   s+=5;
  if (!/password|1234|qwerty|admin|scuola|letmein|abc123/i.test(val)) s+=5;
  s = Math.min(s,100);
  document.getElementById('scoreBar').style.width = s+'%';
  document.getElementById('scoreText').textContent = 'Punteggio: '+s+' / 100 - '+scoreLabel(s);
  var criteria = [
    ['Almeno 8 caratteri',      len>=8],
    ['Almeno 12 caratteri',     len>=12],
    ['Almeno 16 caratteri',     len>=16],
    ['Almeno 20 caratteri',     len>=20],
    ['Lettere maiuscole',       /[A-Z]/.test(val)],
    ['Lettere minuscole',       /[a-z]/.test(val)],
    ['Numeri',                  /[0-9]/.test(val)],
    ['Simboli speciali',        /[^\w\s]/.test(val)],
    ['Spazi / passphrase',      /\s/.test(val)],
    ['Nessuna tripletta (aaa)', !/(.)\1{2,}/.test(val)],
    ['No parole deboli',        !/password|1234|qwerty|admin|scuola/i.test(val)],
    ['Lunghezza ottimale 20+',  len>=20]
  ];
  document.getElementById('scoreChecks').innerHTML = criteria.map(function(c){
    return '<div class="check-row '+(c[1]?'pass':'')+'">'
      +(c[1]?'&#10003;':'&bull;')+' '+c[0]
      +'</div>';
  }).join('');
}

// Anonimo toggle
var anonCheck = document.getElementById('anonCheck');
var nameInput = document.getElementById('nameInput');
anonCheck.addEventListener('change', function(){
  nameInput.disabled = anonCheck.checked;
  nameInput.placeholder = anonCheck.checked ? 'Non visibile (anonimo)' : 'Opzionale';
  if(anonCheck.checked) nameInput.value = '';
});

// Report form
document.getElementById('reportForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var fd = Object.fromEntries(new FormData(e.target));
  if (anonCheck.checked) fd.anonymous = '1';
  var r = await api('add_report','POST', fd);
  msgEl(document.getElementById('reportMsg'),
    r.ok ? 'Segnalazione inviata &mdash; codice: <code>'+r.tracking+'</code>' : (r.message||'Errore'),
    r.ok ? 'ok' : 'err'
  );
  if(r.ok){ e.target.reset(); anonCheck.checked=false; nameInput.disabled=false; loadReports(); }
});

// Score input
document.getElementById('scoreInput').addEventListener('input', function(e){
  evalPasswordLocal(e.target.value);
});

// Genera casuale
document.getElementById('genRandom').addEventListener('click', async function(){
  var r = await api('generate_password');
  if(r.ok){
    document.getElementById('generatedPw').value = r.password;
    document.getElementById('genMsg').textContent = 'Password casuale generata - clicca Copia';
    evalPasswordLocal(r.password);
  }
});

// Genera passphrase
document.getElementById('genPhrase').addEventListener('click', async function(){
  var r = await fetch('api.php?action=generate_password&mode=phrase').then(function(x){return x.json();});
  if(r.ok){
    document.getElementById('generatedPw').value = r.password;
    document.getElementById('genMsg').textContent = 'Passphrase generata - facile da ricordare';
    evalPasswordLocal(r.password);
  }
});

// Copia password
document.getElementById('copyPw').addEventListener('click', function(){
  var val = document.getElementById('generatedPw').value;
  if(val) navigator.clipboard.writeText(val).then(function(){
    document.getElementById('genMsg').textContent = 'Copiata negli appunti!';
  });
});

// Auth modal
document.getElementById('authBtn').addEventListener('click', function(){ document.getElementById('authModal').classList.add('open'); });
document.getElementById('closeAuth').addEventListener('click', function(){ document.getElementById('authModal').classList.remove('open'); });

// Login
document.getElementById('loginForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var r = await api('login','POST',Object.fromEntries(new FormData(e.target)));
  msgEl(document.getElementById('authMsg'), r.ok?'Login effettuato!':(r.message||'Errore'), r.ok?'ok':'err');
  if(r.ok){ document.getElementById('authModal').classList.remove('open'); loadSession(); }
});

// Register
document.getElementById('registerForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var r = await api('register','POST',Object.fromEntries(new FormData(e.target)));
  msgEl(document.getElementById('authMsg'), r.ok?'Registrazione completata!':(r.message||'Errore'), r.ok?'ok':'err');
  if(r.ok){ document.getElementById('authModal').classList.remove('open'); loadSession(); }
});

// Logout
document.getElementById('logoutBtn').addEventListener('click', async function(){
  await api('logout'); loadSession();
});

// Vault form
document.getElementById('vaultForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var r = await api('save_password','POST',Object.fromEntries(new FormData(e.target)));
  if(r.ok){ e.target.reset(); loadVault(); }
});

// Tema toggle
document.getElementById('themeBtn').addEventListener('click', function(){
  var cur = document.documentElement.getAttribute('data-theme');
  document.documentElement.setAttribute('data-theme', cur==='dark' ? 'light' : 'dark');
});

renderQuick();
renderAdv();
loadReports();
loadSession();
evalPasswordLocal('');
</script>
</body>
</html>
