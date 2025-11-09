<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $device->display_name ?? $device->name }} - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>.gray-750{background-color:#1d2430}</style>
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-8">
        <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
        <div class="mt-4">
            <h1 class="text-3xl font-bold text-center">{{ $device->display_name ?? $device->name }}</h1>
            <p class="text-gray-300 text-center mt-2">Bienvenido al {{ $device->display_name ?? $device->name }}</p>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="text-gray-300">Estado del sensor</span>
                <span id="status-badge" class="px-3 py-1 rounded text-sm font-semibold {{ $device->is_active ? 'bg-green-600' : 'bg-red-600' }}">{{ $device->is_active ? 'ON' : 'OFF' }}</span>
            </div>
            <div class="mt-6">
                <button id="toggle-btn" class="w-full py-3 rounded-md font-semibold transition {{ $device->is_active ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">{{ $device->is_active ? 'Apagar (OFF)' : 'Encender (ON)' }}</button>
            </div>
        </div>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('devices.explorer', $device->id) }}" class="group bg-gray-800 rounded-lg p-6 shadow-lg hover:bg-gray-750 transition border border-gray-700 hover:border-blue-500">
                <div class="flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 text-blue-400 group-hover:text-blue-300"><path d="M3 3h18v2H3zM5 8h2v11H5zM11 8h2v11h-2zM17 8h2v11h-2z"/></svg>
                    <div class="mt-3 text-lg font-semibold">Explorador de datos</div>
                </div>
            </a>
            <a href="{{ route('devices.panels', $device->id) }}" class="group bg-gray-800 rounded-lg p-6 shadow-lg hover:bg-gray-750 transition border border-gray-700 hover:border-purple-500">
                <div class="flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 text-purple-400 group-hover:text-purple-300"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>
                    <div class="mt-3 text-lg font-semibold">Paneles</div>
                </div>
            </a>
            <a href="{{ route('devices.home', $device->id) }}" class="group bg-gray-800 rounded-lg p-6 shadow-lg hover:bg-gray-750 transition border border-gray-700 hover:border-green-500">
                <div class="flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 text-green-400 group-hover:text-green-300"><path d="M12 3l9 8-1.5 1.5L18 10.5V20h-5v-5H11v5H6v-9.5L3.5 12.5 2 11z"/></svg>
                    <div class="mt-3 text-lg font-semibold">Casa</div>
                </div>
            </a>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const statusBadge = document.getElementById('status-badge');
        toggleBtn.addEventListener('click', async () => {
            toggleBtn.disabled = true;
            try {
                const res = await fetch(`{{ route('devices.toggle', $device->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({}),
                    credentials: 'same-origin',
                    cache: 'no-store'
                });
                const data = await res.json();
                const isActive = !!data.is_active;
                statusBadge.textContent = isActive ? 'ON' : 'OFF';
                statusBadge.className = `px-3 py-1 rounded text-sm font-semibold ${isActive ? 'bg-green-600' : 'bg-red-600'}`;
                toggleBtn.textContent = isActive ? 'Apagar (OFF)' : 'Encender (ON)';
                toggleBtn.className = `w-full py-3 rounded-md font-semibold transition ${isActive ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'}`;
            } catch (e) { console.error(e); }
            finally { toggleBtn.disabled = false; }
        });
    </script>
</body>
</html>