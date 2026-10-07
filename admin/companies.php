<?php
$page_title = 'Companies';
require __DIR__ . '/_layout_start.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id) {
    $pdo->prepare('DELETE FROM companies WHERE id=?')->execute([$id]);
    flash('success', 'Company deleted.');
    redirect('admin/companies.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','edit'], true)) {
    if (!verify_csrf($_POST['csrf'] ?? '')) { flash('error','Invalid'); redirect('admin/companies.php'); }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'active');
    $logo = null;
    if ($action==='edit' && $id) {
        $logo = $pdo->prepare('SELECT logo FROM companies WHERE id=?');
        $logo->execute([$id]);
        $logo = $logo->fetchColumn() ?: null;
    }
    if (!empty($_FILES['logo']['tmp_name'])) {
        [$path,$err] = upload_file($_FILES['logo'],'logos',ALLOWED_IMAGES,MAX_IMAGE_SIZE);
        if (!$err) $logo = $path;
    }
    if ($name === '') {
        flash('error','Name required.');
    } else {
        if ($action==='add') {
            $pdo->prepare('INSERT INTO companies (name,email,phone,website,logo,description,location,status) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$name,$email,$phone,$website,$logo,$description,$location,$status]);
            flash('success','Company added.');
        } else {
            $pdo->prepare('UPDATE companies SET name=?,email=?,phone=?,website=?,logo=?,description=?,location=?,status=? WHERE id=?')
                ->execute([$name,$email,$phone,$website,$logo,$description,$location,$status,$id]);
            flash('success','Company updated.');
        }
        redirect('admin/companies.php');
    }
}

if ($action==='add' || ($action==='edit' && $id)) {
    $co = ['name'=>'','email'=>'','phone'=>'','website'=>'','location'=>'','description'=>'','status'=>'active','logo'=>null];
    if ($action==='edit') {
        $st=$pdo->prepare('SELECT * FROM companies WHERE id=?'); $st->execute([$id]); $co=$st->fetch()?:$co;
    }
    ?>
    <h2 class="fw-bold mb-4"><?= $action==='add'?'Add Company':'Edit Company' ?></h2>
    <div class="card border-0 shadow-sm"><div class="card-body p-4">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label fw-semibold">Name *</label><input name="name" class="form-control" required value="<?= e($co['name']) ?>"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input name="email" type="email" class="form-control" value="<?= e($co['email']??'') ?>"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Phone</label><input name="phone" class="form-control" value="<?= e($co['phone']??'') ?>"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Website</label><input name="website" class="form-control" value="<?= e($co['website']??'') ?>"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Location</label><input name="location" class="form-control" value="<?= e($co['location']??'') ?>"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Logo</label><input type="file" name="logo" class="form-control" accept="image/*"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select"><option value="active">Active</option><option value="inactive" <?= ($co['status']??'')==='inactive'?'selected':'' ?>>Inactive</option></select>
          </div>
          <div class="col-12"><label class="form-label fw-semibold">Description</label><textarea name="description" class="form-control" rows="3"><?= e($co['description']??'') ?></textarea></div>
          <div class="col-12"><button class="btn btn-primary">Save</button> <a href="<?= base_url('admin/companies.php') ?>" class="btn btn-outline-secondary">Cancel</a></div>
        </div>
      </form>
    </div></div>
    <?php require __DIR__.'/_layout_end.php'; exit;
}

$list = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM jobs j WHERE j.company_id=c.id) AS job_count FROM companies c ORDER BY c.name')->fetchAll();
?>
<div class="d-flex justify-content-between mb-4">
  <h2 class="fw-bold">Companies</h2>
  <a href="?action=add" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Company</a>
</div>
<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead><tr><th>Name</th><th>Location</th><th>Jobs</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($list as $c): ?>
<tr>
  <td class="fw-semibold"><?= e($c['name']) ?></td>
  <td><?= e($c['location']??'') ?></td>
  <td><?= (int)$c['job_count'] ?></td>
  <td><?= status_badge(ucfirst($c['status']??'active')) ?></td>
  <td>
    <a href="?action=edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
    <a href="?action=delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?php require __DIR__.'/_layout_end.php'; ?>
