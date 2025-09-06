<?php
// core/Database.php
require_once __DIR__ . '/../config/db_config.php';

class Database
{
    protected ?PDO $pdo = null;

    public function __construct()
    {
        if ($this->pdo) {
            return;
        }

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // In production, don't echo exceptions. For dev, helpful:
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
