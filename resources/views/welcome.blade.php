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
                    <h2 class="text-2xl font-bold mb-2">{{ $device->name }}</h2>
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
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = btn.dataset.deviceId;
                try {
                    const res = await fetch(`/devices/${id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    const isActive = !!data.is_active;
                    // Actualizar texto
                    btn.textContent = isActive ? 'ON' : 'OFF';
                    // Actualizar estilos
                    const onClasses = ['bg-green-600','hover:bg-green-700','focus:ring-green-400'];
                    const offClasses = ['bg-red-600','hover:bg-red-700','focus:ring-red-400'];
                    onClasses.forEach(c => btn.classList.toggle(c, isActive));
                    offClasses.forEach(c => btn.classList.toggle(c, !isActive));
                } catch (err) {
                    console.error('Toggle error', err);
                }
            });
        });
    </script>
</body>
</html>