<?php
// models/Student.php
require_once __DIR__ . '/User.php';

class Student extends User
{
    protected string $studentsTable = 'students';

    /**
     * Register a student user and its student record (course + year)
     * returns student_row (students.id) on success.
     */
    public function register(string $full_name, string $email, string $password, int $course_id, int $year_level): int
    {
        // Create user with role student
        $user_id = $this->create($full_name, $email, $password, 'student');

        $sql = "INSERT INTO {$this->studentsTable} (user_id, course_id, year_level) VALUES (:user_id, :course_id, :year_level)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'course_id' => $course_id,
            'year_level' => $year_level
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getByUserId(int $user_id)
    {
        $sql = "SELECT s.*, u.full_name, u.email, c.name as course_name, c.code as course_code
                FROM {$this->studentsTable} s
                JOIN users u ON u.id = s.user_id
                LEFT JOIN courses c ON c.id = s.course_id
                WHERE s.user_id = :user_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch();
    }

    public function getById(int $student_id)
    {
        $sql = "SELECT s.*, u.full_name, u.email, c.name as course_name, c.code as course_code
                FROM {$this->studentsTable} s
                JOIN users u ON u.id = s.user_id
                LEFT JOIN courses c ON c.id = s.course_id
                WHERE s.id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $student_id]);
        return $stmt->fetch();
    }

    public function listAll()
    {
        $sql = "SELECT s.*, u.full_name, u.email, c.name as course_name, c.code as course_code
                FROM {$this->studentsTable} s
                JOIN users u ON u.id = s.user_id
                LEFT JOIN courses c ON c.id = s.course_id
                ORDER BY u.full_name ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Get attendance history for a student's user_id
     */
    public function getAttendanceHistoryByUserId(int $user_id)
    {
        $student = $this->getByUserId($user_id);
        if (!$student) {
            return [];
        }
        $student_id = $student['id'];

        $sql = "SELECT a.*, c.name as course_name, c.code as course_code
                FROM attendance a
                LEFT JOIN courses c ON c.id = a.course_id
                WHERE a.student_id = :student_id
                ORDER BY a.date DESC, a.time_in DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['student_id' => $student_id]);
        return $stmt->fetchAll();
    }
}
