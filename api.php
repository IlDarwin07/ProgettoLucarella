<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

set_exception_handler(function(Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Errore interno del server: ' . $e->getMessage()]);
});
set_error_handler(function(int $errno, string $errstr) {
    throw new ErrorException($errstr, 0, $errno);
});

$action = $_GET['action'] ?? '';

function vault_encrypt(string $plain): string {
    $iv = random_bytes(16);
    $enc = openssl_encrypt($plain, 'AES-256-CBC', VAULT_KEY, OPENSSL_RAW_DATA, $iv);
    if ($enc === false) throw new RuntimeException('Cifratura vault fallita — chiave non valida (deve essere 32 byte)');
    return base64_encode($iv . $enc);
}

function vault_decrypt(string $enc): string {
    if (empty($enc)) return '';
    $raw = base64_decode($enc, true);
    if ($raw === false || strlen($raw) < 17) return '(errore decodifica)';
    $iv = substr($raw, 0, 16);
    $ct = substr($raw, 16);
    $result = openssl_decrypt($ct, 'AES-256-CBC', VAULT_KEY, OPENSSL_RAW_DATA, $iv);
    return $result === false ? '(chiave vault non corrispondente)' : $result;
}

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
        try { $pdo->exec("ALTER TABLE users ADD COLUMN $def"); } catch (\Throwable $e) { }
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        badge_id VARCHAR(40) NOT NULL,
        earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_ub (user_id, badge_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function award_badges(PDO $pdo, int $uid): array {
    $u = $pdo->prepare('SELECT * FROM users WHERE id=?'); $u->execute([$uid]); $user=$u->fetch(PDO::FETCH_ASSOC);
    $st2=$pdo->prepare('SELECT COUNT(*) FROM reports WHERE user_id=?'); $st2->execute([$uid]); $reportCount=(int)$st2->fetchColumn();
    $st3=$pdo->prepare('SELECT COUNT(*) FROM vault_items WHERE user_id=?'); $st3->execute([$uid]); $vaultCount=(int)$st3->fetchColumn();
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
    if(!empty($user['name'])&&!empty($user['email'])&&!empty($user['class_section'])&&!empty($user['bio'])) $toAward[]='profile_complete';
    if($uid<=10) $toAward[]='early_adopter';
    $insert=$pdo->prepare('INSERT IGNORE INTO user_badges (user_id,badge_id) VALUES (?,?)');
    $xpMap=['first_report'=>50,'five_reports'=>150,'ten_reports'=>300,'vault_start'=>50,'vault_pro'=>200,
            'quiz_complete'=>100,'quiz_perfect'=>250,'strong_password'=>75,'profile_complete'=>80,
            'early_adopter'=>100,'week_streak'=>200,'security_100'=>500];
    $newXP=0;
    foreach($toAward as $bid){
        if(!in_array($bid',$already)){
            $insert->execute([$uid,$bid]);
            $newXP+=($xpMap[$bid]??0);
        }
    }
    if($newXP>0) $pdo->prepare('UPDATE users SET xp=xp+? WHERE id=?')->execute([$newXP,$uid]);
    $stAll=$pdo->prepare('SELECT badge_id as id,earned_at FROM user_badges WHERE user_id=? ORDER BY earned_at ASC');
    $stAll->execute([$uid]);
    return $stAll->fetchAll(PDO::FETCH_ASSOC);
}

switch ($action) {

    case 'session':
        echo json_encode(['ok' => true, 'user' => $_SESSION['user'] ?? null]);
        break;

    case 'guest_login':
        $_SESSION['user'] = ['id'=>null,'name'=>'Ospite','email'=>null,'role'=>'guest'];
        echo json_encode(['ok' => true, 'user' => $_SESSION['user']]);
        break;

    case 'login':
        $d = json_decode(file_get_contents('php://input'), true);
        $email    = trim($d['email'] ?? '');
        $password = $d['password'] ?? '';
        if (!$email || !$password) { echo json_encode(['ok'=>false,'message'=>'Compila tutti i campi']); exit; }
        ensure_user_columns($pdo);
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([$email]);
        $u = $st->fetch(PDO::FETCH_ASSOC);
        if (!$u || !password_verify($password, $u['password_hash'])) {
            echo json_encode(['ok'=>false,'message'=>'Credenziali non valide']); exit; }
        if (password_needs_rehash($u['password_hash'], PASSWORD_BCRYPT, ['cost'=>12])) {
            $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($password,PASSWORD_BCRYPT,['cost'=>12]),$u['id']]);
        }
        $pdo->prepare('UPDATE users SET last_login=CURDATE() WHERE id=?')->execute([$u['id']]);
        $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
        echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]);
        break;

    case 'register':
        $d = json_decode(file_get_contents('php://input'), true);
        $name     = trim($d['name'] ?? '');
        $email    = trim($d['email'] ?? '');
        $password = $d['password'] ?? '';
        if (!$name || !$email || !$password) { echo json_encode(['ok'=>false,'message'=>'Compila tutti i campi']); exit; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { echo json_encode(['ok'=>false,'message'=>'Email non valida']); exit; }
        if (strlen($password) < 8) { echo json_encode(['ok'=>false,'message'=>'Password troppo corta (min. 8 caratteri)']); exit; }
        ensure_user_columns($pdo);
        $chk = $pdo->prepare('SELECT id FROM users WHERE email=?'); $chk->execute([$email]);
        if ($chk->fetch()) { echo json_encode(['ok'=>false,'message'=>'Email già registrata']); exit; }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);

        // Se è il primo utente nel sistema, promuovilo a root
        $countStmt = $pdo->query('SELECT COUNT(*) FROM users');
        $userCount = (int)$countStmt->fetchColumn();
        $role = $userCount === 0 ? 'root' : 'user';

        $st = $pdo->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)');
        $st->execute([$name,$email,$hash,$role]);
        $id = $pdo->lastInsertId();
        $_SESSION['user'] = ['id'=>$id,'name'=>$name,'email'=>$email,'role'=>$role];
        echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]);
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['ok'=>true]);
        break;

    // ... il resto del file resta invariato ...
