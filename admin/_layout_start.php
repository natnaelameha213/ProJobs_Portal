<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
if (!isset($page_title)) $page_title = 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($page_title) ?> · Admin · ProJobs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('assets/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= base_url('admin/dashboard.php') ?>"><i class="bi bi-briefcase-fill"></i> ProJobs Admin</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('index.php') ?>" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('auth/logout.php') ?>">Logout</a>
    </div>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
    <aside class="col-lg-2 admin-sidebar p-3 d-none d-lg-block">
      <div class="text-uppercase small text-secondary mb-2 px-2">Menu</div>
      <?php
      $nav = [
        'dashboard.php' => ['Dashboard', 'speedometer2'],
        'jobs.php' => ['Jobs', 'briefcase'],
        'companies.php' => ['Companies', 'building'],
        'categories.php' => ['Categories', 'tags'],
        'applications.php' => ['Applications', 'file-earmark-text'],
        'seekers.php' => ['Job Seekers', 'people'],
      ];
      $cur = basename($_SERVER['PHP_SELF']);
      foreach ($nav as $file => [$label, $icon]):
      ?>
        <a class="nav-link <?= $cur===$file?'active':'' ?>" href="<?= base_url('admin/'.$file) ?>">
          <i class="bi bi-<?= $icon ?>"></i> <?= $label ?>
        </a>
      <?php endforeach; ?>
    </aside>
    <main class="col-lg-10 p-4">
      <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
      <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
