<?php

namespace iseeyoucopy\phpmvc\db;
use PDO;

class DbSessionHandler implements \SessionHandlerInterface
{
    private $pdo;

    public function __construct($dbConfig = [])
    {
        $dbDsn = $dbConfig['dsn'] ?? '';
        $username = $dbConfig['user'] ?? '';
        $password = $dbConfig['password'] ?? '';

        //$this->log("Connecting to database...");

        try {
            $this->pdo = new \PDO($dbDsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $this->log("Failed to connect to database: " . $e->getMessage());
            throw $e;
        }
    }

    public function open(string $savePath, string $sessionName): bool
    {
        // No action needed when the session is opened
        return true;
    }

    public function close(): bool
    {
        // No action needed when the session is closed
        return true;
    }

    public function read(string $sessionKey): string|false
    {
        $stmt = $this->pdo->prepare("SELECT session_value FROM sessions WHERE session_key = :session_key AND session_expiry > :time");
        $stmt->execute(['session_key' => $sessionKey, 'time' => time()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['session_value'] : '';
    }

   public function write(string $sessionKey, string $data): bool
    {
        $expiry = time() + (int) ini_get('session.gc_maxlifetime');
        $stmt = $this->pdo->prepare("REPLACE INTO sessions (session_key, session_value, session_expiry) VALUES (:session_key, :data, :expiry)");
        return $stmt->execute(['session_key' => $sessionKey, 'data' => $data, 'expiry' => $expiry]);
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }


    public function gc(int $max_lifetime): int|false
    {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE last_access < DATE_SUB(NOW(), INTERVAL :maxlifetime SECOND)");
        return $stmt->execute(['maxlifetime' => $maxlifetime]);
    }
}