<?php
require_once __DIR__ . '/includes/auth.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input  = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (json_decode(file_get_contents('php://input'), true) ?? $_POST)
    : $_GET;

// --- Punteggio sicurezza password ---
function password_score(string $pw): int {
    $s = 0; $l = mb_strlen($pw);
    if ($l >= 8)  $s += 10;
    if ($l >= 12) $s += 15;
    if ($l >= 16) $s += 10;
    if ($l >= 20) $s += 5;
    if (preg_match('/[A-Z]/', $pw))  $s += 10;
    if (preg_match('/[a-z]/', $pw))  $s += 10;
    if (preg_match('/[0-9]/', $pw))  $s += 10;
    if (preg_match('/[^\w\s]/u', $pw)) $s += 15;
    if (preg_match('/\s/', $pw))     $s += 5;
    if (!preg_match('/(.)\1{2,}/', $pw)) $s += 5;
    if (!preg_match('/password|1234|qwerty|admin|scuola|letmein|abc123/i', $pw)) $s += 5;
    return min($s, 100);
}

// --- Generatore password ---
function generate_password(bool $phrase = false): string {
    if ($phrase) {
        $w = ['ponte','stella','quadro','luna','rete','scudo','fermi','cielo','codice','vento','mare','nodo','pixel','carta','libro','fibra'];
        shuffle($w);
        return ucfirst($w[0]).'-'.ucfirst($w[1]).'-'.rand(100,999).'!';
    }
    $c = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*()_+-=';
    $p = ''; $m = strlen($c) - 1;
    for ($i = 0; $i < 18; $i++) $p .= $c[random_int(0, $m)];
    return $p;
}

// SESSION
if ($action === 'session') {
    echo json_encode(['ok'=>true,'user'=>current_user()]); exit;
}

// GENERATE PASSWORD
if ($action === 'generate_password') {
    echo json_encode(['ok'=>true,'password'=>generate_password(($_GET['mode']??'')==='phrase')]); exit;
}

// SECURITY SCORE
if ($action === 'security_score') {
    $pw = $input['password'] ?? '';
    echo json_encode(['ok'=>true,'score'=>password_score($pw)]); exit;
}

// REGISTER
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($input['name'] ?? '');
    $email    = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    if (!$name || !$email || !$password) {
        echo json_encode(['ok'=>false,'message'=>'Tutti i campi sono obbligatori']); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok'=>false,'message'=>'Email non valida']); exit;
    }
    if (password_score($password) < 50) {
        echo json_encode(['ok'=>false,'message'=>'Password troppo debole (minimo 50/100)']); exit;
    }
    $st = $pdo->prepare('SELECT id FROM users WHERE email=?');
    $st->execute([$email]);
    if ($st->fetch()) {
        echo json_encode(['ok'=>false,'message'=>'Email già registrata']); exit;
    }
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $st = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?,?)');
    $st->execute([$name, $email, $hash, 'user']);
    $_SESSION['user'] = ['id'=>(int)$pdo->lastInsertId(),'name'=>$name,'email'=>$email,'role'=>'user'];
    echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]); exit;
}

// LOGIN
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $st = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $st->execute([$email]);
    $u = $st->fetch();
    if (!$u || !password_verify($password, $u['password_hash'])) {
        echo json_encode(['ok'=>false,'message'=>'Credenziali non valide']); exit;
    }
    if (password_needs_rehash($u['password_hash'], PASSWORD_BCRYPT, ['cost'=>12])) {
        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
        $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([$newHash, $u['id']]);
    }
    $_SESSION['user'] = ['id'=>(int)$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
    echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]); exit;
}

// LOGOUT
if ($action === 'logout') {
    session_destroy();
    echo json_encode(['ok'=>true]); exit;
}

