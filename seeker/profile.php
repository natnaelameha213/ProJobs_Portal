<?php
require_once __DIR__ . '/../config/config.php';
require_seeker();
$user = current_user();
$page_title = 'My Profile';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $skills = trim($_POST['skills'] ?? '');
        $education = trim($_POST['education'] ?? '');
        $experience = trim($_POST['experience'] ?? '');

        $avatar = $user['avatar'];
        $cv = $user['cv_path'];

        if (!empty($_FILES['avatar']['tmp_name'])) {
            [$path, $err] = upload_file($_FILES['avatar'], 'avatars', ALLOWED_IMAGES, MAX_IMAGE_SIZE);
            if ($err) $errors[] = $err; else $avatar = $path;
        }
        if (!empty($_FILES['cv']['tmp_name'])) {
            [$path, $err] = upload_file($_FILES['cv'], 'cvs', ALLOWED_CV, MAX_CV_SIZE);
            if ($err) $errors[] = $err; else $cv = $path;
        }

        if ($name === '') $errors[] = 'Name is required.';

        if (!$errors) {
            $pdo->prepare('UPDATE users SET name=?, phone=?, location=?, bio=?, skills=?, education=?, experience=?, avatar=?, cv_path=? WHERE id=?')
                ->execute([$name, $phone, $location, $bio, $skills, $education, $experience, $avatar, $cv, $user['id']]);
            $s = $pdo->prepare('SELECT * FROM users WHERE id=?');
            $s->execute([$user['id']]);
            $_SESSION['user'] = $s->fetch();
            flash('success', 'Profile updated successfully.');
            redirect('seeker/profile.php');
        }
    }
    $user = array_merge($user, $_POST);
}

require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3"><?php include __DIR__ . '/_nav.php'; ?></div>
    <div class="col-lg-9">
      <h2 class="fw-bold mb-4">Edit Profile</h2>
      <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Location</label>
                <input type="text" name="location" class="form-control" value="<?= e($user['location'] ?? '') ?>">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Bio</label>
              <textarea name="bio" class="form-control" rows="2"><?= e($user['bio'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Skills (comma separated)</label>
              <input type="text" name="skills" class="form-control" value="<?= e($user['skills'] ?? '') ?>" placeholder="PHP, MySQL, JavaScript">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Education</label>
              <textarea name="education" class="form-control" rows="2"><?= e($user['education'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Experience</label>
              <textarea name="experience" class="form-control" rows="2"><?= e($user['experience'] ?? '') ?></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Profile Photo</label>
                <input type="file" name="avatar" class="form-control" accept="image/*">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">CV / Resume</label>
                <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                <?php if (!empty($user['cv_path'])): ?><div class="form-text">Current CV on file</div><?php endif; ?>
              </div>
            </div>
            <button type="submit" class="btn btn-primary fw-semibold">Save Profile</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
