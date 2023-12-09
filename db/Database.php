<?php

namespace iseeyoucopy\phpmvc\db;

use iseeyoucopy\phpmvc\Application;

/**
 * Class Database
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc
 */
class Database
{
    public \PDO $pdo;

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

    private function log($message)
    {
        // Replace with your logging implementation
        // For example, you can use a logging library like Monolog
        echo "[" . date("Y-m-d H:i:s") . "] - " . $message ."<br>". PHP_EOL;
    }

    public function applyMigrations()
    {
        try {
            $this->createMigrationsTable();
            $appliedMigrations = $this->getAppliedMigrations();

            $newMigrations = [];
            $migrationsDir = Application::$ROOT_DIR . '/migrations';
            $files = scandir($migrationsDir);
            $toApplyMigrations = array_diff($files, $appliedMigrations);

            foreach ($toApplyMigrations as $migration) {
                if ($migration === '.' || $migration === '..') {
                    continue;
                }

                require_once $migrationsDir . '/' . $migration;
                $className = pathinfo($migration, PATHINFO_FILENAME);
                $migrationInstance = new $className();

                $this->log("Applying migration $migration");
                $migrationInstance->up();
                $this->log("Applied migration $migration");

                $newMigrations[] = $migration;
            }

            if (!empty($newMigrations)) {
                $this->saveMigrations($newMigrations);
            } else {
                $this->log("There are no migrations to apply");
            }
        } catch (\PDOException $e) {
            $this->handleError($e->getMessage());
            throw $e;
        }
    }

    protected function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
    }

    protected function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    protected function saveMigrations(array $newMigrations)
    {
        $str = implode(',', array_map(fn($m) => "('$m')", $newMigrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES 
            $str
        ");
        $statement->execute();
    }

    public function prepare($sql): \PDOStatement
    {
        try {
            return $this->pdo->prepare($sql);
        } catch (\PDOException $e) {
            $this->handleError($e->getMessage());
            throw $e; // re-throw the exception to propagate it
        }
    }
    private function handleError($errorMessage)
    {
        // Custom error handling logic
        // This method could log the error, send alerts, or display a friendly message
        echo "An error occurred: " . $errorMessage;
        // You can customize this method to handle errors according to your application's needs
    }
}