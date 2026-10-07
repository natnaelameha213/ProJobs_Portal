<?php
require_once __DIR__ . '/../config/config.php';
require_seeker();
$user = current_user();
$page_title = 'Saved Jobs';

if (isset($_GET['remove'])) {
    $jid = (int)$_GET['remove'];
    $pdo->prepare('DELETE FROM saved_jobs WHERE user_id=? AND job_id=?')->execute([$user['id'], $jid]);
    flash('success', 'Job removed from saved list.');
    redirect('seeker/saved.php');
}

$st = $pdo->prepare("
  SELECT j.*, c.name AS company_name, c.logo AS company_logo, cat.name AS category_name,
    (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS applicant_count
  FROM saved_jobs sj
  JOIN jobs j ON j.id = sj.job_id
  LEFT JOIN companies c ON c.id = j.company_id
  LEFT JOIN categories cat ON cat.id = j.category_id
  WHERE sj.user_id = ?
  ORDER BY sj.saved_at DESC
");
$st->execute([$user['id']]);
$jobs = $st->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3"><?php include __DIR__ . '/_nav.php'; ?></div>
    <div class="col-lg-9">
      <h2 class="fw-bold mb-4">Saved Jobs</h2>
      <?php if (empty($jobs)): ?>
        <div class="card border-0 shadow-sm"><div class="card-body text-muted">No saved jobs yet. <a href="<?= base_url('jobs.php') ?>">Browse jobs</a></div></div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($jobs as $job): ?>
            <?php include __DIR__ . '/../includes/job-card.php'; ?>
            <div class="col-12 text-end mt-n3 mb-2">
              <a href="?remove=<?= (int)$job['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this saved job?')">Remove</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
