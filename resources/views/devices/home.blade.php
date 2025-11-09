<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $device->display_name ?? $device->name }} - Casa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="flex">
        <aside class="w-64 bg-gray-800 min-h-screen p-4 border-r border-gray-700 flex flex-col">
            <h2 class="text-lg font-semibold mb-4 text-center">{{ $device->display_name ?? $device->name }}</h2>
            <div class="flex-1 flex items-center">
                <nav class="w-full space-y-3">
                    <a href="{{ route('devices.show', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-300"><path d="M12 3l9 8-1.5 1.5L18 10.5V20h-5v-5H11v5H6v-9.5L3.5 12.5 2 11z"/></svg>
                        <span>Inicio</span>
                    </a>
                    <a href="{{ route('devices.explorer', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-blue-400"><path d="M5 8h2v11H5zM11 11h2v8h-2zM17 6h2v13h-2z"/></svg>
                        <span>Explorador de datos</span>
                    </a>
                    <a href="{{ route('devices.panels', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-purple-400"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>
                        <span>Paneles</span>
                    </a>
                    <a href="{{ route('devices.home', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-green-400"><path d="M12 3l9 8-1.5 1.5L18 10.5V20h-5v-5H11v5H6v-9.5L3.5 12.5 2 11z"/></svg>
                        <span>Casa</span>
                    </a>
                </nav>
            </div>
        </aside>
        <main class="flex-1 p-8">
            <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
            <h1 class="text-2xl font-bold mt-2">Casa</h1>
            <p class="text-gray-300">Próximamente: integración de dispositivos del hogar.</p>
            <div class="mt-6 bg-gray-800 rounded-lg p-6">
                <div class="text-gray-400">Placeholder de contenido.</div>
            </div>
        </main>
    </div>
</body>
</html>