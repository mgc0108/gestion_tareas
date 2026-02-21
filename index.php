<?php
// index.php
require_once 'config/database.php';
require_once 'src/Models/Tarea.php';

$conexion = conectarDB();
$modelo = new Tarea($conexion);

// --- LÓGICA DE CONTROL (EL CEREBRO) ---

// 1. Añadir Tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $modelo->crear($_POST['titulo'], $_POST['prioridad']);
    header("Location: index.php");
    exit();
}

// 2. Marcar como Completada
if (isset($_GET['completar'])) {
    $modelo->completar($_GET['completar']);
    header("Location: index.php");
    exit();
}

// 3. Eliminar Tarea
if (isset($_GET['eliminar'])) {
    $modelo->borrar($_GET['eliminar']);
    header("Location: index.php");
    exit();
}

$lista_tareas = $modelo->obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tareas Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-3xl mx-auto py-12 px-4">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mi Dashboard de Tareas</h1>
            <p class="text-slate-500 mt-2">Arquitectura MVC: PHP + MySQL + Tailwind</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-xl shadow-slate-200/60 mb-10 border border-slate-100">
            <form method="POST" class="flex flex-col md:flex-row gap-4">
                <input type="text" name="titulo" placeholder="¿Cuál es el siguiente reto?"
                    class="flex-1 bg-slate-50 border-none p-4 rounded-xl focus:ring-2 focus:ring-indigo-500 transition outline-none" required>
                
                <select name="prioridad" class="bg-slate-50 border-none p-4 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium text-slate-600">
                    <option value="Baja">Prioridad Baja</option>
                    <option value="Media" selected>Prioridad Media</option>
                    <option value="Alta">Prioridad Alta</option>
                </select>

                <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 transition-all active:scale-95">
                    Añadir
                </button>
            </form>
        </div>

        <div class="grid gap-4">
            <?php if (empty($lista_tareas)): ?>
                <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                    <p class="text-slate-400 font-medium text-lg">No hay tareas pendientes. ¡Buen trabajo! ☕</p>
                </div>
            <?php endif; ?>

            <?php foreach($lista_tareas as $t): ?>
                <div class="group bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-1.5 h-12 rounded-full
                            <?php echo $t['prioridad'] === 'Alta' ? 'bg-red-500' : ($t['prioridad'] === 'Media' ? 'bg-amber-500' : 'bg-emerald-500'); ?>">
                        </div>
                        
                        <div>
                            <p class="text-lg font-semibold <?php echo $t['estado'] === 'Completada' ? 'text-slate-300 line-through' : 'text-slate-700'; ?>">
                                <?php echo htmlspecialchars($t['titulo']); ?>
                            </p>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-2 py-0.5 bg-slate-50 rounded border border-slate-100">
                                <?php echo $t['prioridad']; ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <?php if ($t['estado'] === 'Pendiente'): ?>
                            <a href="index.php?completar=<?php echo $t['id']; ?>"
                            class="text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white p-2 px-4 rounded-xl font-bold text-sm transition-all">
                                Finalizar
                            </a>
                        <?php else: ?>
                            <span class="text-slate-400 bg-slate-50 p-2 px-4 rounded-xl text-sm font-medium italic border border-slate-100">
                                ¡Hecho!
                            </span>
                        <?php endif; ?>

                        <a href="index.php?eliminar=<?php echo $t['id']; ?>"
                        onclick="return confirm('¿Eliminar esta tarea definitivamente?')"
                        class="text-slate-300 hover:bg-red-50 hover:text-red-500 p-2 rounded-xl transition-all"
                        title="Eliminar tarea">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>