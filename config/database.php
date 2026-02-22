<?php
// config/database.php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct() {
        $this->host = getenv("MYSQL_ADDON_HOST") ?: "127.0.0.1";
        $this->db_name = getenv("MYSQL_ADDON_DB") ?: "planificador_tareas"; // Tu nombre local
        $this->username = getenv("MYSQL_ADDON_USER") ?: "root";
        $this->password = getenv("MYSQL_ADDON_PASSWORD") ?: "";
        $this->port = getenv("MYSQL_ADDON_PORT") ?: "3306";
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
        }
        return $this->conn;
    }
}