<?php
require_once __DIR__ . '/includes/auth.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
if (($_SESSION['user']['id'] ?? null) != 1) { header('Location: index.php'); exit; }
$userName = $_SESSION['user']['name'] ?? 'Root';
?>
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Gestione Utenti – SafeSchool Hub</title>
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

.nav{position:sticky;top:0;z-index:200;background:var(--surface);border-bottom:1px solid var(--border)}
.nav-top{height:var(--nav-h);display:flex;align-items:center;padding:0 1.25rem;gap:1rem}
.nav-brand{display:flex;align-items:center;gap:9px;text-decoration:none;color:var(--text);font-weight:700;font-size:.95rem;flex-shrink:0}
.brand-icon{width:30px;height:30px;flex-shrink:0}
.nav-right{display:flex;align-items:center;gap:.5rem;margin-left:auto}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:.45rem 1rem;border-radius:var(--radius);font-size:.82rem;font-weight:600;cursor:pointer;border:1px solid transparent;font-family:inherit;transition:background .15s,border-color .15s,color .15s;text-decoration:none;white-space:nowrap}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-primary:hover{background:var(--primary-hover)}
.btn-ghost{background:transparent;color:var(--text-muted);border-color:var(--border)}
.btn-ghost:hover{background:var(--surface3);color:var(--text)}
.btn-sm{padding:.32rem .7rem;font-size:.78rem}
.btn-danger{background:var(--error-bg);color:var(--error);border-color:var(--error-bg)}
.btn-danger:hover{border-color:var(--error)}
.btn-warn{background:var(--warn-bg);color:var(--warn);border-color:var(--warn-bg)}
.btn-warn:hover{border-color:var(--warn)}
.btn-success{background:var(--success-bg);color:var(--success);border-color:var(--success-bg)}
.btn-success:hover{border-color:var(--success)}

.page-wrap{max-width:1100px;margin:0 auto;padding:1.5rem 1.25rem;display:flex;flex-direction:column;gap:1.25rem}

.page-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.page-title{font-size:1.25rem;font-weight:700}
.page-sub{font-size:.82rem;color:var(--text-muted);margin-top:.15rem}

.search-bar{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap}
.search-input{padding:.42rem .75rem;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface2);color:var(--text);font-size:.85rem;font-family:inherit;min-width:220px;transition:border .15s,box-shadow .15s}
.search-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-bg)}
.filter-select{padding:.42rem .75rem;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface2);color:var(--text);font-size:.85rem;font-family:inherit;cursor:pointer}

.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}

