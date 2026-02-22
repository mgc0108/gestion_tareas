<?php
// config/database.php
function conectarDB() {
    $host = 'localhost';
    $db   = 'planificador_tareas';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4'; // IMPORTANTE

    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}