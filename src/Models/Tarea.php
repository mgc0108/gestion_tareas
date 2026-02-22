<?php
// src/Models/Tarea.php
class Tarea {
    public function obtenerTodas() {
        $db = conectarDB();
        $sql = "SELECT * FROM tareas ORDER BY estado DESC, fecha_limite ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($titulo, $prioridad, $fecha_limite, $notas = '') {
        $db = conectarDB();
        $sql = "INSERT INTO tareas (titulo, prioridad, fecha_limite, estado, notas) VALUES (?, ?, ?, 'Pendiente', ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$titulo, $prioridad, $fecha_limite, $notas]);
    }

    public function actualizarFecha($id, $nueva_fecha) {
        $db = conectarDB();
        $sql = "UPDATE tareas SET fecha_limite = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nueva_fecha, $id]);
    }

    public function eliminar($id) {
        $db = conectarDB();
        $sql = "DELETE FROM tareas WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function cambiarEstado($id, $estado) {
        $db = conectarDB();
        $sql = "UPDATE tareas SET estado = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$estado, $id]);
    }
}