<?php
// admin/attendance.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Attendance.php';

$auth = new Auth();
$auth->requireRole('admin');

$courseModel = new Course();
$attendanceModel = new Attendance();

$courses = $courseModel->all();

$selected_course = (int)($_GET['course_id'] ?? 0);
$selected_year = (int)($_GET['year_level'] ?? 0);
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;
$rows = [];

if ($selected_course && $selected_year) {
    $rows = $attendanceModel->getAttendanceByCourseAndYear($selected_course, $selected_year, $from, $to);
}
?>

<div class="card">
  <div class="card-body">
    <h4>View Attendance by Course / Year</h4>

    <form class="row g-2 mb-3">
      <div class="col-md-4">
        <select class="form-select" name="course_id">
          <option value="0">Select course</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id'] ?>" <?php if ($selected_course == $c['id']) echo 'selected' ?>><?php echo htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <select class="form-select" name="year_level">
          <option value="0">Select year</option>
          <?php for ($i=1;$i<=4;$i++): ?>
            <option value="<?php echo $i ?>" <?php if ($selected_year == $i) echo 'selected' ?>>Year <?php echo $i ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-2">
        <input class="form-control" type="date" name="from" value="<?php echo htmlspecialchars($from); ?>">
      </div>
      <div class="col-md-2">
        <input class="form-control" type="date" name="to" value="<?php echo htmlspecialchars($to); ?>">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100">Show</button>
      </div>
    </form>

    <?php if ($rows): ?>
      <table class="table table-striped">
        <thead><tr><th>Date</th><th>Student</th><th>Course</th><th>Time In</th><th>Late?</th><th>Remarks</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?php echo htmlspecialchars($r['date']) ?></td>
              <td><?php echo htmlspecialchars($r['full_name']) ?></td>
              <td><?php echo htmlspecialchars($r['course_name']) ?></td>
              <td><?php echo htmlspecialchars($r['time_in']) ?></td>
              <td><?php echo $r['is_late'] ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-success">No</span>' ?></td>
              <td><?php echo htmlspecialchars($r['remarks']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No records to display. Select course and year level to view attendance.</div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
