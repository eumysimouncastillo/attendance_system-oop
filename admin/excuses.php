<?php
// admin/excuses.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Excuse.php';

$auth = new Auth();
$auth->requireRole('admin');

$excuseModel = new Excuse();

// If the admin used the page form and posted here directly, we won't process it — all updates go through core/handleforms.php.
// But allow a simple status message display:
$info = '';
if (isset($_GET['updated'])) {
    $info = 'Excuse status updated.';
}

// course filter
$course_id = isset($_GET['course_id']) && $_GET['course_id'] !== '' ? (int)$_GET['course_id'] : null;
$excuses = $excuseModel->listAll($course_id);

// fetch courses via PDO (using model accessor)
$pdo = $excuseModel->getPDO();
$courses = $pdo->query("SELECT * FROM courses ORDER BY name ASC")->fetchAll();
?>
<div class="card">
  <div class="card-body">
    <h4>Manage Excuse Letters</h4>

    <?php if ($info): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($info); ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-3">
      <label class="form-label">Filter by Course</label>
      <select name="course_id" class="form-select w-auto" onchange="this.form.submit()">
        <option value="">All Courses</option>
        <?php foreach ($courses as $c): ?>
          <option value="<?php echo $c['id']; ?>" <?php if ($course_id === (int)$c['id']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($c['name'] . ' (' . $c['code'] . ')'); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Student</th>
          <th>Course</th>
          <th>Reason</th>
          <th>File</th>
          <th>Status</th>
          <th>Admin Comment</th>
          <th>Date Submitted</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($excuses as $ex): ?>
          <tr>
            <td><?php echo htmlspecialchars($ex['full_name']); ?></td>
            <td><?php echo htmlspecialchars($ex['course_name'] . ' (' . $ex['course_code'] . ')'); ?></td>
            <td><?php echo nl2br(htmlspecialchars($ex['reason'])); ?></td>
            <td>
              <?php if (!empty($ex['file_path'])): ?>
                <a href="../<?php echo htmlspecialchars($ex['file_path']); ?>" target="_blank">View PDF</a>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td><?php echo ucfirst($ex['status']); ?></td>
            <td><?php echo htmlspecialchars($ex['admin_comment'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($ex['created_at']); ?></td>
            <td>
              <form method="POST" action="../core/handleforms.php" class="d-flex flex-column gap-2">
                <input type="hidden" name="action" value="update_excuse_status">
                <input type="hidden" name="excuse_id" value="<?php echo $ex['id']; ?>">
                <select name="status" class="form-select form-select-sm">
                  <option value="approved">Approve</option>
                  <option value="rejected">Reject</option>
                </select>
                <input type="text" name="admin_comment" placeholder="Comment (optional)" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-primary mt-1">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
