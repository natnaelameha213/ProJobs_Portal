<?php
require_once __DIR__ . '/../config/config.php';

if (is_seeker()) {
    redirect('seeker/dashboard.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        $pass2 = $_POST['password2'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');

        if ($name === '') $errors[] = 'Name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($pass !== $pass2) $errors[] = 'Passwords do not match.';

        if (!$errors) {
            $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = 'Email already registered.';
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $pdo->prepare('INSERT INTO users (name, email, password, role, phone, location) VALUES (?,?,?,?,?,?)')
                    ->execute([$name, $email, $hash, 'seeker', $phone, $location]);
                $uid = $pdo->lastInsertId();
                $s = $pdo->prepare('SELECT * FROM users WHERE id = ?');
                $s->execute([$uid]);
                $_SESSION['user'] = $s->fetch();
                flash('success', 'Account created successfully! Complete your profile.');
                redirect('seeker/profile.php');
            }
        }
    }
}
$page_title = 'Create Job Seeker Account';
require __DIR__ . '/../includes/header.php';
?>
<div class="container py-5" style="max-width:520px">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <h3 class="fw-bold mb-1">Create your account</h3>
      <p class="text-muted small mb-4">Join ProJobs and start applying today</p>
      <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="mb-3">
          <label class="form-label fw-semibold">Full Name *</label>
          <input type="text" name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Email *</label>
          <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Password *</label>
            <input type="password" name="password" class="form-control" required minlength="6">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Confirm Password *</label>
            <input type="password" name="password2" class="form-control" required minlength="6">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Location</label>
            <input type="text" name="location" class="form-control" placeholder="Addis Ababa" value="<?= e($_POST['location'] ?? '') ?>">
          </div>
        </div>
        <button class="btn btn-primary w-100 fw-semibold">Create Account</button>
      </form>
      <p class="text-center small mt-3 mb-0">
        Already have an account? <a href="<?= base_url('auth/seeker-login.php') ?>">Sign in</a>
      </p>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
