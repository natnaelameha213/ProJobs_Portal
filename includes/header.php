<?php
if (!isset($page_title)) $page_title = APP_NAME;
$current_user = current_user();
$current_page = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> · <?= e(APP_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-projobs sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?= base_url('index.php') ?>">
      <span class="logo-icon"><i class="bi bi-briefcase-fill"></i></span> ProJobs
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?= $current_page==='index.php'?'active':'' ?>" href="<?= base_url('index.php') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $current_page==='jobs.php'?'active':'' ?>" href="<?= base_url('jobs.php') ?>">Jobs</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('jobs.php') ?>#companies">Companies</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php') ?>#categories">Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php') ?>#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('jobs.php') ?>" class="text-white opacity-75 me-1 d-none d-lg-inline" title="Search"><i class="bi bi-search"></i></a>
        <?php if (is_seeker()): ?>
          <a class="btn btn-login btn-sm" href="<?= base_url('seeker/dashboard.php') ?>"><i class="bi bi-person-circle me-1"></i>Dashboard</a>
          <a class="btn btn-register btn-sm" href="<?= base_url('auth/logout.php') ?>">Logout</a>
        <?php elseif (is_admin()): ?>
          <a class="btn btn-login btn-sm" href="<?= base_url('admin/dashboard.php') ?>">Admin</a>
          <a class="btn btn-register btn-sm" href="<?= base_url('auth/logout.php') ?>">Logout</a>
        <?php else: ?>
          <a class="btn btn-login btn-sm" href="<?= base_url('auth/seeker-login.php') ?>">Login</a>
          <a class="btn btn-register btn-sm" href="<?= base_url('auth/seeker-register.php') ?>"><i class="bi bi-person-plus me-1"></i>Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<?php if ($msg = flash('success')): ?>
<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show shadow-sm"><?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
<div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show shadow-sm"><?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
<?php endif; ?>
