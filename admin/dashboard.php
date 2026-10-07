<?php
$page_title = 'Dashboard';
require __DIR__ . '/_layout_start.php';

$stats = [
  'jobs' => (int)$pdo->query("SELECT COUNT(*) FROM jobs WHERE status='open'")->fetchColumn(),
  'companies' => (int)$pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn(),
  'applications' => (int)$pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn(),
  'seekers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='seeker'")->fetchColumn(),
  'pending' => (int)$pdo->query("SELECT COUNT(*) FROM applications WHERE status='Pending'")->fetchColumn(),
];
$recent = $pdo->query("
  SELECT a.*, j.title AS job_title FROM applications a
  JOIN jobs j ON j.id=a.job_id ORDER BY a.applied_at DESC LIMIT 8
")->fetchAll();
?>
<h2 class="fw-bold">Executive Dashboard</h2>
<p class="text-muted">Real-time business overview</p>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-xl-3"><div class="card kpi-card p-4"><div class="text-muted">Active Jobs</div><div class="fs-3 fw-bold mt-1"><?= $stats['jobs'] ?></div></div></div>
  <div class="col-md-6 col-xl-3"><div class="card kpi-card p-4"><div class="text-muted">Companies</div><div class="fs-3 fw-bold mt-1"><?= $stats['companies'] ?></div></div></div>
  <div class="col-md-6 col-xl-3"><div class="card kpi-card p-4"><div class="text-muted">Applications</div><div class="fs-3 fw-bold mt-1"><?= $stats['applications'] ?></div></div></div>
  <div class="col-md-6 col-xl-3"><div class="card kpi-card p-4"><div class="text-muted">Job Seekers</div><div class="fs-3 fw-bold mt-1"><?= $stats['seekers'] ?></div></div></div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <h5 class="fw-bold mb-3">Recent Applications <span class="badge text-bg-warning"><?= $stats['pending'] ?> pending</span></h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead><tr><th>Candidate</th><th>Job</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($recent as $a): ?>
          <tr>
            <td><?= e($a['candidate_name']) ?></td>
            <td><?= e($a['job_title']) ?></td>
            <td><?= status_badge($a['status']) ?></td>
            <td class="small text-muted"><?= e(date('M j', strtotime($a['applied_at']))) ?></td>
            <td><a href="<?= base_url('admin/applications.php') ?>" class="btn btn-sm btn-outline-primary">Review</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?><tr><td colspan="5" class="text-muted">No applications yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/_layout_end.php'; ?>
