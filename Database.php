<?php

declare(strict_types=1);
include_once "prelude.php";

// include database connection settings
require_once PROJECT_ROOT . '/../connectionSettings.php';

class Database
{
    public static ?Database $instance = null;
    public ?mysqli $mysqli = null;

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

}
