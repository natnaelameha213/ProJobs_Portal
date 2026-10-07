<?php
require_once __DIR__ . '/config/config.php';
$page_title = 'Find Your Next Opportunity';

$featured = $pdo->query("
  SELECT j.*, c.name AS company_name, c.logo AS company_logo, cat.name AS category_name,
    (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS applicant_count
  FROM jobs j
  LEFT JOIN companies c ON c.id = j.company_id
  LEFT JOIN categories cat ON cat.id = j.category_id
  WHERE j.status = 'open' AND j.featured = 1
  ORDER BY j.created_at DESC LIMIT 4
")->fetchAll();

// If fewer than 4 featured, fill with latest open jobs
if (count($featured) < 4) {
  $extra = $pdo->query("
    SELECT j.*, c.name AS company_name, c.logo AS company_logo, cat.name AS category_name,
      (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS applicant_count
    FROM jobs j
    LEFT JOIN companies c ON c.id = j.company_id
    LEFT JOIN categories cat ON cat.id = j.category_id
    WHERE j.status = 'open'
    ORDER BY j.created_at DESC LIMIT 8
  ")->fetchAll();
  $ids = array_column($featured, 'id');
  foreach ($extra as $j) {
    if (!in_array($j['id'], $ids) && count($featured) < 4) $featured[] = $j;
  }
}

$categories = $pdo->query("
  SELECT cat.*, COUNT(j.id) AS job_count
  FROM categories cat
  LEFT JOIN jobs j ON j.category_id = cat.id AND j.status='open'
  GROUP BY cat.id ORDER BY job_count DESC LIMIT 8
")->fetchAll();

$stats = [
  'jobs' => (int)$pdo->query("SELECT COUNT(*) FROM jobs WHERE status='open'")->fetchColumn(),
  'companies' => (int)$pdo->query("SELECT COUNT(*) FROM companies WHERE status='active'")->fetchColumn(),
  'seekers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='seeker'")->fetchColumn(),
];

$cat_icons = [
  'Information Technology' => ['code-slash', 'c1'],
  'Design & Creative' => ['palette', 'c2'],
  'Marketing & Sales' => ['megaphone', 'c3'],
  'Finance & Accounting' => ['graph-up', 'c4'],
  'Engineering' => ['gear', 'c5'],
  'Education' => ['pencil', 'c6'],
  'Customer Service' => ['headset', 'c7'],
  'Healthcare' => ['heart-pulse', 'c8'],
  'Human Resources' => ['people', 'c3'],
  'Logistics & Supply Chain' => ['truck', 'c4'],
];

require __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero-marketplace">
  <div class="container">
    <div class="row align-items-center min-vh-50 py-4 py-lg-0" style="min-height:460px">
      <div class="col-lg-6 py-4">
        <div class="hero-label">Your Next Career Move</div>
        <h1>Find Your Next <span class="accent">Opportunity</span></h1>
        <p class="hero-desc">Discover amazing jobs, grow your skills and build your future with top companies.</p>

        <form action="<?= base_url('jobs.php') ?>" method="get" class="hero-search-box">
          <div class="search-field">
            <i class="bi bi-search"></i>
            <input type="text" name="q" placeholder="Search jobs, skills or companies">
          </div>
          <div class="divider"></div>
          <div class="search-field">
            <i class="bi bi-geo-alt"></i>
            <input type="text" name="location" placeholder="Location">
          </div>
          <button type="submit" class="btn btn-search">Search Jobs</button>
        </form>

        <div class="hero-stats-row">
          <div class="hero-stat-item">
            <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
            <div><strong><?= max($stats['jobs'], 100) ?>+ Jobs</strong>Available Now</div>
          </div>
          <div class="hero-stat-item">
            <div class="stat-icon"><i class="bi bi-building"></i></div>
            <div><strong>Top Companies</strong>Trusted Employers</div>
          </div>
          <div class="hero-stat-item">
            <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
            <div><strong>Apply Easily</strong>Fast & Simple</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 hero-image-col d-none d-lg-flex">
        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=500&fit=crop&crop=faces"
             alt="Professional job seeker"
             style="max-height:400px;border-radius:20px;object-fit:cover;box-shadow:0 25px 60px rgba(0,0,0,0.35)">
        <div class="hero-badge-float">
          <i class="bi bi-lightning-fill"></i> Better Jobs<br><small style="font-weight:500;opacity:0.9">Brighter Future</small>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- POPULAR CATEGORIES -->
<section class="section-pad bg-white" id="categories">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="section-title">Popular <span class="accent">Categories</span></h2>
        <p class="section-sub mb-0">Explore jobs in different fields and find your perfect match.</p>
      </div>
      <a href="<?= base_url('jobs.php') ?>" class="link-orange d-none d-md-inline">View All Categories →</a>
    </div>
    <div class="cat-grid">
      <?php
      $i = 0;
      $colors = ['c1','c2','c3','c4','c5','c6','c7','c8'];
      foreach ($categories as $cat):
        $iconData = $cat_icons[$cat['name']] ?? [($cat['icon'] ?? 'briefcase'), $colors[$i % 8]];
        $i++;
      ?>
      <a class="cat-item" href="<?= base_url('jobs.php?category=' . (int)$cat['id']) ?>">
        <div class="cat-icon <?= e($iconData[1]) ?>"><i class="bi bi-<?= e($iconData[0]) ?>"></i></div>
        <div class="cat-name"><?= e($cat['name']) ?></div>
        <div class="cat-count"><?= (int)$cat['job_count'] ?> Jobs</div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED JOBS -->
<section class="section-pad">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="section-title"><i class="bi bi-fire text-danger"></i> Featured <span class="accent">Jobs</span></h2>
        <p class="section-sub mb-0">Top opportunities from leading companies.</p>
      </div>
      <a href="<?= base_url('jobs.php') ?>" class="link-orange d-none d-md-inline">View All Jobs →</a>
    </div>
    <div class="row g-4">
      <?php foreach ($featured as $job) { include __DIR__ . '/includes/job-card.php'; } ?>
    </div>
    <?php if (empty($featured)): ?>
      <div class="text-center py-5 text-muted">No featured jobs yet. Check back soon!</div>
    <?php endif; ?>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section" id="about">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="section-title">How It <span class="accent">Works</span></h2>
      <p class="section-sub">Get your dream job in just a few simple steps.</p>
    </div>
    <div class="row g-4">
      <div class="col-6 col-md-3">
        <div class="how-step">
          <div class="how-step-num">1</div>
          <h6>Create Account</h6>
          <p>Sign up for free</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="how-step">
          <div class="how-step-num" style="background:#2563EB">2</div>
          <h6>Browse Jobs</h6>
          <p>Find the right opportunity</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="how-step">
          <div class="how-step-num">3</div>
          <h6>Apply</h6>
          <p>Submit your application</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="how-step">
          <div class="how-step-num">4</div>
          <h6>Get Hired</h6>
          <p>Start your new journey</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
