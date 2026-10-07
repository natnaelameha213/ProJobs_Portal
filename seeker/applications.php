<?php
require_once __DIR__ . '/../config/config.php';
require_seeker();
$user = current_user();
$page_title = 'My Applications';

$st = $pdo->prepare("
  SELECT a.*, j.title AS job_title, j.location, c.name AS company_name
  FROM applications a
  JOIN jobs j ON j.id = a.job_id
  LEFT JOIN companies c ON c.id = j.company_id
  WHERE a.user_id = ?
  ORDER BY a.applied_at DESC
");
$st->execute([$user['id']]);
$apps = $st->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3"><?php include __DIR__ . '/_nav.php'; ?></div>
    <div class="col-lg-9">
      <h2 class="fw-bold mb-4">My Applications</h2>
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <?php if (empty($apps)): ?>
            <p class="text-muted">You haven't applied to any jobs yet. <a href="<?= base_url('jobs.php') ?>">Find jobs</a></p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Job</th><th>Company</th><th>Location</th><th>Status</th><th>Applied</th></tr></thead>
                <tbody>
                <?php foreach ($apps as $a): ?>
                  <tr>
                    <td><a href="<?= base_url('job-details.php?id='.$a['job_id']) ?>"><?= e($a['job_title']) ?></a></td>
                    <td><?= e($a['company_name'] ?? '') ?></td>
                    <td><?= e($a['location'] ?? '') ?></td>
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
