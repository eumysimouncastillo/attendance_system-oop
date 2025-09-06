<?php
// student/dashboard.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Student.php';

$auth = new Auth();
$auth->requireRole('student');

$studentModel = new Student();
$student = $studentModel->getByUserId((int)$_SESSION['user_id']);

?>

<div class="card">
  <div class="card-body">
    <h4>Student Dashboard</h4>
    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong></p>

    <div class="row">
      <div class="col-md-4">
        <div class="p-3 bg-white border rounded">
          <h6>Course</h6>
          <div><?php echo htmlspecialchars($student['course_name'] ?? 'N/A'); ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-3 bg-white border rounded">
          <h6>Year Level</h6>
          <div><?php echo htmlspecialchars($student['year_level'] ?? 'N/A'); ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-3 bg-white border rounded">
          <h6>Actions</h6>
          <a class="btn btn-primary" href="/attendance_system-oop/student/attendance.php">File Attendance & View History</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
