<?php
// student/submit_excuse.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Excuse.php';

$auth = new Auth();
$auth->requireRole('student');

$studentModel = new Student();
$excuseModel  = new Excuse();

$student = $studentModel->getByUserId((int)$_SESSION['user_id']);
if (!$student) {
    die("Student profile not found.");
}

$student_id = (int)$student['id'];
$excuses = $excuseModel->getByStudentId($student_id);

// show simple messages from redirect params
$info = '';
if (isset($_GET['success'])) {
    $info = 'Excuse submitted successfully.';
} elseif (isset($_GET['error'])) {
    // map a few common errors
    $err = $_GET['error'];
    $map = [
        'student_not_found' => 'Student record not found.',
        'empty_reason' => 'Please provide a reason.',
        'upload_error' => 'File upload error.',
        'invalid_file_type' => 'Only PDF files are allowed.',
        'file_too_large' => 'File exceeds maximum size (5MB).',
        'move_failed' => 'Failed to save uploaded file.'
    ];
    $info = $map[$err] ?? 'An error occurred.';
}
?>
<div class="card">
  <div class="card-body">
    <h4>Submit Excuse Letter</h4>

    <?php if ($info): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($info); ?></div>
    <?php endif; ?>

    <form method="POST" action="../core/handleforms.php" enctype="multipart/form-data">
      <input type="hidden" name="action" value="submit_excuse">

      <div class="mb-3">
        <label class="form-label">Reason</label>
        <textarea name="reason" class="form-control" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Attach PDF (optional)</label>
        <input type="file" name="file" accept="application/pdf" class="form-control">
        <div class="form-text">Allowed file type: PDF. Max size: 5MB.</div>
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <hr>

    <h5>Your Submitted Excuses</h5>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Reason</th>
          <th>File</th>
          <th>Status</th>
          <th>Admin Comment</th>
          <th>Date Submitted</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($excuses as $ex): ?>
          <tr>
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
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
