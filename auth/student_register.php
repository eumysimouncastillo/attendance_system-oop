<?php
// auth/student_register.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Student.php';

$courseModel = new Course();
$courses = $courseModel->all();

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $course_id = (int)($_POST['course_id'] ?? 0);
    $year_level = (int)($_POST['year_level'] ?? 1);

    if ($full_name === '' || $email === '' || $password === '') {
        $errors[] = 'Please fill in all required fields.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    }

    if (empty($errors)) {
        try {
            $studentModel = new Student();
            $student_id = $studentModel->register($full_name, $email, $password, $course_id, $year_level);
            $success = true;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Student Registration</h4>

    <?php if ($success): ?>
      <div class="alert alert-success">Registration successful. You may <a href="/attendance_system-oop/auth/student_login.php">login now</a>.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $er) echo '<div>' . htmlspecialchars($er) . '</div>'; ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label>Full name</label>
        <input class="form-control" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label>Email</label>
        <input class="form-control" type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label>Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>

      <div class="mb-3">
        <label>Course / Program</label>
        <select class="form-select" name="course_id" required>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php if (isset($_POST['course_id']) && $_POST['course_id'] == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Year Level</label>
        <select class="form-select" name="year_level">
          <?php for ($y=1;$y<=4;$y++): ?>
            <option value="<?php echo $y; ?>" <?php if (isset($_POST['year_level']) && $_POST['year_level'] == $y) echo 'selected'; ?>><?php echo $y; ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <button class="btn btn-primary">Register</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
