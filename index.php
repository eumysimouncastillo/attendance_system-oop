<?php
// index.php
require_once __DIR__ . '/core/Auth.php';

$auth = new Auth();

if ($auth->isLoggedIn()) {
    $role = $_SESSION['role'] ?? '';
    if ($role === 'admin') {
        header('Location: /attendance_system-oop/admin/dashboard.php');
        exit;
    } elseif ($role === 'student') {
        header('Location: /attendance_system-oop/student/dashboard.php');
        exit;
    }
}

// Not logged in: show simple landing
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/navbar.php';
?>

<div class="card">
  <div class="card-body">
    <h4>Welcome to the Attendance System</h4>
    <p>Please login or register using the links in the navigation.</p>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
