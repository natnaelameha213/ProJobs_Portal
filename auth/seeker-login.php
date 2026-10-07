<?php
require_once __DIR__ . '/../config/config.php';

if (is_seeker()) {
    redirect('seeker/dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $s = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = ?');
    $s->execute([$email, 'seeker']);
    $u = $s->fetch();
    if ($u && password_verify($pass, $u['password'])) {
        $_SESSION['user'] = $u;
        $redir = $_SESSION['redirect_after_login'] ?? 'seeker/dashboard.php';
        unset($_SESSION['redirect_after_login']);
        redirect($redir);
    }
    $error = 'Invalid email or password.';
}
$page_title = 'Job Seeker Login';
require __DIR__ . '/../includes/header.php';
?>
<div class="container py-5" style="max-width:440px">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <h3 class="fw-bold mb-1">Welcome back</h3>
      <p class="text-muted small mb-4">Sign in to your job seeker account</p>
      <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? 'abebe@example.com') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100 fw-semibold">Sign In</button>
      </form>
      <p class="text-center small mt-3 mb-0">
        Don't have an account? <a href="<?= base_url('auth/seeker-register.php') ?>">Register</a>
      </p>
      <p class="text-center small text-muted mt-2">Demo: abebe@example.com / Seeker123!</p>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
