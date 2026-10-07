<?php
$page_title = 'Job Seekers';
require __DIR__ . '/_layout_start.php';
$list = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM applications a WHERE a.user_id=u.id) AS apps FROM users u WHERE u.role='seeker' ORDER BY u.created_at DESC")->fetchAll();
?>
<h2 class="fw-bold mb-4">Job Seekers</h2>
<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Location</th><th>Applications</th><th>Joined</th></tr></thead>
<tbody>
<?php foreach ($list as $u): ?>
<tr>
  <td class="fw-semibold"><?= e($u['name']) ?></td>
  <td><?= e($u['email']) ?></td>
  <td><?= e($u['phone']??'') ?></td>
  <td><?= e($u['location']??'') ?></td>
  <td><?= (int)$u['apps'] ?></td>
  <td class="small text-muted"><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$list): ?><tr><td colspan="6" class="text-muted text-center py-4">No job seekers registered yet.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__.'/_layout_end.php'; ?>
