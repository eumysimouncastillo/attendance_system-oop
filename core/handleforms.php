<?php
// core/handleforms.php
session_start();

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/../models/Excuse.php';
require_once __DIR__ . '/../models/Student.php';

$auth = new Auth();
$excuseModel = new Excuse();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'submit_excuse') {
    // Only students can submit
    $auth->requireRole('student');

    $studentModel = new Student();
    $student = $studentModel->getByUserId((int)$_SESSION['user_id']);
    if (!$student) {
        header("Location: ../student/submit_excuse.php?error=student_not_found");
        exit;
    }

    $reason = trim($_POST['reason'] ?? '');
    if ($reason === '') {
        header("Location: ../student/submit_excuse.php?error=empty_reason");
        exit;
    }

    // Handle optional PDF upload
    $relativePath = null;
    if (!empty($_FILES['file']['name'])) {
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            header("Location: ../student/submit_excuse.php?error=upload_error");
            exit;
        }

        $allowedExt = 'pdf';
        $maxSize = 5 * 1024 * 1024; // 5 MB

        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if ($ext !== $allowedExt) {
            header("Location: ../student/submit_excuse.php?error=invalid_file_type");
            exit;
        }

        if ($_FILES['file']['size'] > $maxSize) {
            header("Location: ../student/submit_excuse.php?error=file_too_large");
            exit;
        }

        $uploadsDir = __DIR__ . '/../uploads/excuses/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['file']['name']));
        $target = $uploadsDir . $safeName;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            header("Location: ../student/submit_excuse.php?error=move_failed");
            exit;
        }

        // store relative path (from project root)
        $relativePath = 'uploads/excuses/' . $safeName;
    }

    $excuseModel->create((int)$student['id'], $reason, $relativePath);
    header("Location: ../student/submit_excuse.php?success=1");
    exit;
}

if ($action === 'update_excuse_status') {
    // Only admins can update
    $auth->requireRole('admin');

    $id = (int)($_POST['excuse_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $comment = trim($_POST['admin_comment'] ?? null);

    $valid = ['approved', 'rejected'];
    if (!in_array($status, $valid, true) || $id <= 0) {
        header("Location: ../admin/excuses.php?error=invalid_input");
        exit;
    }

    $excuseModel->updateStatus($id, $status, $comment);
    header("Location: ../admin/excuses.php?updated=1");
    exit;
}

// if we get here: unknown action
header("Location: ../index.php");
exit;
