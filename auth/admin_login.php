<?php
// auth/admin_login.php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../core/Auth.php';

$auth = new Auth();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = $auth->login($email, $password);
    if ($user) {
        if ($user['role'] !== 'admin') {
            $errors[] = 'This login is for admin only.';
            session_unset();
            session_destroy();
        } else {
            header('Location: /attendance_system-oop/admin/dashboard.php');
            exit;
        }
    } else {
        $errors[] = 'Invalid credentials.';
    }
}
?>

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Admin Login</h4>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger"><?php foreach ($errors as $er) echo '<div>'.htmlspecialchars($er).'</div>'; ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label>Email</label>
        <input class="form-control" type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>
      <button class="btn btn-primary">Login</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
