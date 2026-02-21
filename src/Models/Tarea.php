<?php
// src/Models/Tarea.php

class Tarea {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Función para leer todas las tareas de la base de datos
    public function obtenerTodas() {
        $query = "SELECT * FROM tareas ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Función para insertar una tarea nueva
    public function crear($titulo, $prioridad) {
        $query = "INSERT INTO tareas (titulo, prioridad, estado) VALUES (:titulo, :prioridad, 'Pendiente')";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':titulo' => $titulo,
            ':prioridad' => $prioridad
        ]);
    }
    // Función para cambiar el estado de una tarea a Completada
public function completar($id) {
    $query = "UPDATE tareas SET estado = 'Completada' WHERE id = :id";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([':id' => $id]);
}
// Función para eliminar definitivamente una tarea
public function borrar($id) {
    $query = "DELETE FROM tareas WHERE id = :id";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([':id' => $id]);
}
}
?>