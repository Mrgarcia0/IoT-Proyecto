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
                    
                </nav>
            </div>
        </aside>
        <main class="flex-1 p-8">
            <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
            <h1 class="text-2xl font-bold mt-2">Casa</h1>
            @if(session('status'))
                <div class="mt-3 p-3 bg-green-800/40 border border-green-600 rounded">{{ session('status') }}</div>
            @endif

            <!-- Lobby: tarjetas de sensores del dispositivo -->
            <section class="mt-6 bg-gray-800 rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Lobby</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($variables ?? [] as $var)
                        @php($last = \App\Models\SensorReading::where('device_id',$device->id)->where('variable_name',$var)->orderByDesc('recorded_at')->first())
                        <div class="bg-gray-900 border border-gray-700 rounded p-4">
                            <div class="text-sm text-gray-400">{{ $var }}</div>
                            <div class="text-3xl font-bold mt-1">
                                @if($last)
                                    {{ number_format($last->value, 2) }} <span class="text-base text-gray-400">{{ $last->unit }}</span>
                                @else
                                    <span class="text-gray-500">Sin datos</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Hace {{ $last ? $last->recorded_at->diffForHumans() : '—' }}</div>
                        </div>
                    @empty
                        <div class="text-gray-400">No hay sensores para este dispositivo aún.</div>
                    @endforelse
                </div>
                <div class="mt-4 text-center">
                    <a href="#casa" class="inline-block bg-green-600 hover:bg-green-700 rounded px-4 py-2">Entrar a Casa</a>
                </div>
            </section>

            <!-- Bosquejo de Casa: controles interactivos -->
            <section id="casa" class="mt-8 bg-gray-800 rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Controles de la Casa</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Sala: TV y luz -->
                    <div class="bg-gray-900 border border-gray-700 rounded p-4 flex flex-col gap-3">
                        <div class="font-semibold">Sala</div>
                        <button class="control-btn" data-device="{{ $device->id }}" data-type="tv">📺 TV</button>
                        <button class="control-btn" data-device="{{ $device->id }}" data-type="light">💡 Luz</button>
                    </div>
                    <!-- Cocina: Nevera y gas -->
                    <div class="bg-gray-900 border border-gray-700 rounded p-4 flex flex-col gap-3">
                        <div class="font-semibold">Cocina</div>
                        <button class="control-btn" data-device="{{ $device->id }}" data-type="fridge">🧊 Nevera</button>
                        <button class="control-btn" data-device="{{ $device->id }}" data-type="gas">🔥 Llave de gas</button>
                    </div>
                    <!-- Dormitorio: Luz y temperatura -->
                    <div class="bg-gray-900 border border-gray-700 rounded p-4 flex flex-col gap-3">
                        <div class="font-semibold">Dormitorio</div>
                        <button class="control-btn" data-device="{{ $device->id }}" data-type="light">💡 Luz</button>
                        <button class="control-btn" data-device="{{ $device->id }}" data-type="temperature">🌡️ Temperatura</button>
                    </div>
                    <!-- Baño: Luz -->
                    <div class="bg-gray-900 border border-gray-700 rounded p-4 flex flex-col gap-3">
                        <div class="font-semibold">Baño</div>
                        <button class="control-btn" data-device="{{ $device->id }}" data-type="light">💡 Luz</button>
                    </div>
                </div>

                <!-- Modal estilo control -->
                <div id="modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center">
                    <div class="bg-white text-black rounded-lg p-6 w-80">
                        <div class="flex justify-between items-center">
                            <div id="modal-title" class="font-semibold"></div>
                            <button id="modal-close" class="text-gray-600">✖</button>
                        </div>
                        <div class="text-center mt-4">
                            <div id="modal-value" class="text-3xl font-bold">–</div>
                            <div id="modal-sub" class="text-sm text-gray-600">Hace un momento</div>
                            <div class="mt-4">
                                <input id="modal-range" type="range" min="0" max="100" value="50" class="w-full" />
                            </div>
                            <div class="mt-4">
                                <button id="modal-toggle" class="px-3 py-2 rounded bg-gray-200">Encendido</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <script>
            const modal = document.getElementById('modal');
            const mTitle = document.getElementById('modal-title');
            const mValue = document.getElementById('modal-value');
            const mSub = document.getElementById('modal-sub');
            const mRange = document.getElementById('modal-range');
            const mToggle = document.getElementById('modal-toggle');
            const mClose = document.getElementById('modal-close');

            let current = {type:null, deviceId:null};

            function openModal(type, deviceId){
                current = {type, deviceId};
                mTitle.textContent = typeLabel(type);
                mValue.textContent = initialValue(type);
                mSub.textContent = 'Hace un momento';
                mRange.style.display = isRange(type) ? '' : 'none';
                mToggle.style.display = isToggle(type) ? '' : 'none';
                mToggle.textContent = 'Encendido';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            function closeModal(){
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            function typeLabel(type){
                switch(type){
                    case 'temperature': return 'Regulador de temperatura';
                    case 'gas': return 'Llave de gas';
                    case 'light': return 'Luz';
                    case 'tv': return 'TV';
                    case 'fridge': return 'Nevera';
                    default: return 'Control';
                }
            }
            function isRange(type){
                return ['temperature','light'].includes(type);
            }
            function isToggle(type){
                return ['gas','tv','fridge','light'].includes(type);
            }
            function initialValue(type){
                return type==='temperature' ? '22°C' : (type==='light' ? '50%' : '—');
            }

            // Eventos
            document.querySelectorAll('.control-btn').forEach(btn => {
                btn.addEventListener('click', () => openModal(btn.dataset.type, btn.dataset.device));
            });
            mClose.addEventListener('click', closeModal);
            modal.addEventListener('click', (e)=>{ if(e.target===modal) closeModal(); });

            // Envío al backend
            function postSettings(data){
                const deviceId = current.deviceId;
                fetch(`/devices/${deviceId}/settings`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data),
                    credentials: 'same-origin',
                    cache: 'no-store'
                }).then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (_) {}
                    if (r.ok && json && json.ok) {
                        mSub.textContent = 'Ajuste aplicado';
                    } else {
                        mSub.textContent = 'Error aplicando ajuste';
                    }
                }).catch(()=>{
                    mSub.textContent = 'Error aplicando ajuste';
                });
            }

            mRange.addEventListener('input', () => {
                const v = parseInt(mRange.value, 10);
                if(current.type==='temperature'){
                    const target = 16 + Math.round(v/100 * 12); // 16–28°C
                    mValue.textContent = target + '°C';
                    postSettings({ temperature_target: target });
                } else if(current.type==='light'){
                    mValue.textContent = v + '%';
                    postSettings({ light_level: v });
                }
            });

            mToggle.addEventListener('click', () => {
                const nowOn = mToggle.textContent !== 'Apagado';
                const next = !nowOn;
                mToggle.textContent = next ? 'Encendido' : 'Apagado';
                if(current.type==='gas'){
                    postSettings({ gas_valve_open: next });
                } else if(current.type==='tv'){
                    postSettings({ power_profile: next ? 'high' : 'eco' });
                } else if(current.type==='fridge'){
                    postSettings({ power_profile: next ? 'normal' : 'eco' });
                } else if(current.type==='light'){
                    // Si se apaga, nivel 0
                    if(!next) postSettings({ light_level: 0 });
                }
            });
            </script>
        </main>
    </div>
</body>
</html>