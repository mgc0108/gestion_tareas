<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Estas variables las rellena Clever Cloud automáticamente al estar vinculados
        $this->host = getenv("MYSQL_ADDON_HOST") ?: "localhost";
        $this->db_name = getenv("MYSQL_ADDON_DB") ?: "gestion_tareas";
        $this->username = getenv("MYSQL_ADDON_USER") ?: "root";
        $this->password = getenv("MYSQL_ADDON_PASSWORD") ?: "";
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}