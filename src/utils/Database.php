<?php
/*
PROGRAM NAME: Database Connection Manager (Database.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform’s core utility layer.
It is responsible for establishing, managing, and maintaining a secure connection
to the MySQL database used throughout the application.
The Database class centralizes all database access logic and provides a consistent
interface for executing queries, transactions, and data retrieval across different
system modules such as authentication, reviews, sessions, and application workflows.

This class follows the Singleton design pattern to ensure that only one database
connection instance exists during the application lifecycle, improving performance,
resource management, and consistency.

DATE CREATED: December 2, 2025
LAST MODIFIED: December 2, 2025

PURPOSE:
The purpose of this program is to provide a reusable, maintainable, and secure
database access layer for the BuzzarFeed system.
It abstracts PDO connection handling, enforces prepared statements to prevent
SQL injection, and standardizes error handling and transaction management.

By centralizing database logic, this module reduces code duplication and simplifies
future changes to database configuration or connection behavior.

DATA STRUCTURES:
- Static Properties:
  - $instance (Database|null): Holds the singleton instance.
- Instance Properties:
  - $connection (PDO|null): Active PDO database connection.
- PDO Prepared Statements:
  Used for executing parameterized queries safely.

ALGORITHM / LOGIC:
1. Prevent direct instantiation using a private constructor.
2. Prevent cloning and unserialization to preserve Singleton integrity.
3. When `getInstance()` is called:
   a. Check if an instance already exists.
   b. If not, create a new instance and establish a database connection.
4. Build the DSN string using configuration constants.
5. Initialize the PDO connection with:
   a. Exception-based error handling
   b. Associative array fetch mode
   c. Disabled emulated prepares
6. Provide helper methods to:
   a. Execute queries returning multiple rows.
   b. Execute queries returning a single row.
   c. Execute insert, update, and delete operations.
   d. Manage transactions (begin, commit, rollback).
7. Automatically reconnect if the PDO connection is lost.

NOTES:
- This class serves as the foundation for all database-dependent modules in BuzzarFeed.
- Persistent connections are intentionally disabled for better compatibility with
  shared hosting environments.
- Future enhancements may include:
  - Read/write connection separation
  - Query logging and profiling
  - Support for multiple database drivers
  - Automatic reconnection retry strategies
*/

namespace BuzzarFeed\Utils;

use PDO;
use PDOException;

class Database {
    
    /**
     * @var Database|null Singleton instance
     */
    private static ?Database $instance = null;
    
    /**
     * @var PDO|null PDO connection object
     */
    private ?PDO $connection = null;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    /**
     * Get singleton instance
     * 
     * @return Database The singleton instance
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     * 
     * @return void
     * @throws PDOException If connection fails
     */
    private function connect(): void {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            
            if (DEVELOPMENT_MODE) {
                throw $e;
            } else {
                throw new PDOException("Database connection failed");
            }
        }
    }
    
    /**
     * Get the PDO connection object
     * 
     * @return PDO The PDO connection
     */
    public function getConnection(): PDO {
        // Reconnect if connection is lost
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }
    
    /**
     * Execute a query and return results
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array Query results
     */
    public function query(string $query, array $params = []): array {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Execute a query and return single result
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array|null Single result or null
     */
    public function querySingle(string $query, array $params = []): ?array {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Execute an insert, update, or delete query
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return int Number of affected rows
     */
    public function execute(string $query, array $params = []): int {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Execute Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get the last inserted ID
     * 
     * @return string Last insert ID
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool Success status
     */
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool Success status
     */
    public function commit(): bool {
        return $this->connection->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool Success status
     */
    public function rollback(): bool {
        return $this->connection->rollBack();
    }
}
