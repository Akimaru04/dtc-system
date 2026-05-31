<?php

class Database {

    private static $instance = null;
    public $conn;

    private $host = "127.0.0.1";
    private $user = "root";
    private $password = "";
    private $database = "dtc_system";

    private function __construct() {
        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database
        );

        if ($this->conn->connect_error) {
            error_log("DB ERROR: " . $this->conn->connect_error);
            die("Database connection failed.");
        }

        $this->conn->set_charset("utf8mb4");
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }
}