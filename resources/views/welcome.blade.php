<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold mb-8">IoT Project Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($devices as $device)
                <a href="{{ route('devices.show', $device) }}" class="block bg-gray-800 rounded-lg p-6 transition transform hover:-translate-y-1 hover:shadow-xl cursor-pointer">
                    <h2 class="text-2xl font-bold mb-2">{{ $device->display_name }}</h2>
                    <p class="text-gray-400">{{ $device->location }}</p>
                    <div class="mt-4">
                        <button
                            class="toggle-btn text-sm px-4 py-2 rounded-md font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 {{ $device->is_active ? 'bg-green-600 hover:bg-green-700 focus:ring-green-400' : 'bg-red-600 hover:bg-red-700 focus:ring-red-400' }}"
                            data-device-id="{{ $device->id }}"
                            onclick="event.preventDefault(); event.stopPropagation();"
                        >
                            {{ $device->is_active ? 'ON' : 'OFF' }}
                        </button>
                        <span class="inline-block text-sm px-3 py-1 rounded-full bg-blue-500 ml-2">
                            {{ $device->type }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const simIntervals = {};

        function startSim(id) {
            if (simIntervals[id]) return;
            simIntervals[id] = setInterval(async () => {
                try {
                    await fetch(`/devices/${id}/simulate-sample`, {
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
                } catch (err) { console.error('simulate error', err); }
            }, 15000);
        }

        function stopSim(id) {
            if (simIntervals[id]) {
                clearInterval(simIntervals[id]);
                delete simIntervals[id];
            }
        }
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = btn.dataset.deviceId;
                try {
                    btn.disabled = true;
                    btn.classList.add('opacity-60');
                    const res = await fetch(`/devices/${id}/toggle`, {
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
                    // Actualizar texto
                    btn.textContent = isActive ? 'ON' : 'OFF';
                    // Actualizar estilos
                    const onClasses = ['bg-green-600','hover:bg-green-700','focus:ring-green-400'];
                    const offClasses = ['bg-red-600','hover:bg-red-700','focus:ring-red-400'];
                    onClasses.forEach(c => btn.classList.toggle(c, isActive));
                    offClasses.forEach(c => btn.classList.toggle(c, !isActive));

                    // Iniciar/parar simulación sólo para el Termostato (id=1)
                    if (id === '1') {
                        if (isActive) startSim(id); else stopSim(id);
                    }
                } catch (err) {
                    console.error('Toggle error', err);
                } finally {
                    btn.disabled = false;
                    btn.classList.remove('opacity-60');
                }
            });
        });

        // Iniciar simulación al cargar si el Termostato está ON
        const thermostatBtn = document.querySelector('.toggle-btn[data-device-id="1"]');
        if (thermostatBtn && thermostatBtn.textContent.trim() === 'ON') {
            startSim('1');
        }
    </script>
</body>
</html>