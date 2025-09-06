<?php
// models/User.php
require_once __DIR__ . '/../core/Database.php';

class User extends Database
{
    protected string $table = 'users';

    public function create(string $full_name, string $email, string $password, string $role = 'student'): int
    {
        // check duplicate
        if ($this->findByEmail($email)) {
            throw new Exception("Email already registered.");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (full_name, email, password, role) VALUES (:full_name, :email, :password, :role)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'full_name' => $full_name,
            'email' => $email,
            'password' => $hash,
            'role' => $role
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findById(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY date_created DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
}
