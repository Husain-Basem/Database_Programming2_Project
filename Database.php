<?php

declare(strict_types=1);
include_once PROJECT_ROOT . '/prelude.php';

// include database connection settings
require_once PROJECT_ROOT . '/../connectionSettings.php';

class Database
{
    /// @var Database|null
    public static $instance = null;
    /// @var mysqli|null
    public $mysqli = null;

    private function __construct()
    {
        if ($this->mysqli == null) {
            $this->mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
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
    /**
     * @param string $query
     * @return mysqli_result|bool
     */
    public function query(string $query): object
    {
        return $this->mysqli->query($query);
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

}
