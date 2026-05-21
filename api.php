<?php
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

// Chiave AES per cifratura vault (in produzione usare variabile d'ambiente)
define('VAULT_KEY', hash('sha256', 'safeschool_vault_secret_key', true));

function vault_encrypt(string $plain): string {
    $iv = random_bytes(16);
    $enc = openssl_encrypt($plain, 'AES-256-CBC', VAULT_KEY, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $enc);
}

function vault_decrypt(string $enc): string {
    $raw = base64_decode($enc);
    $iv  = substr($raw, 0, 16);
    $ct  = substr($raw, 16);
    return openssl_decrypt($ct, 'AES-256-CBC', VAULT_KEY, OPENSSL_RAW_DATA, $iv);
}

switch ($action) {

    /* -------- SESSION -------- */
    case 'session':
        echo json_encode(['ok' => true, 'user' => $_SESSION['user'] ?? null]);
        break;

    /* -------- GUEST LOGIN -------- */
    case 'guest_login':
        $_SESSION['user'] = [
            'id'    => null,
            'name'  => 'Ospite',
            'email' => null,
            'role'  => 'guest',
        ];
        echo json_encode(['ok' => true, 'user' => $_SESSION['user']]);
        break;

    /* -------- LOGIN -------- */
    case 'login':
        $d = json_decode(file_get_contents('php://input'), true);
        $email    = trim($d['email'] ?? '');
        $password = $d['password'] ?? '';
        if (!$email || !$password) {
            echo json_encode(['ok' => false, 'message' => 'Compila tutti i campi']); exit;
        }
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([$email]);
        $u = $st->fetch(PDO::FETCH_ASSOC);
        if (!$u || !password_verify($password, $u['password_hash'])) {
            echo json_encode(['ok' => false, 'message' => 'Credenziali non valide']); exit;
        }
        if (password_needs_rehash($u['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$newHash, $u['id']]);
        }
        $_SESSION['user'] = [
            'id'   => $u['id'],
            'name' => $u['name'],
            'email'=> $u['email'],
            'role' => $u['role'],
        ];
        echo json_encode(['ok' => true, 'user' => $_SESSION['user']]);
        break;

    /* -------- REGISTER -------- */
    case 'register':
        $d = json_decode(file_get_contents('php://input'), true);
        $name     = trim($d['name'] ?? '');
        $email    = trim($d['email'] ?? '');
        $password = $d['password'] ?? '';
        if (!$name || !$email || !$password) {
            echo json_encode(['ok' => false, 'message' => 'Compila tutti i campi']); exit;
        }
        if (strlen($password) < 8) {
            echo json_encode(['ok' => false, 'message' => 'Password troppo corta (min. 8 caratteri)']); exit;
        }
        $chk = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) {
            echo json_encode(['ok' => false, 'message' => 'Email già registrata']); exit;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $st = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $st->execute([$name, $email, $hash, 'user']);
        $id = $pdo->lastInsertId();
        $_SESSION['user'] = ['id' => $id, 'name' => $name, 'email' => $email, 'role' => 'user'];
        echo json_encode(['ok' => true, 'user' => $_SESSION['user']]);
        break;

    /* -------- LOGOUT -------- */
    case 'logout':
        session_destroy();
        echo json_encode(['ok' => true]);
        break;

    /* -------- REPORTS (lista pubblica) -------- */
    case 'reports':
        $st = $pdo->query('SELECT id, name, category, title, priority, status, created_at FROM reports ORDER BY created_at DESC LIMIT 30');
        echo json_encode(['ok' => true, 'reports' => $st->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    /* -------- REPORT CREATE -------- */
    case 'report_create':
        $d = json_decode(file_get_contents('php://input'), true);
        $name     = trim($d['name'] ?? 'Anonimo');
        $category = trim($d['category'] ?? 'Altro');
        $title    = trim($d['title'] ?? '');
        $desc     = trim($d['description'] ?? '');
        $priority = in_array($d['priority'] ?? '', ['Bassa','Media','Alta']) ? $d['priority'] : 'Media';
        $anon     = !empty($d['anonymous']);
        if ($anon) $name = 'Anonimo';
        if (!$title || !$desc) {
            echo json_encode(['ok' => false, 'message' => 'Titolo e descrizione obbligatori']); exit;
        }
        $code = strtoupper(bin2hex(random_bytes(5)));
        $uid  = $_SESSION['user']['id'] ?? null;
        $st = $pdo->prepare(
            'INSERT INTO reports (user_id,name,category,title,description,priority,tracking_code) VALUES (?,?,?,?,?,?,?)'
        );
        $st->execute([$uid, $name, $category, $title, $desc, $priority, $code]);
        echo json_encode(['ok' => true, 'tracking_code' => $code]);
        break;

    /* -------- REPORT STATUS UPDATE (admin) -------- */
    case 'report_status':
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['ok' => false, 'message' => 'Non autorizzato']); exit;
        }
        $d = json_decode(file_get_contents('php://input'), true);
        $pdo->prepare('UPDATE reports SET status = ? WHERE id = ?')
            ->execute([$d['status'], $d['id']]);
        echo json_encode(['ok' => true]);
        break;

    /* -------- REPORT DELETE (admin) -------- */
    case 'delete_report':
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['ok' => false, 'message' => 'Non autorizzato']); exit;
        }
        $d = json_decode(file_get_contents('php://input'), true);
        $pdo->prepare('DELETE FROM reports WHERE id = ?')->execute([$d['id']]);
        echo json_encode(['ok' => true]);
        break;

    /* -------- ADMIN REPORTS -------- */
    case 'admin_reports':
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['ok' => false, 'message' => 'Non autorizzato']); exit;
        }
        $st = $pdo->query('SELECT * FROM reports ORDER BY created_at DESC');
        echo json_encode(['ok' => true, 'reports' => $st->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    /* -------- VAULT LIST -------- */
    case 'vault_list':
        require_login_json();
        $uid = $_SESSION['user']['id'];
        $st = $pdo->prepare('SELECT * FROM vault_items WHERE user_id = ? ORDER BY created_at DESC');
        $st->execute([$uid]);
        $items = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as &$item) {
            $item['password_plain'] = vault_decrypt($item['password_enc']);
            unset($item['password_enc']);
        }
        unset($item);
        echo json_encode(['ok' => true, 'items' => $items]);
        break;

    /* -------- VAULT ADD -------- */
    case 'vault_add':
        require_login_json();
        $uid = $_SESSION['user']['id'];
        $d = json_decode(file_get_contents('php://input'), true);
        $site = trim($d['site_name'] ?? '');
        $user = trim($d['username'] ?? '');
        $pw   = $d['password_plain'] ?? '';
        $note = trim($d['notes'] ?? '');
        if (!$site || !$user || !$pw) {
            echo json_encode(['ok' => false, 'message' => 'Servizio, username e password obbligatori']); exit;
        }
        $pwEnc = vault_encrypt($pw);
        $pdo->prepare(
            'INSERT INTO vault_items (user_id,site_name,username,password_enc,notes) VALUES (?,?,?,?,?)'
        )->execute([$uid, $site, $user, $pwEnc, $note]);
        echo json_encode(['ok' => true]);
        break;

    /* -------- VAULT DELETE -------- */
    case 'vault_delete':
        require_login_json();
        $uid = $_SESSION['user']['id'];
        $d = json_decode(file_get_contents('php://input'), true);
        $pdo->prepare('DELETE FROM vault_items WHERE id = ? AND user_id = ?')
            ->execute([$d['id'], $uid]);
        echo json_encode(['ok' => true]);
        break;

    /* -------- GENERATE PASSWORD -------- */
    case 'generate_password':
        $mode = $_GET['mode'] ?? 'random';
        if ($mode === 'phrase') {
            $words = ['Scuola','Fermi','Foggia','Laptop','Tastiera','Monitor','Python','Linux',
                      'Sicuro','Verde','Rapido','Forte','Cielo','Torre','Nuvola','Ponte',
                      'Stella','Libro','Codice','Pixel'];
            shuffle($words);
            $phrase = implode('-', array_slice($words, 0, 4)) . rand(10,99);
            echo json_encode(['ok' => true, 'password' => $phrase]);
        } else {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
            $len = 18;
            $pw = '';
            for ($i = 0; $i < $len; $i++) {
                $pw .= $chars[random_int(0, strlen($chars) - 1)];
            }
            echo json_encode(['ok' => true, 'password' => $pw]);
        }
        break;

    default:
        echo json_encode(['ok' => false, 'message' => 'Azione non valida']);
}
