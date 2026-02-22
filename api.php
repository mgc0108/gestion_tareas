<?php
// api.php
require_once 'config/database.php';
require_once 'src/Models/Tarea.php';

header('Content-Type: application/json');
$tareaModel = new Tarea();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $tareas = $tareaModel->obtenerTodas();
    $eventos = [];
    foreach ($tareas as $t) {
        $completada = ($t['estado'] === 'Completada');
        $eventos[] = [
            'id'    => $t['id'],
            'title' => ($completada ? '✓ ' : '') . $t['titulo'],
            'start' => $t['fecha_limite'],
            'description' => $t['notas'] ?? '',
            'color' => $completada ? '#94a3b8' : ($t['prioridad'] === 'Alta' ? '#f43f5e' : ($t['prioridad'] === 'Media' ? '#fbbf24' : '#10b981')),
            'className' => ($completada ? 'opacity-40 ' : '') . ($t['prioridad'] === 'Alta' ? 'pulse-red' : '')
        ];
    }
    echo json_encode($eventos);
} elseif ($metodo === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $response = ['success' => false];

    if (isset($input['accion'])) {
        if ($input['accion'] === 'borrar') $response['success'] = $tareaModel->eliminar($input['id']);
        if ($input['accion'] === 'completar') $response['success'] = $tareaModel->cambiarEstado($input['id'], $input['estado']);
    } elseif (isset($input['id']) && isset($input['nuevaFecha'])) {
        $response['success'] = $tareaModel->actualizarFecha($input['id'], $input['nuevaFecha']);
    }
    echo json_encode($response);
}