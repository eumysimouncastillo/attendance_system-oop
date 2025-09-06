<?php
// admin/courses.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Course.php';

$auth = new Auth();
$auth->requireRole('admin');

$courseModel = new Course();
$errors = [];
$success = null;

// Add or update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $edit_id = (int)($_POST['edit_id'] ?? 0);

    if ($name === '') {
        $errors[] = 'Course name is required.';
    } else {
        if ($edit_id > 0) {
            $courseModel->update($edit_id, $name, $code, $description);
            $success = "Course updated.";
        } else {
            $courseModel->create($name, $code, $description);
            $success = "Course created.";
        }
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $courseModel->delete($id);
    header('Location: /admin/courses.php');
    exit;
}

$editCourse = null;
if (isset($_GET['edit'])) {
    $editCourse = $courseModel->findById((int)$_GET['edit']);
}

$courses = $courseModel->all();
?>

<div class="card">
  <div class="card-body">
    <h4>Courses</h4>

    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif;?>
    <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?></div><?php endif; ?>

    <form method="post" class="mb-4">
      <input type="hidden" name="edit_id" value="<?php echo $editCourse['id'] ?? '' ?>">
      <div class="row">
        <div class="col-md-4 mb-2">
          <input class="form-control" name="name" placeholder="Course name" value="<?php echo htmlspecialchars($editCourse['name'] ?? '') ?>">
        </div>
        <div class="col-md-2 mb-2">
          <input class="form-control" name="code" placeholder="Code" value="<?php echo htmlspecialchars($editCourse['code'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-2">
          <input class="form-control" name="description" placeholder="Description" value="<?php echo htmlspecialchars($editCourse['description'] ?? '') ?>">
        </div>
        <div class="col-md-2 mb-2">
          <button class="btn btn-primary w-100"><?php echo $editCourse ? 'Update' : 'Add' ?></button>
        </div>
      </div>
    </form>

    <table class="table table-striped">
      <thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Description</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($courses as $c): ?>
        <tr>
          <td><?php echo $c['id'] ?></td>
          <td><?php echo htmlspecialchars($c['name']) ?></td>
          <td><?php echo htmlspecialchars($c['code']) ?></td>
          <td><?php echo htmlspecialchars($c['description']) ?></td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="/attendance_system-oop/admin/courses.php?edit=<?php echo $c['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-outline-danger" href="/admin/courses.php?delete=<?php echo $c['id'] ?>" onclick="return confirm('Delete course?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