.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.75rem}
.stat-card{background:var(--surface2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:.75rem 1rem}
.stat-label{font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em}
.stat-value{font-size:1.35rem;font-weight:700;margin-top:.1rem}
.stat-note{font-size:.72rem;color:var(--text-faint);margin-top:.05rem}

table{width:100%;border-collapse:collapse}
thead th{font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);padding:.55rem .85rem;border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
tbody tr{border-bottom:1px solid var(--border2);transition:background .12s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--surface2)}
td{padding:.55rem .85rem;font-size:.85rem;vertical-align:middle}
.td-name{font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.td-email{color:var(--text-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.td-actions{display:flex;gap:.35rem;align-items:center;flex-wrap:nowrap}

.role-badge{display:inline-flex;align-items:center;gap:.25rem;font-size:.7rem;font-weight:600;padding:.18rem .55rem;border-radius:99px;text-transform:uppercase;letter-spacing:.04em}
.role-root{background:var(--gold-bg);color:var(--gold)}
.role-admin{background:var(--primary-bg);color:var(--primary)}
.role-user{background:var(--surface3);color:var(--text-muted)}
.role-guest{background:rgba(0,0,0,.04);color:var(--text-faint)}

.modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem}
.modal-bg.open{display:flex}
.modal{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;max-width:480px;width:100%;box-shadow:var(--shadow-md);display:flex;flex-direction:column;gap:1rem}
.modal-title{font-size:1rem;font-weight:700}
.modal-body{font-size:.88rem;color:var(--text-muted)}
.modal-actions{display:flex;gap:.5rem;justify-content:flex-end}

.drawer-bg{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:500;display:none;align-items:flex-start;justify-content:flex-end}
.drawer-bg.open{display:flex}
.drawer{background:var(--surface);border-left:1px solid var(--border);width:min(420px,100vw);height:100dvh;overflow-y:auto;padding:1.5rem;box-shadow:var(--shadow-md);display:flex;flex-direction:column;gap:1rem}
.drawer-header{display:flex;align-items:center;justify-content:space-between}
.drawer-title{font-size:1rem;font-weight:700}
.drawer-close{width:30px;height:30px;border-radius:var(--radius);background:var(--surface3);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.1rem}
.dl{display:grid;grid-template-columns:auto 1fr;gap:.25rem .75rem;font-size:.82rem}
.dl dt{color:var(--text-muted);font-weight:600;white-space:nowrap}
.dl dd{word-break:break-word}

.toast{position:fixed;bottom:1.25rem;right:1.25rem;z-index:999;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:.65rem 1rem;box-shadow:var(--shadow-md);font-size:.85rem;max-width:320px;opacity:0;transform:translateY(6px);transition:opacity .2s,transform .2s;pointer-events:none}
.toast.show{opacity:1;transform:translateY(0)}
.toast-ok{border-left:3px solid var(--success);color:var(--success)}
.toast-err{border-left:3px solid var(--error);color:var(--error)}

.empty-state{display:flex;flex-direction:column;align-items:center;text-align:center;padding:3rem 1.5rem;color:var(--text-muted)}
.empty-icon{font-size:2rem;margin-bottom:.5rem}

@media(max-width:700px){
  thead th:nth-child(n+4){display:none}
  td:nth-child(n+4):not(:last-child){display:none}
}
</style>
</head>
<body>
<header class="nav">
  <div class="nav-top">
    <a class="nav-brand" href="index.php">
      <svg class="brand-icon" viewBox="0 0 32 32" aria-hidden="true">
        <defs><linearGradient id="g1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#016469"/><stop offset="1" stop-color="#0b8793"/></linearGradient></defs>
        <rect x="3" y="5" width="26" height="22" rx="7" fill="url(#g1)"/>
        <path d="M10 15.5h12M10 19.5h7" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>
        <circle cx="11" cy="11" r="1.4" fill="#fff"/>
      </svg>
      <span>SafeSchool Hub</span>
    </a>
    <div class="nav-right">
      <span style="font-size:.8rem;color:var(--text-muted)">Sessione: <strong><?= htmlspecialchars($userName) ?></strong></span>
      <a class="btn btn-ghost btn-sm" href="index.php">Dashboard</a>
      <a class="btn btn-ghost btn-sm" href="logout.php">Esci</a>
    </div>
  </div>
</header>

<main class="page-wrap">

  <div class="page-header">
    <div>
      <div class="page-title">👑 Gestione Utenti</div>
      <div class="page-sub">Pannello riservato all'account root — visualizza, promuovi, degrada ed elimina account</div>
    </div>
    <div class="search-bar">
      <input class="search-input" id="searchInput" type="search" placeholder="Cerca nome o email…" aria-label="Cerca utente">
      <select class="filter-select" id="roleFilter" aria-label="Filtra per ruolo">
        <option value="">Tutti i ruoli</option>
        <option value="admin">Admin</option>
        <option value="user">User</option>
        <option value="guest">Guest</option>
      </select>
      <button class="btn btn-ghost btn-sm" id="refreshBtn" title="Ricarica lista">↻ Aggiorna</button>
    </div>
  </div>

  <div class="stats-row" id="statsRow">
    <div class="stat-card"><div class="stat-label">Totale utenti</div><div class="stat-value" id="stTotal">—</div><div class="stat-note">registrati</div></div>
    <div class="stat-card"><div class="stat-label">Admin</div><div class="stat-value" id="stAdmin">—</div><div class="stat-note">con poteri admin</div></div>
    <div class="stat-card"><div class="stat-label">Utenti standard</div><div class="stat-value" id="stUser">—</div><div class="stat-note">role = user</div></div>
    <div class="stat-card"><div class="stat-label">Guest</div><div class="stat-value" id="stGuest">—</div><div class="stat-note">accesso ospite</div></div>
  </div>

  <div class="card">
    <table id="usersTable" aria-label="Elenco utenti">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Ruolo</th>
          <th>XP</th>
          <th>Rep.</th>
          <th>Vault</th>
          <th>Ultimo accesso</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody id="usersBody">
        <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--text-faint)">Caricamento…</td></tr>
      </tbody>
    </table>
  </div>

</main>

<!-- Drawer dettaglio utente -->
<div class="drawer-bg" id="drawerBg" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
  <div class="drawer" id="drawer">
    <div class="drawer-header">
      <div class="drawer-title" id="drawerTitle">Profilo utente</div>
      <button class="drawer-close" id="drawerClose" aria-label="Chiudi">✕</button>
    </div>
    <div id="drawerContent"></div>
  </div>
</div>

<!-- Modal conferma elimina -->
<div class="modal-bg" id="deleteModalBg" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
  <div class="modal">
    <div class="modal-title" id="deleteModalTitle">⚠️ Elimina utente</div>
    <div class="modal-body" id="deleteModalBody">Sei sicuro? L'operazione è irreversibile: verranno eliminati anche segnalazioni, vault e badge dell'utente.</div>
    <div class="modal-actions">
      <button class="btn btn-ghost btn-sm" id="deleteCancelBtn">Annulla</button>
      <button class="btn btn-danger btn-sm" id="deleteConfirmBtn">Elimina definitivamente</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
(function(){
  let allUsers = [];
  let pendingDeleteId = null;

  const toast = document.getElementById('toast');
  function showToast(msg, type='ok') {
    toast.textContent = msg;
    toast.className = 'toast show toast-' + type;
    setTimeout(() => { toast.className = 'toast'; }, 3200);
  }

  async function loadUsers() {
    document.getElementById('usersBody').innerHTML =
      '<tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--text-faint)">Caricamento…</td></tr>';
    try {
      const res = await fetch('api.php?action=root_users_list');
      const data = await res.json();
      if (!data.ok) { showToast(data.message || 'Errore caricamento', 'err'); return; }
      allUsers = data.users;
      renderStats(allUsers);
      renderTable(allUsers);
    } catch(e) {
      showToast('Errore di rete: ' + e.message, 'err');
    }
  }

  function renderStats(users) {
    document.getElementById('stTotal').textContent = users.length;
    document.getElementById('stAdmin').textContent = users.filter(u=>u.role==='admin').length;
    document.getElementById('stUser').textContent  = users.filter(u=>u.role==='user').length;
    document.getElementById('stGuest').textContent = users.filter(u=>u.role==='guest').length;
  }

  function roleBadge(role, id) {
    if (id == 1) return '<span class="role-badge role-root">👑 root</span>';
    const cls = {'admin':'role-admin','user':'role-user','guest':'role-guest'}[role] || 'role-user';
    return `<span class="role-badge ${cls}">${role}</span>`;
  }

  function renderTable(users) {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const rf = document.getElementById('roleFilter').value;
    const filtered = users.filter(u => {
      const matchQ = !q || (u.name||'').toLowerCase().includes(q) || (u.email||'').toLowerCase().includes(q);
      const matchR = !rf || u.role === rf;
      return matchQ && matchR;
    });
    const tbody = document.getElementById('usersBody');
    if (!filtered.length) {
      tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state"><div class="empty-icon">🔍</div><div>Nessun utente trovato</div></div></td></tr>';
      return;
    }
    tbody.innerHTML = filtered.map(u => {
      const isRoot = u.id == 1;
      const promoteBtn = !isRoot && u.role === 'user'
        ? `<button class="btn btn-success btn-sm" data-action="promote" data-id="${u.id}" title="Promuovi ad admin">⬆ Admin</button>`
        : '';
      const demoteBtn = !isRoot && u.role === 'admin'
        ? `<button class="btn btn-warn btn-sm" data-action="demote" data-id="${u.id}" title="Degrada a user">⬇ User</button>`
        : '';
      const deleteBtn = !isRoot
        ? `<button class="btn btn-danger btn-sm" data-action="delete" data-id="${u.id}" data-name="${escHtml(u.name||u.email||'Utente')}" title="Elimina utente">🗑</button>`
        : '';
      const detailBtn = `<button class="btn btn-ghost btn-sm" data-action="detail" data-id="${u.id}" title="Vedi profilo">👤</button>`;
      return `<tr>
        <td style="color:var(--text-faint);font-size:.78rem">#${u.id}</td>
        <td class="td-name">${escHtml(u.name||'—')}</td>
        <td class="td-email">${escHtml(u.email||'—')}</td>
        <td>${roleBadge(u.role, u.id)}</td>
        <td style="font-variant-numeric:tabular-nums">${u.xp||0}</td>
        <td>${u.report_count||0}</td>
        <td>${u.vault_count||0}</td>
        <td style="color:var(--text-faint);font-size:.8rem">${u.last_login||'—'}</td>
        <td><div class="td-actions">${detailBtn}${promoteBtn}${demoteBtn}${deleteBtn}</div></td>
      </tr>`;
    }).join('');
  }

  function escHtml(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  document.getElementById('usersBody').addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    const action = btn.dataset.action;
    const id = parseInt(btn.dataset.id);
    const user = allUsers.find(u => u.id == id);

    if (action === 'detail') {
      openDrawer(user);
      return;
    }
    if (action === 'delete') {
      pendingDeleteId = id;
      document.getElementById('deleteModalBody').textContent =
        `Eliminare l'utente "${user.name||user.email}" (#${id})? Verranno rimossi anche segnalazioni, vault e badge.`;
      document.getElementById('deleteModalBg').classList.add('open');
      return;
    }
    if (action === 'promote' || action === 'demote') {
      const newRole = action === 'promote' ? 'admin' : 'user';
      btn.disabled = true;
      try {
        const res = await fetch('api.php?action=root_set_role', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify({user_id: id, role: newRole})
        });
        const data = await res.json();
        if (data.ok) {
          user.role = newRole;
          showToast(`Ruolo di "${user.name||user.email}" aggiornato a "${newRole}"`, 'ok');
          renderStats(allUsers);
          renderTable(allUsers);
        } else {
          showToast(data.message || 'Errore', 'err');
          btn.disabled = false;
        }
      } catch(err) {
        showToast('Errore di rete: ' + err.message, 'err');
        btn.disabled = false;
      }
    }
  });

  document.getElementById('deleteCancelBtn').addEventListener('click', () => {
    document.getElementById('deleteModalBg').classList.remove('open');
    pendingDeleteId = null;
  });
  document.getElementById('deleteModalBg').addEventListener('click', (e) => {
    if (e.target === e.currentTarget) {
      document.getElementById('deleteModalBg').classList.remove('open');
      pendingDeleteId = null;
    }
  });
  document.getElementById('deleteConfirmBtn').addEventListener('click', async () => {
    if (!pendingDeleteId) return;
    const id = pendingDeleteId;
    const user = allUsers.find(u => u.id == id);
    document.getElementById('deleteConfirmBtn').disabled = true;
    try {
      const res = await fetch('api.php?action=root_delete_user', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({user_id: id})
      });
      const data = await res.json();
      if (data.ok) {
        allUsers = allUsers.filter(u => u.id != id);
        renderStats(allUsers);
        renderTable(allUsers);
        showToast(`Utente "${user?.name||user?.email||id}" eliminato`, 'ok');
      } else {
        showToast(data.message || 'Errore eliminazione', 'err');
      }
    } catch(err) {
      showToast('Errore di rete: ' + err.message, 'err');
    } finally {
      document.getElementById('deleteModalBg').classList.remove('open');
      document.getElementById('deleteConfirmBtn').disabled = false;
      pendingDeleteId = null;
    }
  });

  function openDrawer(u) {
    if (!u) return;
    document.getElementById('drawerTitle').textContent = u.name || u.email || 'Utente #' + u.id;
    document.getElementById('drawerContent').innerHTML = `
      <dl class="dl">
        <dt>ID</dt><dd>#${u.id}</dd>
        <dt>Nome</dt><dd>${escHtml(u.name||'—')}</dd>
        <dt>Email</dt><dd>${escHtml(u.email||'—')}</dd>
        <dt>Ruolo</dt><dd>${roleBadge(u.role, u.id)}</dd>
        <dt>Classe/Sezione</dt><dd>${escHtml(u.class_section||'—')}</dd>
        <dt>Telefono</dt><dd>${escHtml(u.phone||'—')}</dd>
        <dt>Bio</dt><dd>${escHtml(u.bio||'—')}</dd>
        <dt>XP</dt><dd>${u.xp||0}</dd>
        <dt>Segnalazioni</dt><dd>${u.report_count||0}</dd>
        <dt>Credenziali vault</dt><dd>${u.vault_count||0}</dd>
        <dt>Badge ottenuti</dt><dd>${u.badge_count||0}</dd>
        <dt>Quiz score</dt><dd>${u.quiz_score||0}/12</dd>
        <dt>Ultimo accesso</dt><dd>${u.last_login||'—'}</dd>
        <dt>Registrato il</dt><dd>${u.created_at||'—'}</dd>
      </dl>
      ${u.id != 1 ? `
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem">
        ${u.role==='user' ? `<button class="btn btn-success btn-sm" id="drawerPromote">⬆ Promuovi ad Admin</button>` : ''}
        ${u.role==='admin' ? `<button class="btn btn-warn btn-sm" id="drawerDemote">⬇ Degrada a User</button>` : ''}
      </div>` : '<div style="font-size:.78rem;color:var(--text-faint);margin-top:.5rem">Account root — ruolo non modificabile</div>'}
    `;
    document.getElementById('drawerBg').classList.add('open');
    const promBtn = document.getElementById('drawerPromote');
    const demBtn  = document.getElementById('drawerDemote');
    if (promBtn) promBtn.addEventListener('click', () => changeRoleFromDrawer(u, 'admin'));
    if (demBtn)  demBtn.addEventListener('click',  () => changeRoleFromDrawer(u, 'user'));
  }

  async function changeRoleFromDrawer(u, newRole) {
    try {
      const res = await fetch('api.php?action=root_set_role', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({user_id: u.id, role: newRole})
      });
      const data = await res.json();
      if (data.ok) {
        u.role = newRole;
        showToast(`Ruolo aggiornato a "${newRole}"`, 'ok');
        renderStats(allUsers);
        renderTable(allUsers);
        openDrawer(u);
      } else {
        showToast(data.message || 'Errore', 'err');
      }
    } catch(err) {
      showToast('Errore di rete: ' + err.message, 'err');
    }
  }

  document.getElementById('drawerClose').addEventListener('click', () => {
    document.getElementById('drawerBg').classList.remove('open');
  });
  document.getElementById('drawerBg').addEventListener('click', (e) => {
    if (e.target === e.currentTarget) document.getElementById('drawerBg').classList.remove('open');
  });

  document.getElementById('searchInput').addEventListener('input', () => renderTable(allUsers));
  document.getElementById('roleFilter').addEventListener('change', () => renderTable(allUsers));
  document.getElementById('refreshBtn').addEventListener('click', loadUsers);

  document.addEventListener('DOMContentLoaded', loadUsers);
})();
</script>
</body>
</html>
