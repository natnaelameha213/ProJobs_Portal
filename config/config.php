<?php
/**
 * ProJobs Portal - Core Configuration
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'projobs_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// App
define('APP_NAME', 'ProJobs Portal');
define('APP_URL', ''); // leave empty for relative paths; set full URL in production if needed
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', 'assets/uploads/');
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024); // 2MB
define('MAX_CV_SIZE', 5 * 1024 * 1024);    // 5MB
define('ALLOWED_IMAGES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_CV', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Please import database/projobs_portal.sql and check config.');
}

require_once __DIR__ . '/../auth/auth.php';
require_once __DIR__ . '/../includes/functions.php';
