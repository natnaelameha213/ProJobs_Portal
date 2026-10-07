<?php
$page_title = 'Categories';
require __DIR__ . '/_layout_start.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf($_POST['csrf']??'')) {
    $name = trim($_POST['name']??'');
    $icon = trim($_POST['icon']??'briefcase');
    if ($name) {
        try {
            $pdo->prepare('INSERT INTO categories (name,icon) VALUES (?,?)')->execute([$name,$icon]);
            flash('success','Category added.');
        } catch (PDOException $e) {
            flash('error','Category already exists.');
        }
    }
    redirect('admin/categories.php');
}
if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([(int)$_GET['delete']]);
    flash('success','Deleted.');
    redirect('admin/categories.php');
}
$list = $pdo->query('SELECT cat.*, COUNT(j.id) AS job_count FROM categories cat LEFT JOIN jobs j ON j.category_id=cat.id GROUP BY cat.id ORDER BY cat.name')->fetchAll();
?>
<div class="d-flex justify-content-between mb-4">
  <h2 class="fw-bold">Categories</h2>
</div>
<div class="row g-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm"><div class="card-body">
      <h6 class="fw-bold">Add Category</h6>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="mb-2"><input name="name" class="form-control" placeholder="Name" required></div>
        <div class="mb-2"><input name="icon" class="form-control" placeholder="Bootstrap icon (e.g. code-slash)" value="briefcase"></div>
        <button class="btn btn-primary btn-sm">Add</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-8">
    <div class="card border-0 shadow-sm"><div class="table-responsive">
      <table class="table mb-0"><thead><tr><th>Name</th><th>Jobs</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($list as $c): ?>
        <tr><td><?= e($c['name']) ?></td><td><?= (int)$c['job_count'] ?></td>
        <td><a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</a></td></tr>
      <?php endforeach; ?>
      </tbody></table>
    </div></div>
  </div>
</div>
<?php require __DIR__.'/_layout_end.php'; ?>
