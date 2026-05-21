<?php
require_once __DIR__ . '/includes/auth.php';
if(($_SESSION['user']['role'] ?? '') === 'guest') { header('Location: guest.php'); exit; }
header('Location: login.php');
exit;
