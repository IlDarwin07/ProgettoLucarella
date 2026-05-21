<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accedi &mdash; SafeSchool Hub</title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&f[]=cabinet-grotesk@700,800&display=swap" rel="stylesheet">
<style>
:root,[data-theme="light"]{
  --bg:#faf9f4;--surface:#ffffff;--surface2:#f3f1e8;--border:#e0dcd0;
  --text:#1e1b14;--muted:#7a7263;--faint:#c0bab0;
  --primary:#c49a00;--primary-hover:#a07e00;--primary-fg:#1e1b14;
  --ok:#437a22;--danger:#a12c7b;
  --shadow:0 2px 12px rgba(30,27,20,.07),0 1px 3px rgba(30,27,20,.05);
  --radius:16px;--radius-sm:10px;
}
[data-theme="dark"]{
  --bg:#0f0e0a;--surface:#181610;--surface2:#201e17;--border:#2e2b22;
  --text:#f0ead8;--muted:#9c9480;--faint:#4a4638;
  --primary:#f5c800;--primary-hover:#e0b400;--primary-fg:#0f0e0a;
  --ok:#7fd05b;--danger:#ff77b7;
  --shadow:0 2px 12px rgba(0,0,0,.25),0 1px 3px rgba(0,0,0,.2);
}
*{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased}
body{font-family:Satoshi,system-ui,sans-serif;background:var(--bg);color:var(--text);
  min-height:100vh;display:flex;flex-direction:column;align-items:center;
  justify-content:center;padding:24px;transition:background .25s,color .25s}
h1,h2,h3{font-family:'Cabinet Grotesk',sans-serif;line-height:1.15}
button,input{font:inherit;color:inherit}
a{color:var(--primary);text-decoration:none;font-weight:600}

.card{
  background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
  box-shadow:var(--shadow);width:100%;max-width:420px;padding:36px 32px;
}
.logo-area{
  display:flex;align-items:center;gap:12px;margin-bottom:28px;
}
.logo-area h1{font-size:1.25rem}
.logo-area span{font-size:.78rem;color:var(--muted);display:block}
svg.ic{stroke:currentColor;fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round}

form{display:grid;gap:14px}
.field{display:grid;gap:5px}
.label{font-size:.78rem;font-weight:700;color:var(--muted);letter-spacing:.05em;text-transform:uppercase}
.input{width:100%;padding:11px 14px;border-radius:var(--radius-sm);border:1px solid var(--border);
  background:var(--surface2);color:var(--text);transition:border-color .18s;font-size:.95rem}
.input:focus{outline:none;border-color:var(--primary);background:var(--surface)}
.btn{border:0;border-radius:var(--radius-sm);padding:12px 20px;font-weight:700;cursor:pointer;
  transition:background .18s,transform .12s;font-size:.95rem;display:inline-flex;align-items:center;
  justify-content:center;gap:8px;width:100%}
.btn:active{transform:scale(.97)}
.btn-primary{background:var(--primary);color:var(--primary-fg)}
.btn-primary:hover{background:var(--primary-hover)}
.msg{padding:10px 14px;border-radius:var(--radius-sm);font-size:.9rem;margin-top:4px}
.msg.ok{background:rgba(67,122,34,.1);color:var(--ok)}
.msg.err{background:rgba(161,44,123,.1);color:var(--danger)}
.divider{height:1px;background:var(--border);margin:20px 0}
.center{text-align:center;font-size:.88rem;color:var(--muted)}
.theme-btn{position:fixed;top:16px;right:16px;background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-sm);padding:8px 12px;cursor:pointer;display:flex;align-items:center;gap:6px;
  font-size:.82rem;font-weight:600;color:var(--muted);transition:background .18s}
.theme-btn:hover{background:var(--surface2)}
</style>
</head>
<body>
<button class="theme-btn" id="themeBtn" aria-label="Cambia tema">
  <svg class="ic" width="16" height="16" viewBox="0 0 24 24" id="themeIcon">
    <circle cx="12" cy="12" r="5"/>
    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
  </svg>
  Tema
