<?php
// admin/students.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Course.php';

$auth = new Auth();
$auth->requireRole('admin');

$studentModel = new Student();
$courseModel = new Course();

$courses = $courseModel->all();

$course_filter = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$year_filter = isset($_GET['year_level']) ? (int)$_GET['year_level'] : 0;

$allStudents = $studentModel->listAll();
$filtered = array_filter($allStudents, function($s) use ($course_filter, $year_filter) {
    if ($course_filter && $s['course_id'] != $course_filter) return false;
    if ($year_filter && $s['year_level'] != $year_filter) return false;
    return true;
});
?>

<div class="card">
  <div class="card-body">
    <h4>Students</h4>

    <form class="row g-2 mb-3">
      <div class="col-md-4">
        <select class="form-select" name="course_id" onchange="this.form.submit()">
          <option value="0">All courses</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id'] ?>" <?php if ($course_filter == $c['id']) echo 'selected' ?>><?php echo htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="year_level" onchange="this.form.submit()">
          <option value="0">All year levels</option>
          <?php for ($i=1;$i<=6;$i++): ?>
            <option value="<?php echo $i ?>" <?php if ($year_filter == $i) echo 'selected' ?>>Year <?php echo $i ?></option>
          <?php endfor; ?>
        </select>
      </div>
    </form>

    <table class="table table-striped">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th></tr></thead>
      <tbody>
        <?php $i=1; foreach ($filtered as $st): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($st['full_name']) ?></td>
            <td><?php echo htmlspecialchars($st['email']) ?></td>
            <td><?php echo htmlspecialchars($st['course_name'] ?? '') ?></td>
            <td><?php echo htmlspecialchars($st['year_level']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
