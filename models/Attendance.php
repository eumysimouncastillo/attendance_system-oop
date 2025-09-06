<?php
// models/Attendance.php
require_once __DIR__ . '/../core/Database.php';

class Attendance extends Database
{
    protected string $table = 'attendance';
    /**
     * Time cutoff for lateness (HH:MM:SS). Can be changed when instantiating or via setter.
     */
    protected string $lateCutoff = '08:00:00';

    public function setLateCutoff(string $time)
    {
        $this->lateCutoff = $time;
    }

    /**
     * Record attendance. Returns inserted id.
     */
    public function recordAttendance(int $student_id, int $course_id, string $date, string $time_in, string $status = 'present', ?string $remarks = null): int
    {
        // Prevent duplicate records for same student + date
        $sqlCheck = "SELECT id FROM {$this->table} WHERE student_id = :student_id AND date = :date LIMIT 1";
        $stmt = $this->pdo->prepare($sqlCheck);
        $stmt->execute(['student_id' => $student_id, 'date' => $date]);
        $existing = $stmt->fetch();
        if ($existing) {
            // update instead of insert
            $is_late = $this->timeIsLate($time_in) ? 1 : 0;
            $sqlUpdate = "UPDATE {$this->table} SET time_in = :time_in, status = :status, is_late = :is_late, remarks = :remarks, course_id = :course_id WHERE id = :id";
            $stmtUp = $this->pdo->prepare($sqlUpdate);
            $stmtUp->execute([
                'time_in' => $time_in,
                'status' => $status,
                'is_late' => $is_late,
                'remarks' => $remarks,
                'course_id' => $course_id,
                'id' => $existing['id']
            ]);
            return (int)$existing['id'];
        }

        $is_late = $this->timeIsLate($time_in) ? 1 : 0;
        $sql = "INSERT INTO {$this->table} (student_id, course_id, date, time_in, status, is_late, remarks) 
                VALUES (:student_id, :course_id, :date, :time_in, :status, :is_late, :remarks)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'student_id' => $student_id,
            'course_id' => $course_id,
            'date' => $date,
            'time_in' => $time_in,
            'status' => $status,
            'is_late' => $is_late,
            'remarks' => $remarks
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    protected function timeIsLate(string $time_in): bool
    {
        return (strtotime($time_in) > strtotime($this->lateCutoff));
    }

    public function getAttendanceByStudentId(int $student_id): array
    {
        $sql = "SELECT a.*, c.name as course_name, c.code as course_code
                FROM {$this->table} a
                LEFT JOIN courses c ON c.id = a.course_id
                WHERE a.student_id = :student_id
                ORDER BY a.date DESC, a.time_in DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['student_id' => $student_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get attendance for a given course + year level (joins students)
     */
    public function getAttendanceByCourseAndYear(int $course_id, int $year_level, ?string $from = null, ?string $to = null): array
    {
        $params = [
            'course_id' => $course_id,
            'year_level' => $year_level
        ];

        $whereDate = "";
        if ($from !== null && $to !== null) {
            $whereDate = " AND a.date BETWEEN :from_date AND :to_date ";
            $params['from_date'] = $from;
            $params['to_date'] = $to;
        }

        $sql = "SELECT a.*, s.year_level, u.full_name, c.name as course_name
                FROM {$this->table} a
                JOIN students s ON s.id = a.student_id
                JOIN users u ON u.id = s.user_id
                LEFT JOIN courses c ON c.id = a.course_id
                WHERE a.course_id = :course_id AND s.year_level = :year_level
                {$whereDate}
                ORDER BY a.date DESC, u.full_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTotals()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        return $this->pdo->query($sql)->fetch();
    }
}
