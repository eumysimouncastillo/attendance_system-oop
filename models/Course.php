<?php
// models/Course.php
require_once __DIR__ . '/../core/Database.php';

class Course extends Database
{
    protected string $table = 'courses';

    public function create(string $name, string $code = null, string $description = null): int
    {
        $sql = "INSERT INTO {$this->table} (name, code, description) VALUES (:name, :code, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'code' => $code,
            'description' => $description
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, string $code = null, string $description = null): bool
    {
        $sql = "UPDATE {$this->table} SET name = :name, code = :code, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findById(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
