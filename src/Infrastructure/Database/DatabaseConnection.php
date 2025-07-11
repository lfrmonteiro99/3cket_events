<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private string $host;
    private string $port;
    private string $database;
    private string $username;
    private string $password;

    public function __construct(
        string $host = 'db',
        string $port = '3306',
        string $database = '3cket_events',
        string $username = '3cket_user',
        string $password = '3cket_password'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    public function createConnection(): PDO
    {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true, // Enable PDO persistent connections
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
            PDO::ATTR_TIMEOUT => 30,
        ];

        try {
            $connection = new PDO($dsn, $this->username, $this->password, $options);

            // Log connection creation for monitoring
            error_log('DatabaseConnection: New PDO persistent connection created for process ' . getmypid());

            return $connection;

        } catch (PDOException $e) {
            throw new PDOException('Connection failed: ' . $e->getMessage(), $e->getCode());
        }
    }

    public static function fromEnvironment(): self
    {
        return new self(
            $_ENV['DB_HOST'] ?? 'db',
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_DATABASE'] ?? '3cket_events',
            $_ENV['DB_USERNAME'] ?? '3cket_user',
            $_ENV['DB_PASSWORD'] ?? '3cket_password'
        );
    }
}
