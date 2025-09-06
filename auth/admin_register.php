<?php
// auth/admin_register.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../models/Admin.php';

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($full_name === '' || $email === '' || $password === '') {
        $errors[] = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    } else {
        try {
            $admin = new Admin();
            $admin->create($full_name, $email, $password, 'admin');
            $success = true;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Admin Registration</h4>

    <?php if ($success): ?>
      <div class="alert alert-success">Admin registered. <a href="/attendance_system-oop/auth/admin_login.php">Login now</a>.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger"><?php foreach ($errors as $er) echo '<div>' . htmlspecialchars($er) . '</div>'; ?></div>
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

      <button class="btn btn-primary">Register</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
