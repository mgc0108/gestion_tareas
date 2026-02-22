<?php
// src/Models/Tarea.php

// Importamos la clase Database. Asegúrate de que la ruta sea correcta.
require_once __DIR__ . '/../../config/database.php';

class Tarea {
    private $db;

    public function __construct() {
        // Instanciamos la clase y obtenemos la conexión PDO
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerTodas() {
        $sql = "SELECT * FROM tareas ORDER BY estado DESC, fecha_limite ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($titulo, $prioridad, $fecha_limite, $notas = '') {
        $sql = "INSERT INTO tareas (titulo, prioridad, fecha_limite, estado, notas) VALUES (?, ?, ?, 'Pendiente', ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$titulo, $prioridad, $fecha_limite, $notas]);
    }

    public function actualizarFecha($id, $nueva_fecha) {
        $sql = "UPDATE tareas SET fecha_limite = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nueva_fecha, $id]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM tareas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE tareas SET estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$estado, $id]);
    }
}