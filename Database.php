<?php

declare(strict_types=1);
include_once PROJECT_ROOT . '/prelude.php';
include_once 'mysqli';

// include database connection settings
if (DEBUG) {
    require_once PROJECT_ROOT . '/../connectionSettings.php';
} else {
    // define database connection settings for production environment
    // these settings are for production. 
    // developers should instead edit /home/u20xxxxxxx/public_html/connectionSettings.php
    define("DB_HOSTNAME", "localhost");
    define("DB_DATABASE", "db202001264");
    define("DB_USERNAME", "u202001264");
    define("DB_PASSWORD", "u202001264");
}


class Database
{
    /// @var Database|null
    public static $instance = null;
    /// @var mysqli|null
    public $mysqli = null;

    private function __construct()
    {
        if ($this->mysqli == null) {
            // DO NOT EDIT THIS; EDIT IN /home/u20xxxxxxx/public_html/connectionSettings.php
            $this->mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            $this->mysqli->set_charset('utf8mb4');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance == null) {
            return new self();
        }
    }

    public function __descruct(): void
    {
        $this->mysqli->close();
    }

    public function escape(string $string): string
    {
        return $this->mysqli->real_escape_string($string);
    }

    /**
     * @param string $query
     * @return mysqli_result|bool
     */
    public function query(string $sql): object
    {
        $result = $this->mysqli->query($sql);
        if (!$result) {
            die("Error executing query: " . $this->mysqli->error);
        }
        return $result;
    }

    /**
     * Execute prepared SQL statement
     * @param string $query
     * @param string $types
     * @param mixed $params
     * @return bool
     */
    public function pquery(string $query, string $types, ...$params): bool
    {
        $q = $this->mysqli->prepare($query);
        $q->bind_param($types, ...$params);
        return $q->execute();
    }

    /**
     * Execute prepared SQL statement, return insert_id
     * @param string $query
     * @param string $types
     * @param mixed $params
     * @return ?int
     */
    public function pquery_insert(string $query, string $types, ...$params): ?int
    {
        $q = $this->mysqli->prepare($query);
        $q->bind_param($types, ...$params);
        $success = $q->execute();
        if (!$success) {
            return null;
        } else {
            return $q->insert_id;
        }
    }

}