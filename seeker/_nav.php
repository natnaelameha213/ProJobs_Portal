<div class="card border-0 shadow-sm">
  <div class="card-body p-3">
    <div class="text-center mb-3">
      <img src="<?= e(avatar_url($user['avatar'] ?? null)) ?>" class="rounded-circle mb-2" width="72" height="72" alt="">
      <div class="fw-semibold"><?= e($user['name']) ?></div>
      <div class="small text-muted"><?= e($user['email']) ?></div>
    </div>
    <nav class="nav flex-column seeker-nav">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':'' ?>" href="<?= base_url('seeker/dashboard.php') ?>"><i class="bi bi-house me-2"></i>Dashboard</a>
      <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='profile.php'?'active':'' ?>" href="<?= base_url('seeker/profile.php') ?>"><i class="bi bi-person me-2"></i>Profile</a>
      <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='applications.php'?'active':'' ?>" href="<?= base_url('seeker/applications.php') ?>"><i class="bi bi-file-earmark-text me-2"></i>My Applications</a>
      <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='saved.php'?'active':'' ?>" href="<?= base_url('seeker/saved.php') ?>"><i class="bi bi-bookmark me-2"></i>Saved Jobs</a>
      <a class="nav-link" href="<?= base_url('jobs.php') ?>"><i class="bi bi-search me-2"></i>Browse Jobs</a>
      <a class="nav-link text-danger" href="<?= base_url('auth/logout.php') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </div>
</div>
