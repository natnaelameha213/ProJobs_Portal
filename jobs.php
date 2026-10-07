<?php
require_once __DIR__ . '/config/config.php';
$page_title = 'Browse Jobs';

$q = trim($_GET['q'] ?? '');
$location = trim($_GET['location'] ?? '');
$category = (int)($_GET['category'] ?? 0);
$job_type = trim($_GET['job_type'] ?? '');
$work_mode = trim($_GET['work_mode'] ?? '');
$featured = (int)($_GET['featured'] ?? 0);
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;

$where = ["j.status = 'open'"];
$params = [];

if ($q !== '') {
    $where[] = "(j.title LIKE ? OR j.description LIKE ? OR j.skills LIKE ? OR c.name LIKE ? OR j.short_description LIKE ?)";
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like);
}
if ($location !== '') {
    $where[] = "j.location LIKE ?";
    $params[] = '%' . $location . '%';
}
if ($category > 0) {
    $where[] = "j.category_id = ?";
    $params[] = $category;
}
if ($job_type !== '') {
    $where[] = "j.job_type = ?";
    $params[] = $job_type;
}
if ($work_mode !== '') {
    $where[] = "j.work_mode = ?";
    $params[] = $work_mode;
}
if ($featured) {
    $where[] = "j.featured = 1";
}

$order = match ($sort) {
    'oldest' => 'j.created_at ASC',
    'salary_asc' => 'j.salary_min ASC',
    'salary_desc' => 'j.salary_max DESC',
    default => 'j.created_at DESC',
};

$where_sql = implode(' AND ', $where);

// Count
$count_sql = "SELECT COUNT(*) FROM jobs j LEFT JOIN companies c ON c.id = j.company_id WHERE $where_sql";
$st = $pdo->prepare($count_sql);
$st->execute($params);
$total = (int)$st->fetchColumn();
$total_pages = max(1, (int)ceil($total / $per_page));
$offset = ($page - 1) * $per_page;

$sql = "
  SELECT j.*, c.name AS company_name, c.logo AS company_logo, cat.name AS category_name,
    (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS applicant_count
  FROM jobs j
  LEFT JOIN companies c ON c.id = j.company_id
  LEFT JOIN categories cat ON cat.id = j.category_id
  WHERE $where_sql
  ORDER BY $order
  LIMIT $per_page OFFSET $offset
";
$st = $pdo->prepare($sql);
$st->execute($params);
$jobs = $st->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$job_types = ['Full Time', 'Part Time', 'Contract', 'Internship', 'Freelance'];
$work_modes = ['On-site', 'Remote', 'Hybrid'];

require __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1">Browse Jobs</h1>
      <p class="text-muted mb-0"><?= $total ?> job<?= $total !== 1 ? 's' : '' ?> found</p>
    </div>
    <form class="d-flex gap-2" method="get">
      <?php foreach ($_GET as $k => $v): if ($k === 'sort' || $k === 'page') continue; ?>
        <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
      <?php endforeach; ?>
      <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto">
        <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest</option>
        <option value="oldest" <?= $sort==='oldest'?'selected':'' ?>>Oldest</option>
        <option value="salary_asc" <?= $sort==='salary_asc'?'selected':'' ?>>Salary: Low to High</option>
        <option value="salary_desc" <?= $sort==='salary_desc'?'selected':'' ?>>Salary: High to Low</option>
      </select>
    </form>
  </div>

  <div class="row g-4">
    <!-- Filters -->
    <div class="col-lg-3">
      <div class="filter-card">
        <h6 class="fw-bold mb-3">Filters</h6>
        <form method="get">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Keyword</label>
            <input type="text" name="q" class="form-control form-control-sm" value="<?= e($q) ?>" placeholder="Title, skill...">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Location</label>
            <input type="text" name="location" class="form-control form-control-sm" value="<?= e($location) ?>" placeholder="City">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Category</label>
            <select name="category" class="form-select form-select-sm">
              <option value="">All Categories</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= $category===$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Job Type</label>
            <select name="job_type" class="form-select form-select-sm">
              <option value="">All Types</option>
              <?php foreach ($job_types as $t): ?>
                <option value="<?= e($t) ?>" <?= $job_type===$t?'selected':'' ?>><?= e($t) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Work Mode</label>
            <select name="work_mode" class="form-select form-select-sm">
              <option value="">All Modes</option>
              <?php foreach ($work_modes as $m): ?>
                <option value="<?= e($m) ?>" <?= $work_mode===$m?'selected':'' ?>><?= e($m) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="featured" value="1" id="feat" <?= $featured?'checked':'' ?>>
            <label class="form-check-label small" for="feat">Featured only</label>
          </div>
          <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filters</button>
          <a href="<?= base_url('jobs.php') ?>" class="btn btn-link btn-sm w-100 mt-1">Clear all</a>
        </form>
      </div>
    </div>

    <!-- Results -->
    <div class="col-lg-9">
      <?php if (empty($jobs)): ?>
        <div class="text-center py-5">
          <i class="bi bi-search display-4 text-muted"></i>
          <h4 class="mt-3">No jobs found</h4>
          <p class="text-muted">Try adjusting your filters or search terms.</p>
          <a href="<?= base_url('jobs.php') ?>" class="btn btn-primary">View all jobs</a>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($jobs as $job) { include __DIR__ . '/includes/job-card.php'; } ?>
        </div>
        <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
          <ul class="pagination justify-content-center">
            <?php
            $qs = $_GET;
            for ($i = 1; $i <= $total_pages; $i++):
              $qs['page'] = $i;
              $url = base_url('jobs.php?' . http_build_query($qs));
            ?>
              <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="<?= e($url) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
          </ul>
        </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
