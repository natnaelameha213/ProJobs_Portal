<?php
/**
 * Authentication helpers - supports both Admin and Job Seeker roles
 */

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return !empty($_SESSION['user']);
}

function is_admin() {
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function is_seeker() {
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'seeker';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . base_url('auth/login.php'));
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header('Location: ' . base_url('auth/login.php'));
        exit;
    }
}

function require_seeker() {
    if (!is_seeker()) {
        header('Location: ' . base_url('auth/seeker-login.php'));
        exit;
    }
}

function e($x) {
    return htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8');
}

/**
 * Reliable base URL for ProJobs Portal on XAMPP / shared hosting
 * Detects the project folder from SCRIPT_NAME
 */
function base_url($path = '') {
    static $base = null;
    if ($base === null) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        // Remove filename
        $dir = dirname($script);
        // Climb out of known subfolders
        $subfolders = ['admin', 'auth', 'seeker', 'includes'];
        $parts = array_filter(explode('/', trim($dir, '/')));
        $parts = array_values($parts);
        while (!empty($parts) && in_array(end($parts), $subfolders, true)) {
            array_pop($parts);
        }
        $base = '/' . implode('/', $parts);
        if ($base === '/') $base = '';
        // Ensure no trailing issues
        $base = rtrim($base, '/');
    }
    $path = ltrim($path, '/');
    if ($path === '') return $base . '/';
    return $base . '/' . $path;
}

function redirect($path) {
    header('Location: ' . base_url($path));
    exit;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}