</button>

<div class="card">
  <div class="logo-area">
    <svg viewBox="0 0 44 44" fill="none" width="44" height="44">
      <path d="M22 3L5 10V22C5 31.5 12.5 39.5 22 42C31.5 39.5 39 31.5 39 22V10L22 3Z"
            fill="var(--primary)" opacity=".15" stroke="var(--primary)" stroke-width="1.5"/>
      <circle cx="22" cy="22" r="2.5" fill="var(--primary)"/>
      <ellipse cx="22" cy="22" rx="10" ry="4" stroke="var(--primary)" stroke-width="1.3" fill="none"/>
      <ellipse cx="22" cy="22" rx="10" ry="4" stroke="var(--primary)" stroke-width="1.3" fill="none" transform="rotate(60 22 22)"/>
      <ellipse cx="22" cy="22" rx="10" ry="4" stroke="var(--primary)" stroke-width="1.3" fill="none" transform="rotate(120 22 22)"/>
      <text x="22" y="12" text-anchor="middle" font-family="Cabinet Grotesk,sans-serif" font-weight="800" font-size="7" fill="var(--primary)">F</text>
    </svg>
    <div>
      <h1>SafeSchool Hub</h1>
      <span>ITT E. Fermi &mdash; Francavilla Fontana</span>
    </div>
  </div>

  <h2 style="font-size:1.4rem;margin-bottom:6px">Accedi al tuo account</h2>
  <p style="font-size:.88rem;color:var(--muted);margin-bottom:24px">Inserisci le tue credenziali scolastiche.</p>

  <form id="loginForm">
    <div class="field">
      <label class="label" for="email">Email</label>
      <input class="input" id="email" name="email" type="email" placeholder="email@scuola.it" required autocomplete="email">
    </div>
    <div class="field">
      <label class="label" for="password">Password</label>
      <input class="input" id="password" name="password" type="password" placeholder="La tua password" required autocomplete="current-password">
    </div>
    <div id="loginMsg"></div>
    <button class="btn btn-primary" type="submit">
      <svg class="ic" width="16" height="16" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
      Accedi
    </button>
  </form>

  <div class="divider"></div>
  <p class="center">
    Non hai un account?
    <a href="register.php">Registrati</a>
  </p>
  <p class="center" style="margin-top:10px">
    <a href="index.php">&larr; Torna alla Home</a>
  </p>
</div>

<script>
var themeBtn=document.getElementById('themeBtn');
var themeIcon=document.getElementById('themeIcon');
var html=document.documentElement;
var dark=window.matchMedia('(prefers-color-scheme:dark)').matches;
html.setAttribute('data-theme',dark?'dark':'light');
if(dark) themeIcon.innerHTML='<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
themeBtn.addEventListener('click',function(){
  var t=html.getAttribute('data-theme')==='dark'?'light':'dark';
  html.setAttribute('data-theme',t);
  themeIcon.innerHTML=t==='dark'?'<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>':'<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
});

document.getElementById('loginForm').addEventListener('submit',async function(e){
  e.preventDefault();
  var btn=this.querySelector('button[type=submit]');
  btn.disabled=true;btn.textContent='Accesso in corso...';
  var data=Object.fromEntries(new FormData(e.target));
  try{
    var r=await fetch('api.php?action=login',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(data)
    }).then(function(x){return x.json();});
    if(r.ok){
      var msg=document.getElementById('loginMsg');
      msg.innerHTML='<div class="msg ok">Accesso effettuato! Reindirizzamento...</div>';
      setTimeout(function(){window.location.href='index.php';},800);
    } else {
      document.getElementById('loginMsg').innerHTML='<div class="msg err">'+(r.message||'Errore')+'</div>';
      btn.disabled=false;btn.innerHTML='<svg class="ic" width="16" height="16" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg> Accedi';
    }
  } catch(err){
    document.getElementById('loginMsg').innerHTML='<div class="msg err">Errore di connessione</div>';
    btn.disabled=false;btn.textContent='Accedi';
  }
});
</script>
</body>
</html>