// ADD REPORT
if ($action === 'add_report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $anon = !empty($input['anonymous']);
    $name = $anon ? 'Anonimo' : trim($input['name'] ?? 'Anonimo');
    if (!$name) $name = 'Anonimo';
    $category    = trim($input['category'] ?? 'Altro');
    $title       = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');
    $priority    = trim($input['priority'] ?? 'Media');
    if (!$title || !$description) {
        echo json_encode(['ok'=>false,'message'=>'Titolo e descrizione obbligatori']); exit;
    }
    $allowed = ['Bullismo','Problema tecnico','Privacy / Dati personali','Sicurezza rete scolastica','Materiali didattici','Altro'];
    if (!in_array($category, $allowed, true)) $category = 'Altro';
    if (!in_array($priority, ['Bassa','Media','Alta'], true)) $priority = 'Media';
    $tracking = 'SS-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    $st = $pdo->prepare('INSERT INTO reports (user_id,name,category,title,description,priority,status,tracking_code) VALUES (?,?,?,?,?,?,?,?)');
    $st->execute([current_user()['id'] ?? null, $name, $category, $title, $description, $priority, 'Aperta', $tracking]);
    echo json_encode(['ok'=>true,'tracking'=>$tracking]); exit;
}

// LIST REPORTS
if ($action === 'reports') {
    if (is_admin()) {
        $rows = $pdo->query('SELECT r.*,u.email as user_email FROM reports r LEFT JOIN users u ON r.user_id=u.id ORDER BY r.id DESC')->fetchAll();
    } else {
        $rows = $pdo->query('SELECT id,name,category,title,description,priority,status,tracking_code,created_at FROM reports ORDER BY id DESC LIMIT 20')->fetchAll();
    }
    echo json_encode(['ok'=>true,'reports'=>$rows,'is_admin'=>is_admin()]); exit;
}

// ADMIN: aggiorna status
if ($action === 'update_report_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login_json(); require_admin_json();
    $id     = (int)($input['id'] ?? 0);
    $status = trim($input['status'] ?? '');
    $allowed = ['Aperta','In lavorazione','Risolta','Chiusa'];
    if (!$id || !in_array($status, $allowed, true)) {
        echo json_encode(['ok'=>false,'message'=>'Dati non validi']); exit;
    }
    $pdo->prepare('UPDATE reports SET status=? WHERE id=?')->execute([$status, $id]);
    echo json_encode(['ok'=>true]); exit;
}

// ADMIN: elimina segnalazione
if ($action === 'delete_report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login_json(); require_admin_json();
    $id = (int)($input['id'] ?? 0);
    $pdo->prepare('DELETE FROM reports WHERE id=?')->execute([$id]);
    echo json_encode(['ok'=>true]); exit;
}

// VAULT SAVE
if ($action === 'save_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login_json();
    $site = trim($input['site_name'] ?? '');
    $user = trim($input['username'] ?? '');
    $pw   = trim($input['password_plain'] ?? '');
    if (!$site || !$user || !$pw) {
        echo json_encode(['ok'=>false,'message'=>'Campi obbligatori mancanti']); exit;
    }
    $st = $pdo->prepare('INSERT INTO vault_items (user_id,site_name,username,password_plain,notes) VALUES (?,?,?,?,?)');
    $st->execute([current_user()['id'], $site, $user, $pw, trim($input['notes'] ?? '')]);
    echo json_encode(['ok'=>true]); exit;
}

// VAULT DELETE
if ($action === 'delete_vault' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login_json();
    $st = $pdo->prepare('DELETE FROM vault_items WHERE id=? AND user_id=?');
    $st->execute([(int)($input['id'] ?? 0), current_user()['id']]);
    echo json_encode(['ok'=>true]); exit;
}

// VAULT LIST
if ($action === 'vault') {
    if (!is_logged_in()) {
        echo json_encode(['ok'=>true,'items'=>[]]); exit;
    }
    $st = $pdo->prepare('SELECT id,site_name,username,password_plain,notes,created_at FROM vault_items WHERE user_id=? ORDER BY id DESC');
    $st->execute([current_user()['id']]);
    echo json_encode(['ok'=>true,'items'=>$st->fetchAll()]); exit;
}

echo json_encode(['ok'=>false,'message'=>'Azione non valida']);
