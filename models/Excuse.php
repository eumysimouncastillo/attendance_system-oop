<?php
// models/Excuse.php
require_once __DIR__ . '/../core/Database.php';

class Excuse extends Database
{
    protected string $table = 'excuse_letters';

    /**
     * Create a new excuse letter
     * returns inserted id
     */
    public function create(int $student_id, string $reason, ?string $file_path = null): int
    {
        $sql = "INSERT INTO {$this->table} (student_id, reason, file_path, status, admin_comment, created_at)
                VALUES (:student_id, :reason, :file_path, 'pending', NULL, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'student_id' => $student_id,
            'reason'     => $reason,
            'file_path'  => $file_path
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Get list of excuses submitted by a student
     */
    public function getByStudentId(int $student_id): array
    {
        $sql = "SELECT e.*, u.full_name, c.name AS course_name, c.code AS course_code
                FROM {$this->table} e
                JOIN students s ON s.id = e.student_id
                JOIN users u ON u.id = s.user_id
                LEFT JOIN courses c ON c.id = s.course_id
                WHERE e.student_id = :student_id
                ORDER BY e.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['student_id' => $student_id]);
        return $stmt->fetchAll();
    }

    /**
     * Admin: list all excuses, optional filter by course_id
     */
    public function listAll(?int $course_id = null): array
    {
        $sql = "SELECT e.*, u.full_name, c.name AS course_name, c.code AS course_code, s.year_level
                FROM {$this->table} e
                JOIN students s ON s.id = e.student_id
                JOIN users u ON u.id = s.user_id
                LEFT JOIN courses c ON c.id = s.course_id";

        $params = [];
        if ($course_id) {
            $sql .= " WHERE s.course_id = :course_id";
            $params['course_id'] = $course_id;
        }
        $sql .= " ORDER BY e.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Update status (approved/rejected) and optional admin comment
     */
    public function updateStatus(int $id, string $status, ?string $comment = null): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = :status,
                    admin_comment = :comment,
                    reviewed_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'status'  => $status,
            'comment' => $comment,
            'id'      => $id
        ]);
    }

    /**
     * Get single excuse by id
     */
    public function getById(int $id)
    {
        $sql = "SELECT e.*, u.full_name, c.name AS course_name, c.code AS course_code
                FROM {$this->table} e
                JOIN students s ON s.id = e.student_id
                JOIN users u ON u.id = s.user_id
                LEFT JOIN courses c ON c.id = s.course_id
                WHERE e.id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Expose PDO in case a page needs to run app-level queries (e.g. fetch courses)
     */
    public function getPDO(): \PDO
    {
        return $this->pdo;
    }
}
