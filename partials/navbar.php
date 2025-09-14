<?php
// partials/navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border rounded mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/index.php">Attendance System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto">
        <?php if ($loggedIn && $role === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/admin/dashboard.php">Admin Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/admin/students.php">Students</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/admin/courses.php">Courses</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/admin/attendance.php">Attendance</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/admin/excuses.php">Excuse Letters</a></li>
        <?php elseif ($loggedIn && $role === 'student'): ?>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/student/dashboard.php">Student Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/student/attendance.php">Attendance</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/student/submit_excuse.php">Excuse Letters</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <?php if ($loggedIn): ?>
          <li class="nav-item px-2"><span class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span></li>
          <li class="nav-item"><a class="nav-link btn btn-sm btn-outline-secondary" href="/attendance_system-oop/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/auth/student_login.php">Student Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/auth/student_register.php">Student Register</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/auth/admin_login.php">Admin Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/attendance_system-oop/auth/admin_register.php">Admin Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
