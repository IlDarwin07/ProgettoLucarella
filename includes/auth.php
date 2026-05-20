<?php
session_start();
require_once __DIR__ . '/db.php';

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login_json() {
    if (!is_logged_in()) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'Login richiesto']);
        exit;
    }
}
