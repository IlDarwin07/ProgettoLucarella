<?php
require_once __DIR__ . '/includes/auth.php';

// Se non loggato → login
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Se ospite → guest.php
if (($_SESSION['user']['role'] ?? '') === 'guest') {
    header('Location: guest.php');
    exit;
}

// Utente loggato: mostra dashboard
$user = current_user();
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — SafeSchool Hub</title>
<link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f7f6f2;--surface:#ffffff;--border:#dcd9d5;--text:#28251d;--muted:#7a7974;
  --primary:#01696f;--primary-h:#0c4e54;--error:#a12c7b;--success:#437a22;
  --radius:0.5rem;--shadow:0 4px 24px rgba(0,0,0,.08);
  --font:'Satoshi',system-ui,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased}
body{min-height:100dvh;background:var(--bg);color:var(--text);font-family:var(--font)}
.navbar{background:var(--surface);border-bottom:1px solid var(--border);padding:.75rem 1.5rem;
  display:flex;align-items:center;justify-content:space-between;gap:1rem}
.logo{display:flex;align-items:center;gap:8px;font-weight:700;font-size:1rem;color:var(--text);text-decoration:none}
.logo svg{color:var(--primary)}
.nav-right{display:flex;align-items:center;gap:.75rem}
.btn{display:inline-flex;align-items:center;gap:6px;padding:.5rem 1rem;border-radius:var(--radius);
  font-size:.88rem;font-weight:600;cursor:pointer;border:none;font-family:inherit;transition:background .18s}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-h)}
.btn-ghost{background:transparent;color:var(--muted);border:1px solid var(--border)}
.btn-ghost:hover{background:var(--border);color:var(--text)}
.main{max-width:960px;margin:0 auto;padding:2rem 1.5rem}
.welcome{margin-bottom:2rem}
.welcome h1{font-size:1.5rem;font-weight:700;margin-bottom:.3rem}
.welcome p{color:var(--muted);font-size:.95rem}
.cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem}
.card{background:var(--surface);border:1px solid var(--border);border-radius:calc(var(--radius)*1.5);
  box-shadow:var(--shadow);padding:1.5rem}
.card-icon{width:40px;height:40px;border-radius:var(--radius);background:color-mix(in oklab,var(--primary) 12%,transparent);
  display:grid;place-items:center;color:var(--primary);margin-bottom:1rem}
.card h2{font-size:1rem;font-weight:700;margin-bottom:.3rem}
.card p{font-size:.88rem;color:var(--muted);line-height:1.5}
.badge{display:inline-flex;align-items:center;gap:4px;padding:.2rem .6rem;border-radius:99px;
  font-size:.75rem;font-weight:600;background:color-mix(in oklab,var(--primary) 12%,transparent);color:var(--primary)}
</style>
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="logo">
    <svg width="24" height="24" viewBox="0 0 32 32" fill="none">
      <path d="M16 6 L26 10 L26 18 C26 23 21 27 16 29 C11 27 6 23 6 18 L6 10 Z" fill="var(--primary)" opacity=".25"/>
      <path d="M16 8 L24 11.5 L24 18 C24 22 20 25.5 16 27.5 C12 25.5 8 22 8 18 L8 11.5 Z" stroke="var(--primary)" stroke-width="1.5" fill="none"/>
      <path d="M12 16 L15 19 L20 13" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    SafeSchool Hub
  </a>
  <div class="nav-right">
    <span class="badge"><?= htmlspecialchars($user['role'] ?? 'user') ?></span>
    <span style="font-size:.88rem;color:var(--muted)"><?= htmlspecialchars($user['name'] ?? $user['email']) ?></span>
    <a href="logout.php" class="btn btn-ghost">Esci</a>
  </div>
</nav>

<main class="main">
  <div class="welcome">
    <h1>Benvenuto, <?= htmlspecialchars($user['name'] ?? 'Utente') ?>!</h1>
    <p>Sei connesso come <strong><?= htmlspecialchars($user['email']) ?></strong></p>
  </div>

  <div class="cards">
    <div class="card">
      <div class="card-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <h2>Segnalazioni</h2>
      <p>Visualizza e gestisci le segnalazioni di cyberbullismo e problemi digitali.</p>
    </div>
    <div class="card">
      <div class="card-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
      </div>
      <h2>Vault Password</h2>
      <p>Gestisci le tue password salvate in modo sicuro e cifrato.</p>
    </div>
    <div class="card">
      <div class="card-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <h2>Sicurezza Digitale</h2>
      <p>Guide su password, phishing, 2FA, privacy e quiz interattivi.</p>
    </div>
    <?php if(($user['role'] ?? '') === 'admin'): ?>
    <div class="card">
      <div class="card-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.07 4.93l-1.41 1.41M5.34 18.66l-1.41 1.41M4.93 4.93l1.41 1.41M18.66 18.66l1.41 1.41M1 12h2M21 12h2M12 1v2M12 21v2"/>
        </svg>
      </div>
      <h2>Pannello Admin</h2>
      <p>Gestione utenti, segnalazioni e configurazione del sistema.</p>
    </div>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
