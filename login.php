<?php
require_once __DIR__ . '/includes/auth.php';
if(!empty($_SESSION['user'])) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Accedi — SafeSchool Hub</title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f7f6f2;--surface:#ffffff;--border:#dcd9d5;--text:#28251d;--muted:#7a7974;
  --primary:#01696f;--primary-h:#0c4e54;--error:#a12c7b;
  --radius:0.5rem;--shadow:0 4px 24px rgba(0,0,0,.08);
  --font:'Satoshi',system-ui,sans-serif;
}
[data-theme="dark"]{
  --bg:#171614;--surface:#1c1b19;--border:#393836;--text:#cdccca;--muted:#797876;
  --primary:#4f98a3;--primary-h:#227f8b;--error:#d163a7;
  --shadow:0 4px 24px rgba(0,0,0,.35);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased}
body{min-height:100dvh;background:var(--bg);color:var(--text);font-family:var(--font);
  display:grid;place-items:center;padding:1rem}
.card{background:var(--surface);border:1px solid var(--border);border-radius:calc(var(--radius)*1.5);
  box-shadow:var(--shadow);width:100%;max-width:400px;padding:2rem 2rem 1.75rem}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:1.75rem;text-decoration:none;color:var(--text)}
.logo svg{color:var(--primary)}
.logo span{font-weight:700;font-size:1.1rem}
h1{font-size:1.35rem;font-weight:700;margin-bottom:.3rem}
.subtitle{font-size:.88rem;color:var(--muted);margin-bottom:1.5rem}
.field{display:flex;flex-direction:column;gap:.35rem;margin-bottom:1rem}
label{font-size:.82rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em}
input{padding:.6rem .75rem;border:1px solid var(--border);border-radius:var(--radius);
  background:var(--bg);color:var(--text);font-size:.95rem;font-family:inherit;
  transition:border .18s,box-shadow .18s}
input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px color-mix(in oklab,var(--primary) 18%,transparent)}
.btn{display:flex;align-items:center;justify-content:center;gap:7px;
  padding:.65rem 1.25rem;border-radius:var(--radius);font-size:.9rem;font-weight:600;
  cursor:pointer;border:none;font-family:inherit;transition:background .18s,opacity .18s;width:100%;margin-top:.5rem}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-h)}
.btn-primary:disabled{opacity:.6;cursor:not-allowed}
.divider{border:none;border-top:1px solid var(--border);margin:1.5rem 0}
.register-link{text-align:center;font-size:.88rem;color:var(--muted)}
.register-link a{color:var(--primary);font-weight:600;text-decoration:none}
.register-link a:hover{text-decoration:underline}
.msg{padding:.65rem .9rem;border-radius:var(--radius);font-size:.85rem;margin-top:.75rem}
.msg.err{background:color-mix(in oklab,var(--error) 12%,transparent);color:var(--error)}
.msg.ok{background:color-mix(in oklab,var(--primary) 12%,transparent);color:var(--primary)}
.theme-btn{position:fixed;top:1rem;right:1rem;background:var(--surface);border:1px solid var(--border);
  border-radius:50%;width:38px;height:38px;display:grid;place-items:center;cursor:pointer;
  color:var(--muted);transition:background .18s}
.theme-btn:hover{background:var(--border)}
</style>
</head>
<body>

<button class="theme-btn" id="themeBtn" aria-label="Cambia tema">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="themeIcon">
    <circle cx="12" cy="12" r="5"/>
    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
  </svg>
</button>

<div class="card">
  <a href="login.php" class="logo">
    <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
      <rect x="2" y="2" width="28" height="28" rx="7" fill="var(--primary)" opacity=".12"/>
      <path d="M16 6 L26 10 L26 18 C26 23 21 27 16 29 C11 27 6 23 6 18 L6 10 Z" fill="var(--primary)" opacity=".25"/>
      <path d="M16 8 L24 11.5 L24 18 C24 22 20 25.5 16 27.5 C12 25.5 8 22 8 18 L8 11.5 Z" stroke="var(--primary)" stroke-width="1.5" fill="none"/>
      <path d="M12 16 L15 19 L20 13" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span>SafeSchool Hub</span>
  </a>

  <h1>Bentornato!</h1>
  <p class="subtitle">Inserisci le credenziali per accedere al tuo account.</p>

  <form id="loginForm">
    <div class="field">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" placeholder="email@scuola.it" required autocomplete="email">
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" placeholder="La tua password" required autocomplete="current-password">
    </div>
    <button class="btn btn-primary" type="submit" id="submitBtn">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
        <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
      </svg>
      Accedi
    </button>
    <div id="msg"></div>
  </form>

  <hr class="divider">
  <p class="register-link">Non hai un account? <a href="register.php">Registrati!</a></p>
</div>

<script>
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

document.getElementById('loginForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var btn = document.getElementById('submitBtn');
  var msg = document.getElementById('msg');
  btn.disabled = true;
  btn.textContent = 'Accesso in corso…';
  try {
    var r = await fetch('api.php?action=login', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        email:    document.getElementById('email').value,
        password: document.getElementById('password').value
      })
    }).then(function(x){ return x.json(); });

    if(r.ok){
      msg.innerHTML = '<div class="msg ok">Accesso effettuato! Reindirizzamento…</div>';
      setTimeout(function(){ window.location.href = 'index.php'; }, 600);
    } else {
      msg.innerHTML = '<div class="msg err">' + (r.message || 'Credenziali non valide') + '</div>';
      btn.disabled = false;
      btn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg> Accedi';
    }
  } catch(err){
    msg.innerHTML = '<div class="msg err">Errore di rete. Riprova.</div>';
    btn.disabled = false;
    btn.textContent = 'Accedi';
  }
});
</script>
</body>
</html>
