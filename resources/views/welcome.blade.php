<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold mb-8">IoT Project Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($devices as $device)
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-2">{{ $device->name }}</h2>
                    <p class="text-gray-400">{{ $device->location }}</p>
                    <div class="mt-4">
                        <span class="inline-block text-sm px-3 py-1 rounded-full {{ $device->is_active ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $device->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="inline-block text-sm px-3 py-1 rounded-full bg-blue-500 ml-2">
                            {{ $device->type }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>