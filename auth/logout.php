<?php
require_once __DIR__ . '/../config/config.php';
$was_admin = is_admin();
session_destroy();
session_start();
if ($was_admin) {
    header('Location: ' . base_url('auth/login.php'));
} else {
    header('Location: ' . base_url('index.php'));
}
exit;
