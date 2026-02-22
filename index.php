<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';
require_once 'src/Models/Tarea.php';
$tareaModel = new Tarea();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $tareaModel->crear($_POST['titulo'], $_POST['prioridad'], $_POST['fecha_limite'], $_POST['notas'] ?? '');
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) { 
        echo json_encode(['success' => true]); 
        exit; 
    }
    header("Location: index.php"); 
    exit;
}
$tareas = $tareaModel->obtenerTodas();
?>
<!DOCTYPE html>
<html lang="es" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>Planner Elite Pro 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.theme === 'dark') document.documentElement.classList.add('dark')
    </script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background 0.3s; }
        
        .fc-event { border: none !important; border-radius: 8px !important; cursor: grab; padding: 2px 5px; }
        .fc-event:active { cursor: grabbing; }
        .fc-daygrid-day.dia-seleccionado { background-color: rgba(79, 70, 229, 0.1) !important; border: 2px solid #4f46e5 !important; }
        
        .progress-ring__circle {
            transition: stroke-dashoffset 0.8s ease-in-out;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        #modalTarea { display: none; }
        .toast { transform: translateY(100px); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); opacity: 0; }
        .toast.show { transform: translateY(0); opacity: 1; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">

    <div id="toast" class="fixed bottom-8 right-8 z-[110] toast">
        <div class="bg-slate-900 dark:bg-indigo-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/10">
            <span id="toastIcon">✨</span>
            <span id="toastMsg" class="font-bold">Listo</span>
        </div>
    </div>

    <div id="modalTarea" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 w-full max-w-lg shadow-2xl border border-white/10">
            <h2 class="text-2xl font-black mb-6 italic text-slate-400">Nueva Meta: <span id="fechaTextoModal" class="text-indigo-600 not-italic"></span></h2>
            <div class="space-y-4">
                <input type="text" id="taskTitle" placeholder="¿Qué vamos a lograr?" class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-2xl p-4 outline-none focus:ring-2 focus:ring-indigo-500 font-bold text-slate-900 dark:text-white">
                
                <div class="grid grid-cols-2 gap-4">
                    <select id="taskPriority" class="bg-slate-100 dark:bg-slate-800 rounded-2xl p-3 outline-none font-bold text-slate-900 dark:text-white cursor-pointer">
                        <option value="Baja">🟢 Prioridad Baja</option>
                        <option value="Media" selected>🟡 Prioridad Media</option>
                        <option value="Alta">🔴 Prioridad Alta</option>
                    </select>
                    <select id="taskEmoji" class="bg-slate-100 dark:bg-slate-800 rounded-2xl p-3 outline-none text-slate-900 dark:text-white cursor-pointer">
                        <option value="">Icono...</option>
                        <optgroup label="Productividad"><option value="🎯">🎯 Meta</option><option value="💻">💻 Trabajo</option><option value="📚">📚 Estudio</option></optgroup>
                        <optgroup label="Salud"><option value="🏋️">🏋️ Gym</option><option value="🏃">🏃 Deporte</option><option value="🍎">🍎 Dieta</option></optgroup>
                        <optgroup label="Hogar"><option value="🛒">🛒 Compra</option><option value="🧹">🧹 Limpieza</option><option value="🐶">🐶 Mascota</option></optgroup>
                    </select>
                </div>
                <textarea id="taskNotes" placeholder="Notas adicionales..." class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-2xl p-4 outline-none min-h-[80px] text-slate-900 dark:text-white"></textarea>
                <input type="hidden" id="taskDate">
                <div class="flex gap-3 pt-4">
                    <button onclick="cerrarModal()" class="flex-1 py-4 font-bold opacity-50 hover:opacity-100">Cancelar</button>
                    <button onclick="guardarTarea()" class="flex-1 bg-indigo-600 text-white py-4 rounded-3xl font-bold shadow-lg hover:bg-indigo-700 transition active:scale-95">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto py-10 px-6">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-4xl font-black tracking-tighter uppercase">Planner<span class="text-indigo-600">.</span></h1>
            <button onclick="toggleTheme()" class="w-12 h-12 flex items-center justify-center bg-white dark:bg-slate-800 rounded-full shadow-lg border border-slate-200 dark:border-slate-700 transition-all active:scale-90"><span id="themeIcon">🌙</span></button>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl flex items-center justify-between relative overflow-hidden">
                    <div class="z-10">
                        <h2 id="progresoTitulo" class="text-xs font-black uppercase opacity-70 tracking-widest">Progreso Global</h2>
                        <p id="progresoTexto" class="text-lg font-bold mt-1">Cargando...</p>
                    </div>
                    <div class="relative flex items-center justify-center z-10">
                        <svg class="w-24 h-24">
                            <circle class="text-indigo-800" stroke-width="8" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48"/>
                            <circle id="progresoCirculo" class="text-white progress-ring__circle" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="251.2" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48"/>
                        </svg>
                        <span id="progresoPorcentaje" class="absolute text-xl font-black">0%</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 shadow-sm border border-slate-200 dark:border-slate-800 min-h-[400px]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 id="fechaHeader" class="text-xs font-black text-slate-400 uppercase tracking-widest italic">Tareas diarias</h3>
                        <button id="btnAnadir" onclick="abrirModal()" class="hidden w-10 h-10 bg-indigo-600 text-white rounded-full items-center justify-center font-bold text-xl shadow-lg hover:scale-110 transition active:scale-95">+</button>
                    </div>
                    <div id="listaTareasDinamica" class="space-y-4">
                        <p class="text-slate-400 text-center py-20 italic text-sm">Selecciona un día en el calendario.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 shadow-xl border border-slate-200 dark:border-slate-800">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <script>
    let tareasLocal = <?php echo json_encode($tareas); ?>;
    let calendar;
    let fechaSeleccionada = '';

    function showToast(msg, icon = '🚀') {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').innerText = msg;
        document.getElementById('toastIcon').innerText = icon;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function toggleTheme() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.theme = isDark ? 'dark' : 'light';
        document.getElementById('themeIcon').innerText = isDark ? '☀️' : '🌙';
    }

    function cerrarModal() { document.getElementById('modalTarea').style.display='none'; }
    function abrirModal() {
        if (!fechaSeleccionada) return;
        document.getElementById('taskDate').value = fechaSeleccionada;
        document.getElementById('fechaTextoModal').innerText = fechaSeleccionada;
        document.getElementById('modalTarea').style.display = 'flex';
    }

    // CORRECCIÓN: Cálculo de progreso dinámico
    function actualizarProgreso(tareasAFiltrar = null) {
        const lista = tareasAFiltrar || tareasLocal;
        const total = lista.length;
        const completadas = lista.filter(x => x.estado === 'Completada').length;
        const porcentaje = total === 0 ? 0 : Math.round((completadas / total) * 100);
        
        const circulo = document.getElementById('progresoCirculo');
        const circunferencia = 251.2; 
        circulo.style.strokeDashoffset = circunferencia - (porcentaje / 100 * circunferencia);
        
        document.getElementById('progresoPorcentaje').innerText = porcentaje + '%';
        document.getElementById('progresoTitulo').innerText = tareasAFiltrar ? 'Progreso del Día' : 'Progreso Global';
        document.getElementById('progresoTexto').innerText = total === 0 ? 'Sin tareas' : `${completadas} de ${total} finalizadas`;
    }

    function ejecutarAccion(id, accion, estado = '') {
        fetch('api.php', { method: 'POST', body: JSON.stringify({ id, accion, estado }) })
        .then(res => res.json()).then(data => { if (data.success) location.reload(); });
    }

    function guardarTarea() {
        const titulo = document.getElementById('taskTitle').value;
        const emoji = document.getElementById('taskEmoji').value;
        if (!titulo) return;
        let fd = new FormData();
        fd.append('titulo', (emoji + ' ' + titulo).trim()); 
        fd.append('prioridad', document.getElementById('taskPriority').value);
        fd.append('fecha_limite', document.getElementById('taskDate').value); 
        fd.append('notas', document.getElementById('taskNotes').value);
        fetch('index.php', { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} }).then(() => location.reload());
    }

    function filtrarTareasPorFecha(fechaStr) {
        fechaSeleccionada = fechaStr;
        document.getElementById('btnAnadir').classList.replace('hidden', 'flex');
        const filtradas = tareasLocal.filter(t => t.fecha_limite === fechaStr);
        
        // ACTUALIZACIÓN: Reflejar progreso del día seleccionado
        actualizarProgreso(filtradas);

        const contenedor = document.getElementById('listaTareasDinamica');
        contenedor.innerHTML = filtradas.length ? '' : '<p class="text-center text-slate-400 py-20 italic text-sm">Día libre. Pulsa +</p>';
        
        filtradas.forEach(t => {
            const esComp = t.estado === 'Completada';
            const div = document.createElement('div');
            div.className = `p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl flex justify-between items-center border-l-4 transition-all hover:scale-[1.02] ${esComp ? 'border-slate-300 opacity-60' : (t.prioridad === 'Alta' ? 'border-rose-500' : 'border-emerald-400')}`;
            div.innerHTML = `<div class="flex-1 truncate mr-4"><h4 class="font-bold text-sm ${esComp ? 'line-through' : ''}">${t.titulo}</h4></div>
                <div class="flex gap-2">
                    <button onclick="ejecutarAccion(${t.id}, 'completar', '${esComp ? 'Pendiente' : 'Completada'}')" class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-slate-700 shadow-sm hover:bg-indigo-50 transition">${esComp ? '↺' : '✓'}</button>
                    <button onclick="if(confirm('¿Borrar?')) ejecutarAccion(${t.id}, 'borrar')" class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-slate-700 shadow-sm text-rose-500">✕</button>
                </div>`;
            contenedor.appendChild(div);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        actualizarProgreso(); // Inicial: Progreso global
        calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth', locale: 'es', firstDay: 1, buttonText: { today: 'Hoy' }, editable: true, events: 'api.php',
            dateClick: (info) => {
                document.querySelectorAll('.fc-daygrid-day').forEach(el => el.classList.remove('dia-seleccionado'));
                info.dayEl.classList.add('dia-seleccionado');
                filtrarTareasPorFecha(info.dateStr);
            },
            eventDrop: (info) => {
                fetch('api.php', { method: 'POST', body: JSON.stringify({id: info.event.id, nuevaFecha: info.event.startStr}) })
                .then(res => res.json()).then(data => { if (data.success) { showToast('Movido', '📅'); setTimeout(() => location.reload(), 500); } else { info.revert(); } }).catch(() => info.revert());
            }
        });
        calendar.render();
    });
    </script>
</body>
</html>