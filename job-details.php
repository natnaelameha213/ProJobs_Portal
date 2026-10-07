<?php
require_once __DIR__ . '/config/config.php';

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("
  SELECT j.*, c.name AS company_name, c.logo AS company_logo, c.description AS company_desc,
         c.website AS company_website, c.location AS company_location, cat.name AS category_name
  FROM jobs j
  LEFT JOIN companies c ON c.id = j.company_id
  LEFT JOIN categories cat ON cat.id = j.category_id
  WHERE j.id = ? AND j.status = 'open'
");
$st->execute([$id]);
$job = $st->fetch();

if (!$job) {
    flash('error', 'Job not found or no longer available.');
    redirect('jobs.php');
}

// Increment views
$pdo->prepare('UPDATE jobs SET views = views + 1 WHERE id = ?')->execute([$id]);

$applicants = get_job_applicant_count($pdo, $id);
$saved = is_seeker() ? is_job_saved($pdo, current_user()['id'], $id) : false;
$skills = array_filter(array_map('trim', explode(',', $job['skills'] ?? '')));
$page_title = $job['title'];

// Handle save/unsave
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_save']) && is_seeker()) {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
    } else {
        if ($saved) {
            $pdo->prepare('DELETE FROM saved_jobs WHERE user_id=? AND job_id=?')->execute([current_user()['id'], $id]);
            flash('success', 'Job removed from saved list.');
        } else {
            $pdo->prepare('INSERT IGNORE INTO saved_jobs (user_id, job_id) VALUES (?,?)')->execute([current_user()['id'], $id]);
            flash('success', 'Job saved successfully.');
        }
    }
    redirect('job-details.php?id=' . $id);
}

require __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('index.php') ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('jobs.php') ?>">Jobs</a></li>
      <li class="breadcrumb-item active"><?= e($job['title']) ?></li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-lg-8">
      <img src="<?= e(job_image_url($job['image'] ?? null)) ?>" class="job-detail-hero mb-4 shadow-sm" alt="">

      <div class="d-flex align-items-start gap-3 mb-3">
        <img src="<?= e(company_logo_url($job['company_logo'] ?? null)) ?>" width="56" height="56" class="rounded-3 border" alt="">
        <div>
          <h1 class="h3 fw-bold mb-1"><?= e($job['title']) ?></h1>
          <div class="text-muted"><?= e($job['company_name'] ?? 'Company') ?> · <?= e($job['location'] ?? '') ?></div>
        </div>
      </div>

      <div class="d-flex flex-wrap gap-2 mb-4">
        <span class="badge bg-primary-subtle text-primary border"><i class="bi bi-briefcase"></i> <?= e($job['job_type']) ?></span>
        <span class="badge bg-info-subtle text-info border"><i class="bi bi-geo"></i> <?= e($job['work_mode']) ?></span>
        <span class="badge bg-secondary-subtle text-secondary border"><?= e($job['category_name'] ?? '') ?></span>
        <?php if ($job['featured']): ?><span class="badge text-bg-warning">Featured</span><?php endif; ?>
      </div>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-bold">About the role</h5>
          <div class="text-secondary"><?= nl2br_safe($job['description'] ?? '') ?></div>
        </div>
      </div>

      <?php if (!empty($job['responsibilities'])): ?>
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-bold">Responsibilities</h5>
          <div class="text-secondary"><?= nl2br_safe($job['responsibilities']) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($job['requirements'])): ?>
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-bold">Requirements</h5>
          <div class="text-secondary"><?= nl2br_safe($job['requirements']) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($skills): ?>
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-bold">Skills</h5>
          <div>
            <?php foreach ($skills as $s): ?>
              <span class="skill-tag"><?= e($s) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($job['benefits'])): ?>
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-bold">Benefits</h5>
          <div class="text-secondary"><?= nl2br_safe($job['benefits']) ?></div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm detail-sidebar">
        <div class="card-body">
          <div class="mb-3">
            <div class="text-muted small">Salary / Budget</div>
            <div class="fs-5 fw-bold text-primary"><?= e($job['salary'] ?: 'Negotiable') ?></div>
          </div>
          <ul class="list-unstyled small mb-4">
            <li class="mb-2"><i class="bi bi-calendar3 text-muted me-2"></i> Posted <?= e(time_ago($job['created_at'])) ?></li>
            <?php if ($job['deadline']): ?>
            <li class="mb-2"><i class="bi bi-hourglass-split text-muted me-2"></i> Deadline <?= e(date('M j, Y', strtotime($job['deadline']))) ?></li>
            <?php endif; ?>
            <li class="mb-2"><i class="bi bi-people text-muted me-2"></i> <?= $applicants ?> applicant<?= $applicants!==1?'s':'' ?></li>
            <li class="mb-2"><i class="bi bi-eye text-muted me-2"></i> <?= (int)$job['views'] ?> views</li>
          </ul>

          <a href="<?= base_url('apply.php?job_id=' . $id) ?>" class="btn btn-orange w-100 mb-2 fw-semibold">
            <i class="bi bi-send"></i> Apply Now
          </a>

          <?php if (is_seeker()): ?>
          <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <button type="submit" name="toggle_save" value="1" class="btn btn-outline-secondary w-100">
              <i class="bi bi-bookmark<?= $saved ? '-fill' : '' ?>"></i>
              <?= $saved ? 'Saved' : 'Save Job' ?>
            </button>
          </form>
          <?php else: ?>
            <a href="<?= base_url('auth/seeker-login.php') ?>" class="btn btn-outline-secondary w-100">
              <i class="bi bi-bookmark"></i> Save Job
            </a>
          <?php endif; ?>

          <hr>
          <div class="small">
            <div class="fw-semibold mb-1"><?= e($job['company_name'] ?? '') ?></div>
            <?php if (!empty($job['company_location'])): ?>
              <div class="text-muted"><i class="bi bi-geo-alt"></i> <?= e($job['company_location']) ?></div>
            <?php endif; ?>
            <?php if (!empty($job['company_website'])): ?>
              <a href="<?= e($job['company_website']) ?>" target="_blank" rel="noopener" class="small">Visit website</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
