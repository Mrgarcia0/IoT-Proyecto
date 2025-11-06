<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Sensor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-8">
        <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
        <h1 class="text-3xl font-bold mt-2 mb-6">{{ $device->name }}</h1>

        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-300"><span class="font-semibold">Ubicación:</span> {{ $device->location ?? '—' }}</p>
                    <p class="text-gray-300 mt-2"><span class="font-semibold">Tipo:</span> {{ $device->type }}</p>
                    <p class="text-gray-300 mt-2"><span class="font-semibold">Estado:</span>
                        <span class="inline-block text-xs px-3 py-1 rounded-full ml-2 {{ $device->is_active ? 'bg-green-600' : 'bg-red-600' }}">
                            {{ $device->is_active ? 'ON' : 'OFF' }}
                        </span>
                    </p>
                </div>
                <div>
                    <form method="POST" action="{{ route('devices.toggle', $device) }}">
                        @csrf
                        <button type="submit" class="mt-2 text-sm px-4 py-2 rounded-md font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 {{ $device->is_active ? 'bg-red-600 hover:bg-red-700 focus:ring-red-400' : 'bg-green-600 hover:bg-green-700 focus:ring-green-400' }}">
                            {{ $device->is_active ? 'Apagar (OFF)' : 'Encender (ON)' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>