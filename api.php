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

// Aggiunge colonne extra a users se non esistono (migrazione lazy)
function ensure_user_columns(PDO $pdo): void {
    $cols = ['class_section VARCHAR(50) DEFAULT NULL',
             'phone VARCHAR(30) DEFAULT NULL',
             'bio VARCHAR(200) DEFAULT NULL',
             'avatar_color TINYINT UNSIGNED DEFAULT 0',
             'xp INT UNSIGNED DEFAULT 0',
             'quiz_score TINYINT UNSIGNED DEFAULT 0',
             'last_login DATE DEFAULT NULL',
             'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'];
    foreach ($cols as $def) {
        $col = explode(' ', $def)[0];
        try { $pdo->exec("ALTER TABLE users ADD COLUMN $def"); } catch (\Throwable $e) { /* gia' esiste */ }
    }
    // Tabella badge utente
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        badge_id VARCHAR(40) NOT NULL,
        earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_ub (user_id, badge_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Calcola e assegna badge in base ai dati correnti
function award_badges(PDO $pdo, int $uid): array {
    // Dati freschi
    $u = $pdo->prepare('SELECT * FROM users WHERE id=?'); $u->execute([$uid]); $user=$u->fetch(PDO::FETCH_ASSOC);
    $st2=$pdo->prepare('SELECT COUNT(*) FROM reports WHERE user_id=?'); $st2->execute([$uid]); $reportCount=(int)$st2->fetchColumn();
    $st3=$pdo->prepare('SELECT COUNT(*) FROM vault_items WHERE user_id=?'); $st3->execute([$uid]); $vaultCount=(int)$st3->fetchColumn();
    // Earned badges gia' presenti
    $stB=$pdo->prepare('SELECT badge_id FROM user_badges WHERE user_id=?'); $stB->execute([$uid]);
    $already=array_column($stB->fetchAll(PDO::FETCH_ASSOC),'badge_id');
    $toAward=[];
    if($reportCount>=1)  $toAward[]='first_report';
    if($reportCount>=5)  $toAward[]='five_reports';
    if($reportCount>=10) $toAward[]='ten_reports';
    if($vaultCount>=1)   $toAward[]='vault_start';
    if($vaultCount>=5)   $toAward[]='vault_pro';
    if(($user['quiz_score']??0)>=1)  $toAward[]='quiz_complete';
    if(($user['quiz_score']??0)>=5)  $toAward[]='quiz_perfect';
    // profile_complete: tutti i campi base compilati
    if(!empty($user['name'])&&!empty($user['email'])&&!empty($user['class_section'])&&!empty($user['bio'])) $toAward[]='profile_complete';
    // early adopter: id<=10
    if($uid<=10) $toAward[]='early_adopter';
    $insert=$pdo->prepare('INSERT IGNORE INTO user_badges (user_id,badge_id) VALUES (?,?)');
    $xpMap=['first_report'=>50,'five_reports'=>150,'ten_reports'=>300,'vault_start'=>50,'vault_pro'=>200,
            'quiz_complete'=>100,'quiz_perfect'=>250,'strong_password'=>75,'profile_complete'=>80,
            'early_adopter'=>100,'week_streak'=>200,'security_100'=>500];
    $newXP=0;
    foreach($toAward as $bid){
        if(!in_array($bid,$already)){
            $insert->execute([$uid,$bid]);
            $newXP+=($xpMap[$bid]??0);
        }
    }
    if($newXP>0){
        $pdo->prepare('UPDATE users SET xp=xp+? WHERE id=?')->execute([$newXP,$uid]);
    }
    // Ritorna tutti i badge earned
    $stAll=$pdo->prepare('SELECT badge_id as id,earned_at FROM user_badges WHERE user_id=? ORDER BY earned_at ASC');
    $stAll->execute([$uid]);
    return $stAll->fetchAll(PDO::FETCH_ASSOC);
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
        ensure_user_columns($pdo);
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
        $pdo->prepare('UPDATE users SET last_login=CURDATE() WHERE id=?')->execute([$u['id']]);
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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => false, 'message' => 'Email non valida']); exit;
        }
        if (strlen($password) < 8) {
            echo json_encode(['ok' => false, 'message' => 'Password troppo corta (min. 8 caratteri)']); exit;
        }
        ensure_user_columns($pdo);
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
    case 'list_reports':
        $st = $pdo->query('SELECT id, name, category, title, priority, status, created_at FROM reports ORDER BY created_at DESC LIMIT 30');
        echo json_encode(['ok' => true, 'reports' => $st->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    /* -------- REPORT CREATE -------- */
    case 'report_create':
    case 'create_report':
        $d = json_decode(file_get_contents('php://input'), true);
        $name     = trim($d['reporter_name'] ?? $d['name'] ?? 'Anonimo');
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
        // Assegna badge segnalazione
        if ($uid) award_badges($pdo, (int)$uid);
        echo json_encode(['ok' => true, 'success' => true, 'tracking_code' => $code]);
        break;

    /* -------- REPORT STATUS UPDATE (admin) -------- */
    case 'report_status':
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['ok' => false, 'message' => 'Non autorizzato']); exit;
        }
        $d = json_decode(file_get_contents('php://input'), true);
        $validStatuses = ['Aperta', 'In revisione', 'Risolta', 'Chiusa'];
        if (empty($d['id']) || empty($d['status']) || !in_array($d['status'], $validStatuses)) {
            echo json_encode(['ok' => false, 'message' => 'Dati non validi']); exit;
        }
        $pdo->prepare('UPDATE reports SET status = ? WHERE id = ?')
            ->execute([$d['status'], (int)$d['id']]);
        echo json_encode(['ok' => true]);
        break;

    /* -------- REPORT DELETE (admin) -------- */
    case 'delete_report':
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['ok' => false, 'message' => 'Non autorizzato']); exit;
        }
        $d = json_decode(file_get_contents('php://input'), true);
        if (empty($d['id'])) {
            echo json_encode(['ok' => false, 'message' => 'ID mancante']); exit;
        }
        $pdo->prepare('DELETE FROM reports WHERE id = ?')->execute([(int)$d['id']]);
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
        // Assegna badge vault
        award_badges($pdo, (int)$uid);
        // +5 XP per ogni nuova credenziale
        $pdo->prepare('UPDATE users SET xp=xp+5 WHERE id=?')->execute([$uid]);
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
            // +10 XP per aver usato il generatore
            $uid2 = $_SESSION['user']['id'] ?? null;
            if ($uid2) {
                ensure_user_columns($pdo);
                $pdo->prepare('UPDATE users SET xp=xp+10 WHERE id=?')->execute([$uid2]);
                // Badge strong_password
                $pdo->prepare('INSERT IGNORE INTO user_badges (user_id,badge_id) VALUES (?,?)')->execute([$uid2,'strong_password']);
            }
            echo json_encode(['ok' => true, 'password' => $pw]);
        }
        break;

    /* -------- PROFILE GET -------- */
    case 'profile_get':
        require_login_json();
        $uid = (int)$_SESSION['user']['id'];
        ensure_user_columns($pdo);
        $st = $pdo->prepare('SELECT id,name,email,role,class_section,phone,bio,avatar_color,xp,quiz_score,created_at FROM users WHERE id=?');
        $st->execute([$uid]);
        $profile = $st->fetch(PDO::FETCH_ASSOC);
        if (!$profile) { echo json_encode(['ok'=>false,'message'=>'Utente non trovato']); exit; }
        // Stats
        $st2=$pdo->prepare('SELECT COUNT(*) FROM reports WHERE user_id=?'); $st2->execute([$uid]); $reportCount=(int)$st2->fetchColumn();
        $st3=$pdo->prepare('SELECT COUNT(*) FROM vault_items WHERE user_id=?'); $st3->execute([$uid]); $vaultCount=(int)$st3->fetchColumn();
        $stats = [
            'xp'           => (int)($profile['xp']??0),
            'report_count' => $reportCount,
            'vault_count'  => $vaultCount,
            'quiz_score'   => (int)($profile['quiz_score']??0),
            'badge_count'  => 0,
        ];
        // Earned badges + auto-award
        $earned = award_badges($pdo, $uid);
        $stats['badge_count'] = count($earned);
        // Refresh XP dopo award
        $stXP=$pdo->prepare('SELECT xp FROM users WHERE id=?'); $stXP->execute([$uid]); $stats['xp']=(int)$stXP->fetchColumn();
        // Leaderboard top 10
        $stLB=$pdo->query('SELECT id,name,xp,avatar_color FROM users WHERE role!=\'guest\' ORDER BY xp DESC LIMIT 10');
        $lb=$stLB->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['ok'=>true,'profile'=>$profile,'stats'=>$stats,'earned_badges'=>$earned,'leaderboard'=>$lb]);
        break;

    /* -------- PROFILE UPDATE -------- */
    case 'profile_update':
        require_login_json();
        $uid = (int)$_SESSION['user']['id'];
        ensure_user_columns($pdo);
        $d = json_decode(file_get_contents('php://input'), true);
        $allowed = ['name','class_section','phone','bio','avatar_color'];
        $sets=[]; $vals=[];
        foreach($allowed as $f){
            if(isset($d[$f])){
                $sets[]="$f=?";
                $vals[]=$d[$f];
            }
        }
        if(empty($sets)){ echo json_encode(['ok'=>true]); break; }
        $vals[]=$uid;
        $pdo->prepare('UPDATE users SET '.implode(',',$sets).' WHERE id=?')->execute($vals);
        // Aggiorna sessione nome
        if(isset($d['name'])) $_SESSION['user']['name']=$d['name'];
        // Ricalcola badge profilo completo
        award_badges($pdo, $uid);
        echo json_encode(['ok'=>true]);
        break;

    /* -------- CHANGE PASSWORD -------- */
    case 'change_password':
        require_login_json();
        $uid = (int)$_SESSION['user']['id'];
        $d = json_decode(file_get_contents('php://input'), true);
        $oldPw = $d['old_password'] ?? '';
        $newPw = $d['new_password'] ?? '';
        if(!$oldPw||!$newPw){ echo json_encode(['ok'=>false,'message'=>'Compila entrambi i campi']); exit; }
        if(strlen($newPw)<8){ echo json_encode(['ok'=>false,'message'=>'Password minimo 8 caratteri']); exit; }
        $st=$pdo->prepare('SELECT password_hash FROM users WHERE id=?'); $st->execute([$uid]);
        $row=$st->fetch(PDO::FETCH_ASSOC);
        if(!$row||!password_verify($oldPw,$row['password_hash'])){
            echo json_encode(['ok'=>false,'message'=>'Password attuale errata']); exit;
        }
        $newHash=password_hash($newPw,PASSWORD_BCRYPT,['cost'=>12]);
        $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([$newHash,$uid]);
        echo json_encode(['ok'=>true]);
        break;

    default:
        echo json_encode(['ok' => false, 'message' => 'Azione non valida']);
}
