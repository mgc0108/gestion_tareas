<?php
function conectarDB() {
    // Si estamos en Clever Cloud, estas variables existirán. 
    // Si no, usará los datos de tu XAMPP local.
    $host = getenv('MYSQL_ADDON_HOST') ?: 'localhost';
    $db   = getenv('MYSQL_ADDON_DB') ?: 'gestion_tareas';
    $user = getenv('MYSQL_ADDON_USER') ?: 'root';
    $pass = getenv('MYSQL_ADDON_PASSWORD') ?: '';

    try {
        $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}