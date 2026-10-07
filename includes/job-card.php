<?php
/** Expects $job with company_name, category_name, applicant_count optional */
$img = job_image_url($job['image'] ?? null, $job['category_name'] ?? null);
$posted = time_ago($job['created_at'] ?? 'now');
$applicants = $job['applicant_count'] ?? null;
$is_new = !empty($job['created_at']) && (time() - strtotime($job['created_at'])) < 86400 * 3;
?>
<div class="col-12 col-md-6 col-lg-4 col-xl-3">
  <div class="job-card">
    <div class="job-card-img-wrap">
      <img src="<?= e($img) ?>" class="job-card-img" alt="<?= e($job['title']) ?>" loading="lazy">
      <?php if (!empty($job['featured'])): ?>
        <span class="featured-badge">Featured</span>
      <?php elseif ($is_new): ?>
        <span class="badge-new">New</span>
      <?php endif; ?>
    </div>
    <div class="job-card-body">
      <div class="job-card-company">
        <img src="<?= e(company_logo_url($job['company_logo'] ?? null)) ?>" class="company-logo-sm" alt="">
        <span><?= e($job['company_name'] ?? 'Company') ?></span>
      </div>
      <h5 class="job-title">
        <a href="<?= base_url('job-details.php?id=' . (int)$job['id']) ?>"><?= e($job['title']) ?></a>
      </h5>
      <div class="job-meta-row">
        <span><i class="bi bi-geo-alt-fill"></i> <?= e($job['location'] ?? '—') ?></span>
        <span><i class="bi bi-briefcase-fill"></i> <?= e($job['job_type'] ?? 'Full Time') ?></span>
        <span><i class="bi bi-laptop"></i> <?= e($job['work_mode'] ?? 'On-site') ?></span>
      </div>
      <div class="job-salary"><?= e($job['salary'] ?? 'Negotiable') ?></div>
      <p class="job-desc"><?= e(mb_strimwidth($job['short_description'] ?? $job['description'] ?? '', 0, 100, '…')) ?></p>
      <div class="job-card-footer">
        <span><i class="bi bi-clock"></i> <?= e($posted) ?></span>
        <?php if ($applicants !== null): ?>
          <span><i class="bi bi-people"></i> <?= (int)$applicants ?> applicants</span>
        <?php endif; ?>
      </div>
      <div class="job-card-actions">
        <a href="<?= base_url('job-details.php?id=' . (int)$job['id']) ?>" class="btn btn-outline-navy btn-sm">View Details</a>
        <a href="<?= base_url('apply.php?job_id=' . (int)$job['id']) ?>" class="btn btn-orange btn-sm">Apply Now</a>
      </div>
    </div>
  </div>
</div>
