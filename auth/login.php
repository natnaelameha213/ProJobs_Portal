<?php
require_once __DIR__ . '/../config/config.php';

if (is_admin()) {
    redirect('admin/dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $s = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = ?');
    $s->execute([$email, 'admin']);
    $u = $s->fetch();
    if ($u && password_verify($pass, $u['password'])) {
        $_SESSION['user'] = $u;
        redirect('admin/dashboard.php');
    }
    $error = 'Invalid admin credentials.';
}
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login · ProJobs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url('assets/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100">
<div class="card p-4 shadow border-0" style="width:400px;border-radius:16px">
  <div class="text-center mb-3">
    <i class="bi bi-shield-lock fs-1 text-primary"></i>
    <h3 class="fw-bold mt-2">Admin Login</h3>
    <p class="text-muted small">ProJobs Recruitment Portal</p>
  </div>
  <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label small fw-semibold">Email</label>
      <input class="form-control" type="email" name="email" value="admin@projobs.local" required>
    </div>
    <div class="mb-3">
      <label class="form-label small fw-semibold">Password</label>
      <input class="form-control" type="password" name="password" placeholder="Password" required>
    </div>
    <button class="btn btn-primary w-100 fw-semibold">Sign In</button>
  </form>
  <div class="text-center mt-3">
    <small class="text-muted">Demo: admin@projobs.local / Admin123!</small><br>
    <a href="<?= base_url('index.php') ?>" class="small">← Back to marketplace</a>
  </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</body>
</html>
