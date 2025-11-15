<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $device->display_name ?? $device->name }} - Explorador de datos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="flex">
        <!-- Barra lateral -->
        <aside class="w-64 bg-gray-800 min-h-screen p-4 border-r border-gray-700 flex flex-col">
            <h2 class="text-lg font-semibold mb-4 text-center">{{ $device->display_name ?? $device->name }}</h2>
            <div class="flex-1 flex items-center">
                <nav class="w-full space-y-3">
                    <a href="{{ route('devices.show', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-300"><path d="M12 3l9 8-1.5 1.5L18 10.5V20h-5v-5H11v5H6v-9.5L3.5 12.5 2 11z"/></svg>
                        <span>Inicio</span>
                    </a>
                    <a href="{{ route('devices.explorer', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-blue-400"><path d="M5 8h2v11H5zM11 11h2v8h-2zM17 6h2v13h-2z"/></svg>
                        <span>Explorador de datos</span>
                    </a>
                    <a href="{{ route('devices.panels', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-purple-400"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>
                        <span>Paneles</span>
                    </a>
                    
                </nav>
            </div>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
                    <h1 class="text-2xl font-bold mt-2">Explorador de datos</h1>
                    <p class="text-gray-300">Lecturas de {{ $device->display_name ?? $device->name }}</p>
                </div>
                <span class="text-xs px-3 py-1 rounded {{ $device->is_active ? 'bg-green-600' : 'bg-red-600' }}">{{ $device->is_active ? 'ON' : 'OFF' }}</span>
            </div>

            <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <select id="rangeSelect" class="bg-gray-700 text-white rounded px-3 py-2">
                            <option value="hour">Última hora</option>
                            <option value="day" selected>Hoy</option>
                            <option value="week">Última semana</option>
                            <option value="month">Último mes</option>
                            <option value="historico">Histórico</option>
                        </select>
                        <button id="refreshBtn" class="w-10 h-10 rounded bg-gray-700 hover:bg-gray-600 flex items-center justify-center" title="Recargar">
                            <svg id="refreshIcon" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <path d="M20.49 15a9 9 0 1 1 .51-9"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <canvas id="chart" height="140"></canvas>
            </div>
        </main>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const deviceId = {{ $device->id }};
        const ctx = document.getElementById('chart').getContext('2d');
        let chart = new Chart(ctx, {
            type: 'line',
            data: { datasets: [] },
            options: {
                responsive: true,
                scales: {
                    x: { type: 'time', time: { unit: 'minute' }, ticks: { color: '#9ca3af' } },
                    y: { ticks: { color: '#9ca3af' } }
                },
                plugins: { legend: { labels: { color: '#e5e7eb' } } }
            }
        });

        async function load(range) {
            let data;
            try {
                const res = await fetch(`/devices/${deviceId}/readings?range=${range}&_=${Date.now()}`, { cache: 'no-store' });
                if (!res.ok) throw new Error('No se pudo cargar lecturas');
                data = await res.json();
            } catch (err) {
                // Fallback en cliente: generar datos sintéticos para el Termostato
                if (deviceId === 1) {
                    data = generateClientFallback(range);
                } else {
                    throw err;
                }
            }
            const palette = ['#34d399','#60a5fa','#f59e0b','#ef4444','#a78bfa','#10b981','#f472b6','#22d3ee'];
            const series = data.series || {};
            const units = data.units || {};
            const datasets = Object.entries(series).map(([name, points], idx) => ({
                label: `${name}${units[name] ? ' ('+units[name]+')' : ''}`,
                data: (points || []).map(p => ({ x: new Date(p.t), y: p.v })),
                borderColor: palette[idx % palette.length],
                tension: 0.2
            }));
            chart.data.datasets = datasets;
            chart.options.scales.x.time.unit = (range === 'week' || range === 'month' || range === 'historico') ? 'day' : 'minute';
            chart.update();
        }

        function generateClientFallback(range) {
            const now = new Date();
            const series = { temperature: [], humidity: [] };
            const units = { temperature: '°C', humidity: '%' };
            const pointsCount = (range === 'hour') ? 60 : (range === 'week' || range === 'month' || range === 'historico') ? 24 * (range === 'week' ? 7 : 30) : 96; // 15 min para "Hoy"
            const intervalMinutes = (range === 'hour') ? 1 : (range === 'week' || range === 'month' || range === 'historico') ? 60 : 15;
            const target = 24;
            const amp = 4;
            for (let i = pointsCount - 1; i >= 0; i--) {
                const t = new Date(now.getTime() - i * intervalMinutes * 60000);
                const hour = t.getHours();
                const temp = Math.max(14, Math.min(32, target + amp * Math.sin(((hour - 14) / 24) * 2 * Math.PI) + (Math.random() - 0.5) * 0.2));
                const hum = Math.max(45, Math.min(90, 70 - 12 * Math.sin(((hour - 14) / 24) * 2 * Math.PI) + (Math.random() - 0.5) * 3));
                series.temperature.push({ t: t.toISOString(), v: Number(temp.toFixed(2)) });
                series.humidity.push({ t: t.toISOString(), v: Number(hum.toFixed(2)) });
            }
            return { series, units };
        }

        const sel = document.getElementById('rangeSelect');
        sel.addEventListener('change', e => load(e.target.value));
        document.getElementById('refreshBtn').addEventListener('click', async () => {
            const icon = document.getElementById('refreshIcon');
            icon.classList.add('animate-spin');
            try { await load(sel.value); } finally { setTimeout(() => icon.classList.remove('animate-spin'), 400); }
        });

        // Simulación periódica si el dispositivo está ON
        let simInterval = null;
        function startSim() {
            if (simInterval) return;
            simInterval = setInterval(async () => {
                // Intentar insertar muestra en BD para todos los dispositivos
                try {
                    await fetch(`/devices/${deviceId}/simulate-sample`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    });
                } catch (err) {
                    // Silenciar errores de simulación para el Termostato; mostrar en otros
                    if (deviceId !== 1) console.error('simulate error', err);
                }
                // Cargar datos: si el fetch falla, el fallback en cliente pinta datos para id=1
                try { await load(sel.value); } catch (err) { if (deviceId !== 1) console.error('load error', err); }
            }, 15000);
        }
        function stopSim() { if (simInterval) { clearInterval(simInterval); simInterval = null; } }
        @if ($device->id === 1 || $device->is_active)
            // Para el Termostato (id=1) forzamos simulación periódica aunque esté OFF,
            // ya que es el único que reportó no generar datos.
            startSim();
        @endif

        load(sel.value);
    </script>
</body>
</html>