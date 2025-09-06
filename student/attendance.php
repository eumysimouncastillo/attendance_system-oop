<?php
// student/attendance.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Course.php';

$auth = new Auth();
$auth->requireRole('student');

$studentModel = new Student();
$attendanceModel = new Attendance();
$courseModel = new Course();

$student = $studentModel->getByUserId((int)$_SESSION['user_id']);
if (!$student) {
    echo '<div class="alert alert-danger">Student record not found. Contact admin.</div>';
    require_once __DIR__ . '/../partials/footer.php';
    exit;
}

$errors = [];
$success = null;

// File attendance (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_attendance'])) {
    $student_id = (int)$student['id'];
    $course_id = (int)$student['course_id'];
    $date = $_POST['date'] ?? date('Y-m-d');
    $time_in = $_POST['time_in'] ?? date('H:i:s');
    $remarks = trim($_POST['remarks'] ?? '');

    try {
        $attendanceModel->setLateCutoff('08:00:00'); // cutoff example
        $insertId = $attendanceModel->recordAttendance($student_id, $course_id, $date, $time_in, 'present', $remarks);
        $success = "Attendance recorded (id: $insertId).";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// History
$history = $studentModel->getAttendanceHistoryByUserId((int)$_SESSION['user_id']);
$availableCourse = $courseModel->findById((int)$student['course_id']);
?>

<div class="card">
  <div class="card-body">
    <h4>File Attendance</h4>

    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?></div><?php endif; ?>

    <form method="post" class="row g-2 mb-3">
      <div class="col-md-3">
        <label>Date</label>
        <input class="form-control" type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
      </div>
      <div class="col-md-3">
        <label>Time In</label>
        <input class="form-control" type="time" name="time_in" value="<?php echo date('H:i'); ?>">
      </div>
      <div class="col-md-4">
        <label>Course</label>
        <input class="form-control" readonly value="<?php echo htmlspecialchars($availableCourse['name'] ?? 'N/A'); ?>">
      </div>
      <div class="col-md-12">
        <label>Remarks (optional)</label>
        <input class="form-control" name="remarks">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary" name="file_attendance">File Attendance</button>
      </div>
    </form>

    <h5>Attendance History</h5>
    <?php if ($history): ?>
      <table class="table table-striped">
        <thead><tr><th>Date</th><th>Time In</th><th>Course</th><th>Late?</th><th>Remarks</th></tr></thead>
        <tbody>
          <?php foreach ($history as $h): ?>
            <tr>
              <td><?php echo htmlspecialchars($h['date']) ?></td>
              <td><?php echo htmlspecialchars($h['time_in']) ?></td>
              <td><?php echo htmlspecialchars($h['course_name']) ?></td>
              <td><?php echo $h['is_late'] ? '<span class="badge bg-danger">Late</span>' : '<span class="badge bg-success">On time</span>' ?></td>
              <td><?php echo htmlspecialchars($h['remarks']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No attendance history yet.</div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
