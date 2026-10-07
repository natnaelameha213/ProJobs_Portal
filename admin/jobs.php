<?php
$page_title = 'Manage Jobs';
require __DIR__ . '/_layout_start.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Delete
if ($action === 'delete' && $id) {
    $pdo->prepare('DELETE FROM jobs WHERE id=?')->execute([$id]);
    flash('success', 'Job deleted.');
    redirect('admin/jobs.php');
}

// Save (create / update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'], true)) {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid request.');
        redirect('admin/jobs.php');
    }
    $title = trim($_POST['title'] ?? '');
    $company_id = (int)($_POST['company_id'] ?? 0) ?: null;
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $location = trim($_POST['location'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $salary_min = (int)($_POST['salary_min'] ?? 0) ?: null;
    $salary_max = (int)($_POST['salary_max'] ?? 0) ?: null;
    $short_description = trim($_POST['short_description'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $responsibilities = trim($_POST['responsibilities'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $benefits = trim($_POST['benefits'] ?? '');
    $job_type = trim($_POST['job_type'] ?? 'Full Time');
    $work_mode = trim($_POST['work_mode'] ?? 'On-site');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = trim($_POST['status'] ?? 'open');
    $deadline = trim($_POST['deadline'] ?? '') ?: null;

    $image = null;
    if ($action === 'edit' && $id) {
        $ex = $pdo->prepare('SELECT image FROM jobs WHERE id=?');
        $ex->execute([$id]);
        $image = $ex->fetchColumn() ?: null;
    }
    if (!empty($_FILES['image']['tmp_name'])) {
        [$path, $err] = upload_file($_FILES['image'], 'jobs', ALLOWED_IMAGES, MAX_IMAGE_SIZE);
        if ($err) {
            flash('error', $err);
        } else {
            $image = $path;
        }
    }

    if ($title === '') {
        flash('error', 'Job title is required.');
    } else {
        if ($action === 'add') {
            $pdo->prepare("INSERT INTO jobs (company_id,category_id,title,location,salary,salary_min,salary_max,short_description,description,responsibilities,requirements,skills,benefits,job_type,work_mode,image,featured,status,deadline) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$company_id,$category_id,$title,$location,$salary,$salary_min,$salary_max,$short_description,$description,$responsibilities,$requirements,$skills,$benefits,$job_type,$work_mode,$image,$featured,$status,$deadline]);
            flash('success', 'Job published successfully. It is now visible on the public job board.');
        } else {
            $pdo->prepare("UPDATE jobs SET company_id=?,category_id=?,title=?,location=?,salary=?,salary_min=?,salary_max=?,short_description=?,description=?,responsibilities=?,requirements=?,skills=?,benefits=?,job_type=?,work_mode=?,image=?,featured=?,status=?,deadline=? WHERE id=?")
                ->execute([$company_id,$category_id,$title,$location,$salary,$salary_min,$salary_max,$short_description,$description,$responsibilities,$requirements,$skills,$benefits,$job_type,$work_mode,$image,$featured,$status,$deadline,$id]);
            flash('success', 'Job updated successfully.');
        }
        redirect('admin/jobs.php');
    }
}

$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name")->fetchAll();
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

if ($action === 'add' || ($action === 'edit' && $id)) {
    $job = [
        'title'=>'','company_id'=>'','category_id'=>'','location'=>'','salary'=>'','salary_min'=>'','salary_max'=>'',
        'short_description'=>'','description'=>'','responsibilities'=>'','requirements'=>'','skills'=>'','benefits'=>'',
        'job_type'=>'Full Time','work_mode'=>'On-site','featured'=>0,'status'=>'open','deadline'=>'','image'=>null
    ];
    if ($action === 'edit') {
        $st = $pdo->prepare('SELECT * FROM jobs WHERE id=?');
        $st->execute([$id]);
        $job = $st->fetch() ?: $job;
    }
    ?>
    <div class="d-flex justify-content-between mb-4">
      <h2 class="fw-bold"><?= $action==='add'?'Post New Job':'Edit Job' ?></h2>
      <a href="<?= base_url('admin/jobs.php') ?>" class="btn btn-outline-secondary">← Back</a>
    </div>
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Job Title *</label>
              <input type="text" name="title" class="form-control" required value="<?= e($job['title']) ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                <option value="open" <?= ($job['status']??'')==='open'?'selected':'' ?>>Open</option>
                <option value="closed" <?= ($job['status']??'')==='closed'?'selected':'' ?>>Closed</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Company</label>
              <select name="company_id" class="form-select">
                <option value="">— Select —</option>
                <?php foreach ($companies as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= ($job['company_id']??'')==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Category</label>
              <select name="category_id" class="form-select">
                <option value="">— Select —</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= ($job['category_id']??'')==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Location</label>
              <input type="text" name="location" class="form-control" value="<?= e($job['location']??'') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Job Type</label>
              <select name="job_type" class="form-select">
                <?php foreach (['Full Time','Part Time','Contract','Internship','Freelance'] as $t): ?>
                  <option <?= ($job['job_type']??'')===$t?'selected':'' ?>><?= $t ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Work Mode</label>
              <select name="work_mode" class="form-select">
                <?php foreach (['On-site','Remote','Hybrid'] as $m): ?>
                  <option <?= ($job['work_mode']??'')===$m?'selected':'' ?>><?= $m ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Salary (display)</label>
              <input type="text" name="salary" class="form-control" placeholder="ETB 25,000 - 40,000" value="<?= e($job['salary']??'') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Salary Min</label>
              <input type="number" name="salary_min" class="form-control" value="<?= e($job['salary_min']??'') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Salary Max</label>
              <input type="number" name="salary_max" class="form-control" value="<?= e($job['salary_max']??'') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Deadline</label>
              <input type="date" name="deadline" class="form-control" value="<?= e($job['deadline']??'') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Job Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
              <?php if (!empty($job['image'])): ?>
                <div class="mt-2"><img src="<?= e(job_image_url($job['image'])) ?>" height="60" class="rounded"></div>
              <?php endif; ?>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Short Description</label>
              <textarea name="short_description" class="form-control" rows="2" maxlength="500"><?= e($job['short_description']??'') ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Full Description</label>
              <textarea name="description" class="form-control" rows="4"><?= e($job['description']??'') ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Responsibilities</label>
              <textarea name="responsibilities" class="form-control" rows="4"><?= e($job['responsibilities']??'') ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Requirements</label>
              <textarea name="requirements" class="form-control" rows="4"><?= e($job['requirements']??'') ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Skills (comma separated)</label>
              <input type="text" name="skills" class="form-control" value="<?= e($job['skills']??'') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Benefits</label>
              <textarea name="benefits" class="form-control" rows="2"><?= e($job['benefits']??'') ?></textarea>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="featured" value="1" id="feat" <?= !empty($job['featured'])?'checked':'' ?>>
                <label class="form-check-label" for="feat">Featured job (shows on homepage)</label>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary fw-semibold px-4"><?= $action==='add'?'Publish Job':'Update Job' ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php
    require __DIR__ . '/_layout_end.php';
    exit;
}

// List
$jobs = $pdo->query("
  SELECT j.*, c.name AS company_name, cat.name AS category_name,
    (SELECT COUNT(*) FROM applications a WHERE a.job_id=j.id) AS applicants
  FROM jobs j
  LEFT JOIN companies c ON c.id=j.company_id
  LEFT JOIN categories cat ON cat.id=j.category_id
  ORDER BY j.created_at DESC
")->fetchAll();
?>
<div class="d-flex justify-content-between mb-4">
  <div>
    <h2 class="fw-bold">Jobs</h2>
    <p class="text-muted mb-0">Manage all job postings</p>
  </div>
  <a href="?action=add" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Post New Job</a>
</div>
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th></th><th>Title</th><th>Company</th><th>Type</th><th>Status</th><th>Applicants</th><th>Posted</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($jobs as $j): ?>
        <tr>
          <td><img src="<?= e(job_image_url($j['image']??null)) ?>" width="48" height="32" class="rounded object-fit-cover"></td>
          <td>
            <div class="fw-semibold"><?= e($j['title']) ?></div>
            <?php if ($j['featured']): ?><span class="badge text-bg-warning">Featured</span><?php endif; ?>
          </td>
          <td><?= e($j['company_name']??'—') ?></td>
          <td class="small"><?= e($j['job_type']) ?> · <?= e($j['work_mode']) ?></td>
          <td><?= status_badge($j['status']) ?></td>
          <td><?= (int)$j['applicants'] ?></td>
          <td class="small text-muted"><?= e(date('M j, Y', strtotime($j['created_at']))) ?></td>
          <td class="text-nowrap">
            <a href="<?= base_url('job-details.php?id='.$j['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">View</a>
            <a href="?action=edit&id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
            <a href="?action=delete&id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this job?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$jobs): ?><tr><td colspan="8" class="text-muted text-center py-4">No jobs yet. Post your first job!</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/_layout_end.php'; ?>
