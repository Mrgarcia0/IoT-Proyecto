<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $device->display_name ?? $device->name }} - Paneles</title>
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
                    <a href="{{ route('devices.panels', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-purple-400"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>
                        <span>Paneles</span>
                    </a>
                    <a href="{{ route('devices.home', $device->id) }}" class="mx-auto w-11/12 flex items-center gap-2 px-3 py-2 rounded justify-center hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-green-400"><path d="M12 3l9 8-1.5 1.5L18 10.5V20h-5v-5H11v5H6v-9.5L3.5 12.5 2 11z"/></svg>
                        <span>Casa</span>
                    </a>
                </nav>
            </div>
        </aside>
        <main class="flex-1 p-8">
            <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
            <h1 class="text-2xl font-bold mt-2">Paneles</h1>

            @if(session('status'))
                <div class="mt-3 p-3 bg-green-800/40 border border-green-600 rounded">{{ session('status') }}</div>
            @endif

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <section class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Agregar panel</h2>
                    <form action="{{ route('devices.panels.store', $device->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Variables</label>
                            <div class="flex flex-wrap gap-3">
                                @forelse($variables as $variable)
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" name="variable_names[]" value="{{ $variable }}" class="accent-purple-600">
                                        <span class="text-sm">{{ $variable }}</span>
                                    </label>
                                @empty
                                    <span class="text-sm text-gray-400">No hay variables</span>
                                @endforelse
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Selecciona una o varias (esta sección reemplaza la selección individual).</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Métrica</label>
                            <select name="metric" class="w-full bg-gray-900 border border-gray-700 rounded p-2">
                                <option value="lkv">Último valor conocido (LKV)</option>
                                <option value="max">Máximo</option>
                                <option value="min">Mínimo</option>
                                <option value="avg">Promedio</option>
                                <option value="roc">Tasa de cambio (por hora)</option>
                                <option value="critical_time">Tiempo en estado crítico (horas)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Ventana (días)</label>
                            <input type="number" name="window_days" value="7" min="1" max="365" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                            <p class="text-xs text-gray-400 mt-1">Aplica a Máximo/Mínimo/Promedio/RoC/Tiempo crítico.</p>
                        </div>
                        <div class="thresholds" style="display:none">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1">Umbral mínimo crítico</label>
                                    <input type="number" step="any" name="critical_min" class="w-full bg-gray-900 border border-gray-700 rounded p-2" placeholder="Ej. 15" />
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1">Umbral máximo crítico</label>
                                    <input type="number" step="any" name="critical_max" class="w-full bg-gray-900 border border-gray-700 rounded p-2" placeholder="Ej. 30" />
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Se requiere al menos uno: mínimo o máximo.</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Título (opcional)</label>
                            <input type="text" name="title" placeholder="Ej. Temp. promedio 7 días" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Orden</label>
                            <input type="number" name="position" value="0" min="0" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                        </div>
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 rounded p-2 font-semibold">Crear panel</button>
                    </form>
                </section>

                <section class="lg:col-span-2">
                    <h2 class="text-lg font-semibold mb-4">Paneles existentes</h2>
                    @if($items->isEmpty())
                        <div class="bg-gray-800 rounded p-6 text-gray-400">Aún no hay paneles configurados para este dispositivo.</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($items as $item)
                                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                                    <div class="flex items-center justify-between mb-3">
                                        @php($labelVars = isset($item->variables) && $item->variables ? implode(', ', json_decode($item->variables, true)) : $item->variable_name)
                                        <h3 class="font-semibold">{{ $item->title ?? strtoupper($item->metric) }} • {{ $labelVars }}</h3>
                                        <form action="{{ route('devices.panels.destroy', [$device->id, $item->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar panel?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-400 hover:text-red-300">Eliminar</button>
                                        </form>
                                    </div>
                                    <div>
                                        @php($hasMulti = !empty($item->computed_values) && count($item->computed_values) > 1)
                                        @if($hasMulti)
                                            <ul class="space-y-1">
                                                @foreach($item->computed_values as $var => $val)
                                                    <li class="flex items-baseline justify-between">
                                                        <span class="text-sm text-gray-300">{{ $var }}</span>
                                                        <span class="text-2xl font-bold">
                                                            @if(!is_null($val))
                                                                {{ number_format($val, 2) }}
                                                                <span class="text-base text-gray-400">{{ $item->computed_units[$var] ?? '' }}</span>
                                                            @else
                                                                <span class="text-gray-400 text-base">Sin datos</span>
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-3xl font-bold">
                                                @php($singleVar = array_key_first($item->computed_values ?? []))
                                                @php($singleVal = $singleVar ? ($item->computed_values[$singleVar] ?? null) : null)
                                                @php($singleUnit = $singleVar ? ($item->computed_units[$singleVar] ?? null) : null)
                                                @if(!is_null($singleVal))
                                                    {{ number_format($singleVal, 2) }}
                                                    <span class="text-base text-gray-400">{{ $singleUnit }}</span>
                                                @else
                                                    <span class="text-gray-400">Sin datos</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">Métrica: {{ strtoupper($item->metric) }} • Ventana: {{ $item->window_days }} días</div>
                                    <details class="mt-4">
                                        <summary class="cursor-pointer text-sm text-blue-300">Editar panel</summary>
                                        <form action="{{ route('devices.panels.update', [$device->id, $item->id]) }}" method="POST" class="space-y-2 mt-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Variables</label>
                                                <div class="flex flex-wrap gap-3">
                                                    @foreach($variables as $variable)
                                                        @php($checked = isset($item->variables) && $item->variables ? in_array($variable, json_decode($item->variables, true)) : ($variable === $item->variable_name))
                                                        <label class="inline-flex items-center gap-2">
                                                            <input type="checkbox" name="variable_names[]" value="{{ $variable }}" class="accent-blue-600" @checked($checked)>
                                                            <span class="text-sm">{{ $variable }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Métrica</label>
                                                <select name="metric" class="w-full bg-gray-900 border border-gray-700 rounded p-2">
                                                    <option value="lkv" @selected($item->metric==='lkv')>Último valor conocido (LKV)</option>
                                                    <option value="max" @selected($item->metric==='max')>Máximo</option>
                                                    <option value="min" @selected($item->metric==='min')>Mínimo</option>
                                                    <option value="avg" @selected($item->metric==='avg')>Promedio</option>
                                                    <option value="roc" @selected($item->metric==='roc')>Tasa de cambio (por hora)</option>
                                                    <option value="critical_time" @selected($item->metric==='critical_time')>Tiempo en estado crítico (horas)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Ventana (días)</label>
                                                <input type="number" name="window_days" value="{{ $item->window_days }}" min="1" max="365" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                                            </div>
                                            <div class="thresholds" style="display:none">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-sm text-gray-300 mb-1">Umbral mínimo crítico</label>
                                                        <input type="number" step="any" name="critical_min" value="{{ $item->critical_min }}" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm text-gray-300 mb-1">Umbral máximo crítico</label>
                                                        <input type="number" step="any" name="critical_max" value="{{ $item->critical_max }}" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1">Se requiere al menos uno: mínimo o máximo.</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Título</label>
                                                <input type="text" name="title" value="{{ $item->title }}" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-300 mb-1">Orden</label>
                                                <input type="number" name="position" value="{{ $item->position }}" min="0" class="w-full bg-gray-900 border border-gray-700 rounded p-2" />
                                            </div>
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 rounded p-2 font-semibold">Guardar cambios</button>
                                        </form>
                                    </details>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
            <script>
                document.querySelectorAll('form').forEach(function(form){
                    var metric = form.querySelector('select[name="metric"]');
                    var thresholds = form.querySelector('.thresholds');
                    if(!metric || !thresholds) return;
                    function sync(){
                        var show = metric.value === 'critical_time';
                        thresholds.style.display = show ? '' : 'none';
                    }
                    metric.addEventListener('change', sync);
                    sync();
                });
            </script>
        </main>
    </div>
</body>
</html>