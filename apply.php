<?php
require_once __DIR__ . '/config/config.php';

$job_id = (int)($_GET['job_id'] ?? $_POST['job_id'] ?? 0);
$st = $pdo->prepare("SELECT j.*, c.name AS company_name FROM jobs j LEFT JOIN companies c ON c.id=j.company_id WHERE j.id=? AND j.status='open'");
$st->execute([$job_id]);
$job = $st->fetch();

if (!$job) {
    flash('error', 'Job not found.');
    redirect('jobs.php');
}

$page_title = 'Apply – ' . $job['title'];

// Must be logged in as seeker
if (!is_seeker()) {
    flash('error', 'Please login or create a Job Seeker account to apply.');
    $_SESSION['redirect_after_login'] = 'apply.php?job_id=' . $job_id;
    redirect('auth/seeker-login.php');
}

$user = current_user();

// Already applied?
if (has_applied($pdo, $job_id, $user['email'])) {
    flash('error', 'You have already applied to this job.');
    redirect('job-details.php?id=' . $job_id);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $name = trim($_POST['candidate_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $cover = trim($_POST['cover_letter'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '') $errors[] = 'Full name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';

        $resume_path = $user['cv_path'] ?? null;
        if (!empty($_FILES['resume']['tmp_name'])) {
            [$path, $err] = upload_file($_FILES['resume'], 'cvs', ALLOWED_CV, MAX_CV_SIZE);
            if ($err) $errors[] = $err;
            else $resume_path = $path;
        }

        if (!$errors) {
            try {
                $ins = $pdo->prepare("INSERT INTO applications (job_id, user_id, candidate_name, email, phone, cover_letter, resume, message, status) VALUES (?,?,?,?,?,?,?,?,'Pending')");
                $ins->execute([$job_id, $user['id'], $name, $email, $phone, $cover, $resume_path, $message]);

                // Notification
                $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?,?,?)")
                    ->execute([$user['id'], 'Application submitted', 'Your application for "' . $job['title'] . '" was submitted successfully.']);

                flash('success', 'Application submitted successfully! Track it from your dashboard.');
                redirect('seeker/applications.php');
            } catch (PDOException $ex) {
                if ($ex->getCode() == 23000) {
                    $errors[] = 'You have already applied to this job.';
                } else {
                    $errors[] = 'Could not save application. Please try again.';
                }
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container py-4" style="max-width:720px">
  <h1 class="h3 fw-bold mb-1">Apply for this position</h1>
  <p class="text-muted mb-4"><?= e($job['title']) ?> at <?= e($job['company_name'] ?? '') ?></p>

  <?php if ($errors): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="job_id" value="<?= $job_id ?>">

        <div class="mb-3">
          <label class="form-label fw-semibold">Full Name *</label>
          <input type="text" name="candidate_name" class="form-control" required value="<?= e($_POST['candidate_name'] ?? $user['name']) ?>">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Email *</label>
            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? $user['email']) ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? ($user['phone'] ?? '')) ?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Cover Letter</label>
          <textarea name="cover_letter" class="form-control" rows="5" placeholder="Why are you a great fit for this role?"><?= e($_POST['cover_letter'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">CV / Resume</label>
          <?php if (!empty($user['cv_path'])): ?>
            <div class="small text-muted mb-1">Current CV on file will be used unless you upload a new one.</div>
          <?php endif; ?>
          <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
          <div class="form-text">PDF or Word, max 5MB</div>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Additional Message</label>
          <textarea name="message" class="form-control" rows="2"><?= e($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-orange btn-lg w-100 fw-semibold">Submit Application</button>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
