<?php
// admin/dashboard.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Attendance.php';

$auth = new Auth();
$auth->requireRole('admin');

$courseModel = new Course();
$studentModel = new Student();
$attendanceModel = new Attendance();

$totalCourses = count($courseModel->all());
$totalStudents = count($studentModel->listAll());
$attendanceTotals = $attendanceModel->getTotals()['total'] ?? 0;
?>

<div class="card">
  <div class="card-body">
    <h4>Admin Dashboard</h4>
    <div class="row mt-3">
      <div class="col-md-4">
        <div class="p-3 bg-white border rounded">
          <h6>Total Courses</h6>
          <strong><?php echo $totalCourses; ?></strong>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-3 bg-white border rounded">
          <h6>Total Students</h6>
          <strong><?php echo $totalStudents; ?></strong>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-3 bg-white border rounded">
          <h6>Total Attendance Records</h6>
          <strong><?php echo $attendanceTotals; ?></strong>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
