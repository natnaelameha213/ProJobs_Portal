<?php
require_once __DIR__ . '/../config/config.php';
require_seeker();
$user = current_user();
$page_title = 'My Dashboard';

$apps = (int)$pdo->prepare('SELECT COUNT(*) FROM applications WHERE user_id=?')->execute([$user['id']]) ? $pdo->query("SELECT COUNT(*) FROM applications WHERE user_id=".(int)$user['id'])->fetchColumn() : 0;
$saved = (int)$pdo->query("SELECT COUNT(*) FROM saved_jobs WHERE user_id=".(int)$user['id'])->fetchColumn();
$pending = (int)$pdo->query("SELECT COUNT(*) FROM applications WHERE user_id=".(int)$user['id']." AND status='Pending'")->fetchColumn();

$recent = $pdo->prepare("
  SELECT a.*, j.title AS job_title, c.name AS company_name
  FROM applications a
  JOIN jobs j ON j.id = a.job_id
  LEFT JOIN companies c ON c.id = j.company_id
  WHERE a.user_id = ?
  ORDER BY a.applied_at DESC LIMIT 5
");
$recent->execute([$user['id']]);
$recent_apps = $recent->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3">
      <?php include __DIR__ . '/_nav.php'; ?>
    </div>
    <div class="col-lg-9">
      <h2 class="fw-bold mb-1">Welcome, <?= e($user['name']) ?></h2>
      <p class="text-muted mb-4">Here's an overview of your job search activity.</p>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card kpi-card p-3">
            <div class="text-muted small">Applications</div>
            <div class="fs-3 fw-bold"><?= $apps ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card kpi-card p-3">
            <div class="text-muted small">Saved Jobs</div>
            <div class="fs-3 fw-bold"><?= $saved ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card kpi-card p-3">
            <div class="text-muted small">Pending</div>
            <div class="fs-3 fw-bold"><?= $pending ?></div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold mb-3">Recent Applications</h5>
          <?php if (empty($recent_apps)): ?>
            <p class="text-muted mb-0">No applications yet. <a href="<?= base_url('jobs.php') ?>">Browse jobs</a></p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Job</th><th>Company</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($recent_apps as $a): ?>
                  <tr>
                    <td><a href="<?= base_url('job-details.php?id='.$a['job_id']) ?>"><?= e($a['job_title']) ?></a></td>
                    <td><?= e($a['company_name'] ?? '') ?></td>
                    <td><?= status_badge($a['status']) ?></td>
                    <td class="small text-muted"><?= e(date('M j, Y', strtotime($a['applied_at']))) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
