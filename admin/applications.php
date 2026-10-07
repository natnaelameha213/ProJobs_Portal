<?php
$page_title = 'Applications';
require __DIR__ . '/_layout_start.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['app_id'], $_POST['status']) && verify_csrf($_POST['csrf']??'')) {
    $app_id = (int)$_POST['app_id'];
    $status = $_POST['status'];
    $allowed = ['Pending','Reviewed','Shortlisted','Rejected','Hired'];
    if (in_array($status, $allowed, true)) {
        $pdo->prepare('UPDATE applications SET status=? WHERE id=?')->execute([$status, $app_id]);
        // Notify seeker
        $app = $pdo->prepare('SELECT user_id, candidate_name FROM applications WHERE id=?');
        $app->execute([$app_id]);
        $row = $app->fetch();
        if ($row && $row['user_id']) {
            $pdo->prepare('INSERT INTO notifications (user_id,title,message) VALUES (?,?,?)')
                ->execute([$row['user_id'], 'Application status updated', 'Your application status is now: '.$status]);
        }
        flash('success', 'Status updated to '.$status);
    }
    redirect('admin/applications.php');
}

$list = $pdo->query("
  SELECT a.*, j.title AS job_title, c.name AS company_name
  FROM applications a
  JOIN jobs j ON j.id=a.job_id
  LEFT JOIN companies c ON c.id=j.company_id
  ORDER BY a.applied_at DESC
")->fetchAll();
?>
<h2 class="fw-bold mb-4">Applications</h2>
<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead><tr><th>Candidate</th><th>Job</th><th>Company</th><th>Status</th><th>Date</th><th>Update</th></tr></thead>
<tbody>
<?php foreach ($list as $a): ?>
<tr>
  <td>
    <div class="fw-semibold"><?= e($a['candidate_name']) ?></div>
    <div class="small text-muted"><?= e($a['email']) ?></div>
  </td>
  <td><?= e($a['job_title']) ?></td>
  <td><?= e($a['company_name']??'') ?></td>
  <td><?= status_badge($a['status']) ?></td>
  <td class="small text-muted"><?= e(date('M j, Y', strtotime($a['applied_at']))) ?></td>
  <td>
    <form method="post" class="d-flex gap-1">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="app_id" value="<?= $a['id'] ?>">
      <select name="status" class="form-select form-select-sm" style="width:auto">
        <?php foreach (['Pending','Reviewed','Shortlisted','Rejected','Hired'] as $s): ?>
          <option <?= $a['status']===$s?'selected':'' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-sm btn-primary">Save</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$list): ?><tr><td colspan="6" class="text-muted text-center py-4">No applications yet.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__.'/_layout_end.php'; ?>
