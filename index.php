<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="it" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SafeSchool Hub</title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&f[]=cabinet-grotesk@700,800&display=swap" rel="stylesheet">
<style>
:root,[data-theme="light"]{
--bg:#f6f5f1;--surface:#fbfaf7;--surface2:#efede7;--border:#d1ccc3;
--text:#231f19;--muted:#716d66;--faint:#b5b0a8;
--primary:#0c6b73;--primary-hover:#08494f;
--ok:#437a22;--warn:#c97a00;--danger:#a12c7b;
--shadow:0 8px 28px rgba(24,22,18,.09);--radius:20px;
}
[data-theme="dark"]{
--bg:#0d1014;--surface:#151a20;--surface2:#1c2330;--border:#28303d;
--text:#dde4ee;--muted:#7b8fa3;--faint:#3d4e5e;
--primary:#56b7c2;--primary-hover:#3a9aa5;
--ok:#7fd05b;--warn:#ffb14a;--danger:#ff77b7;
--shadow:0 18px 48px rgba(0,0,0,.38);
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Satoshi,system-ui,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;transition:background .25s,color .25s}
[data-theme="dark"] body{background:radial-gradient(ellipse at 80% 0%,rgba(86,183,194,.08) 0%,transparent 40%),var(--bg)}
h1,h2,h3,h4{font-family:'Cabinet Grotesk',sans-serif;line-height:1.1}
button,input,textarea,select{font:inherit;color:inherit}
a{color:var(--primary)}
.wrap{max-width:1280px;margin:0 auto;padding:28px 20px}
.topbar{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:32px;flex-wrap:wrap}
.brand{display:flex;align-items:center;gap:14px}
.logo{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--primary),#7be1ec);display:grid;place-items:center;font-family:'Cabinet Grotesk',sans-serif;font-weight:800;font-size:1rem;color:#071214;flex-shrink:0}
.actions{display:flex;gap:10px;flex-wrap:wrap}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow)}
.p{padding:24px}
.hero-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:20px;margin-bottom:32px}
.hero h1{font-size:clamp(2rem,3.5vw,3.8rem);margin-bottom:14px}
.sub{color:var(--muted);line-height:1.7;max-width:72ch}
.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:22px}
.kpi{background:var(--surface2);border:1px solid var(--border);border-radius:16px;padding:16px}
.kpi .num{font-size:2rem;font-weight:800;font-family:'Cabinet Grotesk',sans-serif;margin-top:6px;color:var(--primary)}
.btn{border:0;border-radius:12px;padding:11px 18px;font-weight:700;cursor:pointer;transition:background .18s,transform .12s}
.btn:active{transform:scale(.97)}
.btn-primary{background:var(--primary);color:#071214}
.btn-primary:hover{background:var(--primary-hover)}
.btn-ghost{background:transparent;border:1px solid var(--border);color:var(--text)}
.btn-ghost:hover{background:var(--surface2)}
.btn-danger{background:rgba(255,119,183,.12);color:var(--danger);border:1px solid rgba(255,119,183,.2)}
.btn-sm{padding:7px 13px;font-size:.88rem;border-radius:10px}
.input{width:100%;padding:12px 14px;border-radius:13px;border:1px solid var(--border);background:rgba(255,255,255,.03);color:var(--text);transition:border-color .18s}
[data-theme="dark"] .input{background:rgba(255,255,255,.04)}
.input:focus{outline:none;border-color:var(--primary)}
.label{font-size:.82rem;font-weight:700;color:var(--muted);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;display:block}
form{display:grid;gap:14px}
.field{display:grid;gap:4px}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.badge{display:inline-flex;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:700}
.alta{background:rgba(255,119,183,.15);color:var(--danger)}
.media{background:rgba(255,177,74,.12);color:var(--warn)}
.bassa{background:rgba(127,208,91,.12);color:var(--ok)}
.st{display:flex;justify-content:space-between;align-items:center;margin:32px 0 16px}
.st h2{font-size:clamp(1.3rem,2vw,1.8rem)}
.main-grid{display:grid;grid-template-columns:1.2fr .9fr;gap:20px}
.tools-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.stack{display:grid;gap:16px}
.list{display:grid;gap:12px}
.item{padding:16px;border:1px solid var(--border);border-radius:16px;background:rgba(255,255,255,.01)}
.score-wrap{background:var(--surface2);border-radius:999px;height:10px;overflow:hidden}
.score-bar{height:100%;width:0%;background:linear-gradient(90deg,var(--danger),var(--warn) 50%,var(--ok));border-radius:999px;transition:width .4s}
.vault-card{padding:16px;border:1px solid var(--border);border-radius:16px;background:var(--surface2);display:grid;gap:8px}
.vault-header{display:flex;justify-content:space-between;align-items:center;gap:8px}
.pw-field{display:flex;gap:8px;align-items:center}
.pw-text{font-family:monospace;font-size:.92rem;background:var(--surface);padding:6px 12px;border-radius:10px;flex:1;word-break:break-all;filter:blur(4px);transition:filter .2s;cursor:pointer;border:1px solid var(--border)}
.pw-text:hover,.pw-text.shown{filter:none}
.check-row{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:13px;border:1px solid var(--border);font-size:.9rem}
.check-row.pass{border-color:rgba(127,208,91,.3);background:rgba(127,208,91,.05)}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;padding:20px;z-index:100}
.modal-overlay.open{display:flex}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);max-width:580px;width:100%;padding:28px}
.muted{color:var(--muted)}
.tiny{font-size:.85rem}
.hidden{display:none}
.msg{padding:10px 14px;border-radius:12px;font-size:.9rem;margin-top:8px}
.msg.ok{background:rgba(127,208,91,.12);color:var(--ok)}
.msg.err{background:rgba(255,119,183,.12);color:var(--danger)}
@media(max-width:900px){.hero-grid,.main-grid,.tools-grid,.kpis,.row2{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
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

  <div class="hero-grid">
    <div class="card p">
      <h1>Una piattaforma scolastica sicura, utile anche senza login.</h1>
      <p class="sub">Chiunque pu&ograve; inviare segnalazioni e usare gli strumenti pubblici. Chi crea un account ottiene in pi&ugrave; un mini <strong>password manager scolastico</strong> personale sincronizzato tramite database MySQL.</p>
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

  <div class="st"><h2>Segnalazioni pubbliche</h2><span class="muted tiny">Accessibili senza login</span></div>
  <div class="main-grid">
    <div class="card p">
      <form id="reportForm">
        <div class="row2">
          <div class="field"><label class="label">Nome / Classe</label><input class="input" name="name" placeholder="Opzionale"></div>
          <div class="field"><label class="label">Categoria</label><select class="input" name="category"><option>Problema tecnico</option><option>Privacy / Dati personali</option><option>Sicurezza rete scolastica</option><option>Materiali didattici</option><option>Altro</option></select></div>
        </div>
        <div class="field"><label class="label">Titolo *</label><input class="input" name="title" placeholder="Descrivi brevemente il problema" required></div>
        <div class="field"><label class="label">Descrizione *</label><textarea class="input" name="description" rows="4" placeholder="Dettagli, quando &egrave; successo, dove&hellip;" required></textarea></div>
        <div class="row2">
          <div class="field"><label class="label">Priorit&agrave;</label><select class="input" name="priority"><option>Media</option><option>Alta</option><option>Bassa</option></select></div>
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

  <div class="st"><h2>Strumenti sicurezza</h2><span class="muted tiny">Pubblici &mdash; nessun login richiesto</span></div>
  <div class="tools-grid">
    <div class="stack">
      <div class="card p">
        <h3>Calcolatore sicurezza password</h3>
        <p class="sub tiny" style="margin:8px 0 16px">Valuta 12 criteri: lunghezza, variet&agrave; caratteri, passphrase, ripetizioni e parole deboli.</p>
        <div class="field"><label class="label">Inserisci o incolla una password</label><input class="input" id="scoreInput" type="password" placeholder="La password non viene salvata"></div>
        <div style="height:14px"></div>
        <div class="score-wrap"><div class="score-bar" id="scoreBar"></div></div>
        <div style="height:8px"></div>
        <div id="scoreText" class="muted tiny">Punteggio: 0 / 100</div>
        <div style="height:16px"></div>
        <div id="scoreChecks" class="list"></div>
      </div>
      <div class="card p">
        <h3>Generatore password sicure</h3>
        <div style="height:12px"></div>
        <div class="row2">
          <button class="btn btn-primary" id="genRandom">Genera casuale</button>
          <button class="btn btn-ghost" id="genPhrase">Genera passphrase</button>
        </div>
        <div style="height:12px"></div>
        <div class="pw-field">
          <input class="input" id="generatedPw" readonly placeholder="La password apparir&agrave; qui">
          <button class="btn btn-ghost btn-sm" id="copyPw">Copia</button>
        </div>
        <div id="genMsg" class="muted tiny" style="margin-top:8px"></div>
      </div>
    </div>
    <div class="stack">
      <div class="card p">
        <h3>Checklist sicurezza avanzata</h3>
        <div style="height:12px"></div>
        <div id="advChecklist" class="list"></div>
      </div>
      <div class="card p">
        <h3>Password Manager scolastico</h3>
        <p class="sub tiny" style="margin:8px 0 14px">Salva credenziali di Moodle, Registro, GitHub e altri servizi scolastici. Solo per utenti autenticati.</p>
        <div id="vaultHint" class="muted tiny">Accedi per salvare le tue credenziali nel vault personale.</div>
        <form id="vaultForm" class="hidden">
          <div class="field"><label class="label">Servizio</label><input class="input" name="site_name" placeholder="es. Moodle, Registro, GitHub, Drive&hellip;"></div>
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
        <div class="field"><label class="label">Password</label><input class="input" name="password" type="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;"></div>
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
const QUICK=['Usa almeno 12 caratteri','Attiva 2FA sulla mail scolastica','Non condividere password in chat','Fai logout dai PC condivisi'];
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
async function api(action,method='GET',data=null){
  return fetch('api.php?action='+action,{method,headers:{'Content-Type':'application/json'},body:data?JSON.stringify(data):null}).then(r=>r.json());
}
function msgEl(el,text,type){el.innerHTML=`<div class="msg ${type}">${text}</div>`;}
function badgeCls(p){return p==='Alta'?'alta':(p==='Bassa'?'bassa':'media');}
function scoreLabel(s){return s<40?'Debole':s<60?'Media':s<80?'Buona':'Ottima';}
function renderQuick(){document.getElementById('quickList').innerHTML=QUICK.map(c=>`<div class="check-row pass tiny">&bull; ${c}</div>`).join('');}
function renderAdv(){document.getElementById('advChecklist').innerHTML=ADV.map(c=>`<div class="check-row tiny">&bull; ${c}</div>`).join('');}
async function loadReports(){
  const r=await api('reports');
  const list=r.reports||[];
  document.getElementById('kpiReports').textContent=list.length;
  document.getElementById('reportsList').innerHTML=list.length?list.map(x=>`
    <div class="item">
      <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap">
        <b>${x.title}</b><span class="badge ${badgeCls(x.priority)}">${x.priority}</span>
      </div>
      <div class="muted tiny" style="margin-top:4px">${x.category} &bull; ${x.name} &bull; <code>${x.tracking_code}</code></div>
      <div class="tiny" style="margin-top:8px">${x.description}</div>
    </div>`).join(''):'<div class="item tiny muted">Nessuna segnalazione ancora.</div>';
}
async function loadSession(){
  const s=await api('session');
  const user=s.user;
  document.getElementById('authBtn').classList.toggle('hidden',!!user);
  document.getElementById('logoutBtn').classList.toggle('hidden',!user);
  document.getElementById('sessionBox').innerHTML=user
    ?`Connesso come <strong>${user.name}</strong> (${user.email}). Hai accesso al password manager personale.`
    :'Modalita ospite &mdash; puoi usare segnalazioni, generatore password e calcolatore sicurezza.';
  document.getElementById('vaultForm').classList.toggle('hidden',!user);
  document.getElementById('vaultHint').classList.toggle('hidden',!!user);
  await loadVault();
}
async function loadVault(){
  const v=await api('vault');
  const items=v.items||[];
  document.getElementById('kpiVault').textContent=items.length;
  document.getElementById('vaultList').innerHTML=items.length
    ?items.map(i=>`<div class="vault-card">
      <div class="vault-header"><b>${i.site_name}</b><button class="btn btn-danger btn-sm" data-id="${i.id}">Elimina</button></div>
      <div class="muted tiny">${i.username}</div>
      <div class="pw-field"><span class="pw-text" title="Clicca per mostrare">${i.password_plain}</span></div>
      ${i.notes?`<div class="tiny">${i.notes}</div>`:''}
    </div>`).join('')
    :'<div class="item tiny muted">Nessuna credenziale nel vault.</div>';
  document.querySelectorAll('[data-id]').forEach(btn=>btn.addEventListener('click',async()=>{await api('delete_vault','POST',{id:btn.dataset.id});loadVault();}));
  document.querySelectorAll('.pw-text').forEach(el=>el.addEventListener('click',()=>el.classList.toggle('shown')));
}
async function evalPassword(val){
  const r=await api('security_score','POST',{password:val});
  const s=r.score||0;
  document.getElementById('scoreBar').style.width=s+'%';
  document.getElementById('scoreText').textContent=`Punteggio: ${s} / 100 — ${scoreLabel(s)}`;
  const criteria=[
    ['Almeno 8 caratteri',     val.length>=8],
    ['Almeno 12 caratteri',    val.length>=12],
    ['Almeno 16 caratteri',    val.length>=16],
    ['Almeno 20 caratteri',    val.length>=20],
    ['Lettere maiuscole',      /[A-Z]/.test(val)],
    ['Lettere minuscole',      /[a-z]/.test(val)],
    ['Numeri',                 /[0-9]/.test(val)],
    ['Simboli speciali',       /[^\w\s]/.test(val)],
    ['Spazi o passphrase',     /\s/.test(val)],
    ['Nessuna tripletta (aaa)',!/(.)(\1{2,})/.test(val)],
    ['No parole deboli',       !/password|1234|qwerty|admin|scuola|letmein|abc123/i.test(val)],
    ['Lunghezza ottimale 20+', val.length>=20],
  ];
  document.getElementById('scoreChecks').innerHTML=criteria.map(([t,ok])=>`
    <div class="check-row ${ok?'pass':'fail'}">${ok?'&#10003;':'&bull;'} ${t}</div>`).join('');
}
document.getElementById('reportForm').addEventListener('submit',async e=>{
  e.preventDefault();
  const r=await api('add_report','POST',Object.fromEntries(new FormData(e.target)));
  msgEl(document.getElementById('reportMsg'),r.ok?`Segnalazione inviata &mdash; codice: <code>${r.tracking}</code>`:r.message||'Errore',r.ok?'ok':'err');
  if(r.ok){e.target.reset();loadReports();}
});
document.getElementById('scoreInput').addEventListener('input',e=>evalPassword(e.target.value));
document.getElementById('genRandom').addEventListener('click',async()=>{
  const r=await api('generate_password');
  document.getElementById('generatedPw').value=r.password;
  document.getElementById('genMsg').textContent='Password casuale generata — clicca Copia';
  evalPassword(r.password);
});
document.getElementById('genPhrase').addEventListener('click',async()=>{
  const r=await fetch('api.php?action=generate_password&mode=phrase').then(x=>x.json());
  document.getElementById('generatedPw').value=r.password;
  document.getElementById('genMsg').textContent='Passphrase generata — facile da ricordare';
  evalPassword(r.password);
});
document.getElementById('copyPw').addEventListener('click',()=>{
  const val=document.getElementById('generatedPw').value;
  if(val)navigator.clipboard.writeText(val).then(()=>document.getElementById('genMsg').textContent='Copiata negli appunti!');
});
document.getElementById('authBtn').addEventListener('click',()=>document.getElementById('authModal').classList.add('open'));
document.getElementById('closeAuth').addEventListener('click',()=>document.getElementById('authModal').classList.remove('open'));
document.getElementById('loginForm').addEventListener('submit',async e=>{
  e.preventDefault();
  const r=await api('login','POST',Object.fromEntries(new FormData(e.target)));
  msgEl(document.getElementById('authMsg'),r.ok?'Login effettuato!':r.message||'Errore',r.ok?'ok':'err');
  if(r.ok){document.getElementById('authModal').classList.remove('open');loadSession();}
});
document.getElementById('registerForm').addEventListener('submit',async e=>{
  e.preventDefault();
  const r=await api('register','POST',Object.fromEntries(new FormData(e.target)));
  msgEl(document.getElementById('authMsg'),r.ok?'Registrazione completata!':r.message||'Errore',r.ok?'ok':'err');
  if(r.ok){document.getElementById('authModal').classList.remove('open');loadSession();}
});
document.getElementById('logoutBtn').addEventListener('click',async()=>{await api('logout');loadSession();});
document.getElementById('vaultForm').addEventListener('submit',async e=>{
  e.preventDefault();
  const r=await api('save_password','POST',Object.fromEntries(new FormData(e.target)));
  if(r.ok){e.target.reset();loadVault();}
});
document.getElementById('themeBtn').addEventListener('click',()=>{
  const cur=document.documentElement.getAttribute('data-theme');
  document.documentElement.setAttribute('data-theme',cur==='dark'?'light':'dark');
});
renderQuick();
renderAdv();
loadReports();
loadSession();
evalPassword('');
</script>
</body>
</html>
