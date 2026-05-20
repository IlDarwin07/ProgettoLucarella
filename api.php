<?php
require_once __DIR__ . '/includes/auth.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input  = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (json_decode(file_get_contents('php://input'), true) ?? $_POST)
    : $_GET;

// --- Punteggio sicurezza password ---
function password_score(string $password): int {
    $score = 0;
    $len   = mb_strlen($password);
    if ($len >= 8)  $score += 10;
    if ($len >= 12) $score += 15;
    if ($len >= 16) $score += 10;
    if ($len >= 20) $score += 5;
    if (preg_match('/[A-Z]/', $password))           $score += 10;
    if (preg_match('/[a-z]/', $password))           $score += 10;
    if (preg_match('/[0-9]/', $password))           $score += 10;
    if (preg_match('/[^\w\s]/u', $password))        $score += 15;
    if (preg_match('/\s/', $password))              $score += 5;
    if (!preg_match('/(.)\1{2,}/', $password))      $score += 5;
    if (!preg_match('/password|1234|qwerty|admin|scuola|letmein|abc123/i', $password)) $score += 5;
    return min($score, 100);
}

// --- Generatore password ---
function generate_password(bool $words = false): string {
    if ($words) {
        $list = ['ponte','stella','quadro','luna','rete','scudo','fermi','cielo','codice','vento','mare','nodo','pixel','carta','libro','fibra'];
        shuffle($list);
        return ucfirst($list[0]).'-'.ucfirst($list[1]).'-'.rand(100,999).'!';
    }
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*()_+-=';
    $pass  = '';
    $max   = strlen($chars) - 1;
    for ($i = 0; $i < 18; $i++) {
        $pass .= $chars[random_int(0, $max)];
    }
    return $pass;
}

// === SESSION ===
if ($action === 'session') {
    echo json_encode(['ok'=>true,'user'=>current_user()]); exit;
}

// === GENERATE PASSWORD (GET, pubblico) ===
if ($action === 'generate_password') {
    $mode = $_GET['mode'] ?? '';
    echo json_encode(['ok'=>true,'password'=>generate_password($mode==='phrase')]); exit;
}

// === SECURITY SCORE (POST, pubblico) ===
if ($action === 'security_score') {
    $pw = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pw = $input['password'] ?? '';
    } else {
        $pw = $_GET['password'] ?? '';
    }
    echo json_encode(['ok'=>true,'score'=>password_score($pw)]); exit;
}

// === REGISTER ===
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($input['name']  ?? '');
    $email    = trim($input['email'] ?? '');
    $password = $input['password']   ?? '';
    if (!$name || !$email || !$password) {
        echo json_encode(['ok'=>false,'message'=>'Tutti i campi sono obbligatori']); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok'=>false,'message'=>'Email non valida']); exit;
    }
    if (password_score($password) < 50) {
        echo json_encode(['ok'=>false,'message'=>'Password troppo debole (minimo 50/100)']); exit;
    }
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['ok'=>false,'message'=>'Email gia registrata']); exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)');
    $stmt->execute([$name,$email,$hash]);
    $_SESSION['user'] = ['id'=>(int)$pdo->lastInsertId(),'name'=>$name,'email'=>$email];
    echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]); exit;
}

// === LOGIN ===
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($input['email']  ?? '');
    $password = $input['password']    ?? '';
    $stmt     = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        echo json_encode(['ok'=>false,'message'=>'Credenziali non valide']); exit;
    }
    $_SESSION['user'] = ['id'=>(int)$user['id'],'name'=>$user['name'],'email'=>$user['email']];
    echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]); exit;
}

// === LOGOUT ===
if ($action === 'logout') {
    session_destroy();
    echo json_encode(['ok'=>true]); exit;
}

// === ADD REPORT (pubblico, supporta anonimo) ===
if ($action === 'add_report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $anonymous   = !empty($input['anonymous']);
    $name        = $anonymous ? 'Anonimo' : trim($input['name'] ?? 'Anonimo');
    $category    = trim($input['category']    ?? 'Altro');
    $title       = trim($input['title']       ?? '');
    $description = trim($input['description'] ?? '');
    $priority    = trim($input['priority']    ?? 'Media');
    if (!$title || !$description) {
        echo json_encode(['ok'=>false,'message'=>'Titolo e descrizione obbligatori']); exit;
    }
    $allowed = ['Bullismo','Problema tecnico','Privacy / Dati personali','Sicurezza rete scolastica','Materiali didattici','Altro'];
    if (!in_array($category, $allowed, true)) $category = 'Altro';
    if (!in_array($priority, ['Bassa','Media','Alta'], true)) $priority = 'Media';
    $tracking = 'SS-'.strtoupper(substr(bin2hex(random_bytes(4)),0,6));
    $stmt = $pdo->prepare(
        'INSERT INTO reports (user_id,name,category,title,description,priority,status,tracking_code)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([current_user()['id']??null, $name, $category, $title, $description, $priority, 'Aperta', $tracking]);
    echo json_encode(['ok'=>true,'tracking'=>$tracking]); exit;
}

// === LIST REPORTS ===
if ($action === 'reports') {
    $rows = $pdo->query(
        'SELECT id,name,category,title,description,priority,status,tracking_code,created_at
         FROM reports ORDER BY id DESC LIMIT 20'
    )->fetchAll();
    echo json_encode(['ok'=>true,'reports'=>$rows]); exit;
}

// === SAVE PASSWORD VAULT (solo autenticati) ===
if ($action === 'save_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login_json();
    $site_name      = trim($input['site_name']       ?? '');
    $username       = trim($input['username']        ?? '');
    $password_plain = trim($input['password_plain']  ?? '');
    $notes          = trim($input['notes']           ?? '');
    if (!$site_name || !$username || !$password_plain) {
        echo json_encode(['ok'=>false,'message'=>'Campi obbligatori mancanti']); exit;
    }
    $stmt = $pdo->prepare(
        'INSERT INTO vault_items (user_id,site_name,username,password_plain,notes) VALUES (?,?,?,?,?)'
    );
    $stmt->execute([current_user()['id'],$site_name,$username,$password_plain,$notes]);
    echo json_encode(['ok'=>true]); exit;
}

// === DELETE VAULT ITEM ===
if ($action === 'delete_vault' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login_json();
    $id = (int)($input['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM vault_items WHERE id=? AND user_id=?');
    $stmt->execute([$id, current_user()['id']]);
    echo json_encode(['ok'=>true]); exit;
}

// === LIST VAULT ===
if ($action === 'vault') {
    if (!is_logged_in()) {
        echo json_encode(['ok'=>true,'items'=>[]]); exit;
    }
    $stmt = $pdo->prepare(
        'SELECT id,site_name,username,password_plain,notes,created_at
         FROM vault_items WHERE user_id=? ORDER BY id DESC'
    );
    $stmt->execute([current_user()['id']]);
    echo json_encode(['ok'=>true,'items'=>$stmt->fetchAll()]); exit;
}

echo json_encode(['ok'=>false,'message'=>'Azione non valida']);
