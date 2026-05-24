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
.score-ring-w