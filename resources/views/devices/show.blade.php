<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Sensor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-8">
        <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
        <h1 class="text-3xl font-bold mt-2 mb-6">{{ $device->display_name }}</h1>

        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-300"><span class="font-semibold">Ubicación:</span> {{ $device->location ?? '—' }}</p>
                    <p class="text-gray-300 mt-2"><span class="font-semibold">Tipo:</span> {{ $device->type }}</p>
                    <p class="text-gray-300 mt-2"><span class="font-semibold">Estado:</span>
                        <span id="stateBadge" class="inline-block text-xs px-3 py-1 rounded-full ml-2 {{ $device->is_active ? 'bg-green-600' : 'bg-red-600' }}">
                            {{ $device->is_active ? 'ON' : 'OFF' }}
                        </span>
                    </p>
                </div>
                <div>
                    <form id="toggleForm" method="POST" action="{{ route('devices.toggle', $device) }}">
                        @csrf
                        <button id="toggleBtnDetail" type="button" class="mt-2 text-sm px-4 py-2 rounded-md font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 {{ $device->is_active ? 'bg-red-600 hover:bg-red-700 focus:ring-red-400' : 'bg-green-600 hover:bg-green-700 focus:ring-green-400' }}">
                            {{ $device->is_active ? 'Apagar (OFF)' : 'Encender (ON)' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Gráfica de lecturas -->
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Lecturas de {{ $device->display_name }}</h2>
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
            <canvas id="chart" height="120"></canvas>
        </div>
    </div>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const deviceId = {{ $device->id }};
        const ctx = document.getElementById('chart').getContext('2d');
        let chart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: []
            },
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
            const res = await fetch(`/devices/${deviceId}/readings?range=${range}&_=${Date.now()}`, { cache: 'no-store' });
            if (!res.ok) throw new Error('No se pudo cargar lecturas');
            const data = await res.json();
            // Soporte genérico: construir datasets a partir de series por variable
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
            // Ajustar unidad del eje X según rango seleccionado
            chart.options.scales.x.time.unit = (range === 'week' || range === 'month' || range === 'historico') ? 'day' : 'minute';
            chart.update();
        }

        const sel = document.getElementById('rangeSelect');
        sel.addEventListener('change', e => load(e.target.value));
        document.getElementById('refreshBtn').addEventListener('click', async () => {
            const icon = document.getElementById('refreshIcon');
            icon.classList.add('animate-spin');
            try { await load(sel.value); } finally {
                setTimeout(() => icon.classList.remove('animate-spin'), 400);
            }
        });

        // Simulación en tiempo real cada 15s mientras el dispositivo esté ON
        let simInterval = null;
        function startSim() {
            if (simInterval) return;
            simInterval = setInterval(async () => {
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
                    // Recargar datos después de insertar nueva muestra
                    await load(sel.value);
                } catch (err) { console.error('simulate error', err); }
            }, 15000);
        }
        function stopSim() {
            if (simInterval) { clearInterval(simInterval); simInterval = null; }
        }
        @if ($device->is_active)
            startSim();
        @endif

        // Interceptar el toggle para hacerlo vía fetch y actualizar UI
        const toggleForm = document.getElementById('toggleForm');
        const toggleBtn = document.getElementById('toggleBtnDetail');
        const stateBadge = document.getElementById('stateBadge');
        toggleBtn.addEventListener('click', async () => {
            toggleBtn.disabled = true;
            toggleBtn.classList.add('opacity-60');
            try {
                const res = await fetch(toggleForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({}),
                    credentials: 'same-origin',
                    cache: 'no-store'
                });
                let data = {};
                try { data = await res.json(); } catch (_) { data = {}; }
                const isActive = !!data.is_active;
                stateBadge.textContent = isActive ? 'ON' : 'OFF';
                stateBadge.classList.toggle('bg-green-600', isActive);
                stateBadge.classList.toggle('bg-red-600', !isActive);
                toggleBtn.textContent = isActive ? 'Apagar (OFF)' : 'Encender (ON)';
                const onClasses = ['bg-red-600','hover:bg-red-700','focus:ring-red-400'];
                const offClasses = ['bg-green-600','hover:bg-green-700','focus:ring-green-400'];
                onClasses.forEach(c => toggleBtn.classList.toggle(c, isActive));
                offClasses.forEach(c => toggleBtn.classList.toggle(c, !isActive));
                if (isActive) { startSim(); } else { stopSim(); }
                await load(sel.value);
            } catch (err) {
                console.error('Toggle error', err);
                alert('No fue posible cambiar el estado. Revisa el servidor.');
            } finally {
                toggleBtn.disabled = false;
                toggleBtn.classList.remove('opacity-60');
            }
        });

        load(sel.value);
    </script>
</body>
</html>