<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-6">
        <div class="flex items-center justify-between">
            <a href="/" class="text-sm text-blue-400 hover:underline">← Volver al Dashboard</a>
            <h1 class="text-2xl font-bold">Casa</h1>
        </div>

        <!-- Plano de la casa -->
        <section class="mt-6 bg-gray-800 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-3">Plano de la casa</h2>
            <div class="bg-gray-900 rounded p-2">
                <div id="house-map-container" class="relative">
                    <img id="house-map"
                         src="{{ asset('house-map.jpg') }}"
                         alt="Plano de la casa"
                         class="w-full h-auto rounded select-none">
                    <!-- Overlay de cuadrados -->
                    <div id="map-hotspots" class="absolute inset-0"></div>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Si no ves el plano, coloca tu imagen en <code class="bg-gray-700 px-1 rounded">public/house-map.jpg</code>.</p>
        </section>

        <!-- Tablero tipo Home Assistant con tarjetas por habitación -->
        <div class="mt-6 space-y-6">
            <!-- Sala de estar -->
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🛋️ Sala de estar</span>
                    <span class="ml-auto text-xs text-gray-400">Estado general</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Temperatura -->
                    <button class="control-btn flex items-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded" data-type="temperature" data-device="1" data-room="living">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-red-300"><path d="M6 2a2 2 0 012-2h8a2 2 0 012 2v20a2 2 0 01-2 2H8a2 2 0 01-2-2V2zm6 2a1 1 0 00-1 1v10.28a2 2 0 10-2 3.45V20h6v-1.27a2 2 0 10-2-3.45V5a1 1 0 00-1-1z"/></svg>
                        <div>
                            <div class="text-sm">Temperatura</div>
                            <div class="text-xs text-gray-300" id="val-1-temp">—</div>
                        </div>
                    </button>
                    <!-- Luz sala -->
                    <button class="control-btn flex items-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded" data-type="light" data-device="2" data-room="living">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-300"><path d="M12 2a7 7 0 00-7 7 7 7 0 005 6.7V20h4v-4.3A7 7 0 0019 9a7 7 0 00-7-7z"/></svg>
                        <div>
                            <div class="text-sm">Luz</div>
                            <div class="text-xs text-gray-300" id="val-2-light-living">—</div>
                            <span id="chip-light-living" class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-600 text-gray-300 mt-1">Apagado</span>
                        </div>
                    </button>
                    <!-- TV -->
                    <button class="control-btn flex items-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded" data-type="tv" data-device="2" data-room="living">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-blue-300"><path d="M4 5h16v11H4zM2 18h20v2H2z"/></svg>
                        <div>
                            <div class="text-sm">TV</div>
                            <div class="text-xs text-gray-300" id="val-2-tv">—</div>
                            <span id="chip-tv" class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-600 text-gray-300 mt-1">Apagado</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Cocina -->
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🍳 Cocina</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Luz cocina -->
                    <button class="control-btn flex items-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded" data-type="light" data-device="2" data-room="kitchen">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-300"><path d="M12 2a7 7 0 00-7 7 7 7 0 005 6.7V20h4v-4.3A7 7 0 0019 9a7 7 0 00-7-7z"/></svg>
                        <div>
                            <div class="text-sm">Luz</div>
                            <div class="text-xs text-gray-300" id="val-2-light-kitchen">—</div>
                            <span id="chip-light-kitchen" class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-600 text-gray-300 mt-1">Apagado</span>
                        </div>
                    </button>
                    <!-- Nevera -->
                    <button class="control-btn flex items-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded" data-type="fridge" data-device="2" data-room="kitchen">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-cyan-300"><path d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm1 4h8v2H8V6z"/></svg>
                        <div>
                            <div class="text-sm">Nevera</div>
                            <div class="text-xs text-gray-300" id="val-2-fridge">—</div>
                            <span id="chip-fridge" class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-600 text-gray-300 mt-1">Apagado</span>
                        </div>
                    </button>
                    <!-- Gas -->
                    <button class="control-btn flex items-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded" data-type="gas" data-device="4" data-room="kitchen">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-300"><path d="M12 2C8 7 4 9 4 13a8 8 0 0016 0c0-4-4-6-8-11z"/></svg>
                        <div>
                            <div class="text-sm">Válvula de gas</div>
                            <div class="text-xs text-gray-300" id="val-4-gas">—</div>
                            <span id="chip-gas" class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-600 text-gray-300 mt-1">Apagado</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Baño -->
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🛁 Baño</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <button class="control-btn flex items-center gap-3 p-3 bg-gray-700 hover:bg-gray-600 rounded" data-type="light" data-device="2" data-room="bath">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-300"><path d="M12 2a7 7 0 00-7 7 7 7 0 005 6.7V20h4v-4.3A7 7 0 0019 9a7 7 0 00-7-7z"/></svg>
                        <div>
                            <div class="text-sm">Luz</div>
                            <div class="text-xs text-gray-300" id="val-2-light-bath">—</div>
                            <span id="chip-light-bath" class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-600 text-gray-300 mt-1">Apagado</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Sensores ambientales extra (opcional) -->
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🌫️ Ambiente</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="p-3 bg-gray-700 rounded">
                        <div class="text-sm">Calidad del aire</div>
                        <div class="text-xs text-gray-300" id="val-3-air">—</div>
                    </div>
                    <div class="p-3 bg-gray-700 rounded">
                        <div class="text-sm">Polvo</div>
                        <div class="text-xs text-gray-300" id="val-5-dust">—</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de control reutilizable -->
        <div id="modal" class="fixed inset-0 bg-black/60 items-center justify-center hidden">
            <div class="bg-white text-gray-900 w-80 rounded-lg p-4 relative">
                <button id="modal-close" class="absolute top-2 right-2 text-purple-600">✕</button>
                <div id="modal-title" class="font-semibold">Control</div>
                <div id="modal-sub" class="text-xs text-gray-500">Hace un momento</div>
                <div class="my-4 text-center text-2xl" id="modal-value">—</div>
                <input type="range" id="modal-range" min="0" max="100" value="50" class="w-full">
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <button id="modal-toggle" class="px-3 py-2 bg-gray-800 text-white rounded">Encendido</button>
                    <button id="modal-action" class="px-3 py-2 bg-indigo-600 text-white rounded hidden">Acción</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const modal = document.getElementById('modal');
        const mTitle = document.getElementById('modal-title');
        const mValue = document.getElementById('modal-value');
        const mSub = document.getElementById('modal-sub');
        const mRange = document.getElementById('modal-range');
        const mToggle = document.getElementById('modal-toggle');
        const mClose = document.getElementById('modal-close');

        let current = {type:null, deviceId:null, room:null, zone:null};
        const lastSettings = {}; // cache de ajustes por dispositivo

        // Utilidad: actualizar chip visible
        function setChip(id, on){
            const el = document.getElementById(id);
            if(!el) return;
            el.textContent = on ? 'Encendido' : 'Apagado';
            el.classList.remove('bg-gray-600','text-gray-300','bg-green-600','text-green-100');
            if(on){ el.classList.add('bg-green-600','text-green-100'); }
            else { el.classList.add('bg-gray-600','text-gray-300'); }
        }

        function openModal(type, deviceId, room, zone){
            current = {type, deviceId, room, zone};
            mTitle.textContent = typeLabel(type);
            mValue.textContent = initialValue(type);
            mSub.textContent = 'Hace un momento';
            mRange.style.display = isRange(type) ? '' : 'none';
            mToggle.style.display = isToggle(type) ? '' : 'none';
            // Botón de acción (solo para limpiar polvo)
            const actionBtn = document.getElementById('modal-action');
            actionBtn.classList.add('hidden');
            if(type==='clean'){ actionBtn.textContent = 'Limpiar'; actionBtn.classList.remove('hidden'); }
            // Inicializar estado desde ajustes persistidos
            const s = lastSettings[deviceId] || {};
            if(type==='temperature'){
                const target = typeof s.temperature_target === 'number' ? s.temperature_target : 22;
                mValue.textContent = target + '°C';
                // map 16–28 -> 0–100
                const r = Math.round(((target - 16) / 12) * 100);
                mRange.value = Math.max(0, Math.min(100, r));
                mToggle.textContent = s.temperature_on ? 'Encendido' : 'Apagado';
            }
            if(type==='light'){
                const key = current.zone ? (`light_${current.zone}_level`) : (room==='living' ? 'living_light_level' : (room==='kitchen' ? 'kitchen_light_level' : 'bath_light_level'));
                const lvl = typeof s[key] === 'number' ? s[key] : 0;
                mRange.value = lvl;
                mValue.textContent = lvl + '%';
                mToggle.textContent = lvl > 0 ? 'Encendido' : 'Apagado';
                // chip por habitación
                const chipId = room==='living' ? 'chip-light-living' : (room==='kitchen' ? 'chip-light-kitchen' : 'chip-light-bath');
                setChip(chipId, lvl > 0);
            } else if(type==='tv'){
                mToggle.textContent = s.tv_on ? 'Encendido' : 'Apagado';
                setChip('chip-tv', !!s.tv_on);
            } else if(type==='fridge'){
                mToggle.textContent = s.fridge_on ? 'Encendido' : 'Apagado';
                setChip('chip-fridge', !!s.fridge_on);
            } else if(type==='gas'){
                mToggle.textContent = s.gas_valve_open ? 'Encendido' : 'Apagado';
                setChip('chip-gas', !!s.gas_valve_open);
            } else {
                mToggle.textContent = 'Encendido';
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }
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
        function isRange(type){ return ['temperature','light'].includes(type); }
        function isToggle(type){ return ['gas','tv','fridge','light'].includes(type); }
        function initialValue(type){ return type==='temperature' ? '22°C' : (type==='light' ? '50%' : '—'); }

        document.querySelectorAll('.control-btn').forEach(btn => {
            btn.addEventListener('click', () => openModal(btn.dataset.type, btn.dataset.device, btn.dataset.room));
        });
        mClose.addEventListener('click', closeModal);
        modal.addEventListener('click', (e)=>{ if(e.target===modal) closeModal(); });

        function postSettings(deviceId, data){
            fetch(`/devices/${deviceId}/settings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data),
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(async r => {
                let json = null;
                try { json = await r.json(); } catch (_) {}
                if (r.ok) {
                    mSub.textContent = 'Ajuste aplicado';
                    // Actualizar cache de ajustes (prioriza respuesta, sino usa el payload enviado)
                    const merged = json && json.settings ? json.settings : { ...(lastSettings[deviceId]||{}), ...data };
                    lastSettings[deviceId] = merged;
                } else {
                    mSub.textContent = 'Error aplicando ajuste';
                }
            }).catch(()=>{ mSub.textContent = 'Error aplicando ajuste'; });
        }

        mRange.addEventListener('input', () => {
            const v = parseInt(mRange.value, 10);
            if(current.type==='temperature'){
                const target = 16 + Math.round(v/100 * 12); // 16–28°C
                mValue.textContent = target + '°C';
                postSettings(current.deviceId, { temperature_target: target });
                // Actualiza inmediatamente la tarjeta de temperatura y cache local
                const tempEl = document.getElementById('val-1-temp');
                if (tempEl) tempEl.textContent = `${target} °C`;
                lastSettings[current.deviceId] = { ...(lastSettings[current.deviceId]||{}), temperature_target: target };
            } else if(current.type==='light'){
                mValue.textContent = v + '%';
                const key = current.zone ? (`light_${current.zone}_level`) : (current.room==='living' ? 'living_light_level' : (current.room==='kitchen' ? 'kitchen_light_level' : 'bath_light_level'));
                postSettings(current.deviceId, { [key]: v });
                const chipId = current.room==='living' ? 'chip-light-living' : (current.room==='kitchen' ? 'chip-light-kitchen' : 'chip-light-bath');
                setChip(chipId, v > 0);
                // Actualiza iconos del plano
                lastSettings[2] = { ...(lastSettings[2]||{}), [key]: v };
                updateSquareIcons();
            }
        });
        mToggle.addEventListener('click', () => {
            const nowOn = mToggle.textContent !== 'Apagado';
            const next = !nowOn;
            mToggle.textContent = next ? 'Encendido' : 'Apagado';
            if(current.type==='gas'){
                postSettings(current.deviceId, { gas_valve_open: next });
                setChip('chip-gas', next);
            } else if(current.type==='tv'){
                postSettings(current.deviceId, { tv_on: next });
                setChip('chip-tv', next);
            } else if(current.type==='fridge'){
                postSettings(current.deviceId, { fridge_on: next });
                setChip('chip-fridge', next);
            } else if(current.type==='light'){
                if(!next){
                    const key = current.zone ? (`light_${current.zone}_level`) : (current.room==='living' ? 'living_light_level' : (current.room==='kitchen' ? 'kitchen_light_level' : 'bath_light_level'));
                    postSettings(current.deviceId, { [key]: 0 });
                    const chipId = current.room==='living' ? 'chip-light-living' : (current.room==='kitchen' ? 'chip-light-kitchen' : 'chip-light-bath');
                    setChip(chipId, false);
                    lastSettings[2] = { ...(lastSettings[2]||{}), [key]: 0 };
                    updateSquareIcons();
                }
            } else if(current.type==='temperature'){
                postSettings(current.deviceId, { temperature_on: next });
                lastSettings[current.deviceId] = { ...(lastSettings[current.deviceId]||{}), temperature_on: next };
            }
        });

        // Acción de limpiar polvo
        document.getElementById('modal-action').addEventListener('click', async () => {
            if(current.type!=='clean') return;
            try {
                // Obtener lectura actual de polvo
                const res = await fetch(`/devices/5/latest`, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                const data = await res.json();
                const cur = (data.series?.pm25?.value ?? 50);
                const reduced = Math.max(5, Math.round(cur * 0.3)); // baja considerablemente
                mValue.textContent = reduced + ' µg/m³';
                mSub.textContent = 'Polvo reducido';
                // Guardar override en ajustes para que se mantenga
                postSettings(5, { pm25_override: reduced });
                lastSettings[5] = { ...(lastSettings[5]||{}), pm25_override: reduced };
            } catch(e) {
                mSub.textContent = 'Error limpiando';
            }
        });

        // Tiempo real: simulación + actualización de indicadores
        const deviceIds = [1,2,3,4,5];
        let interval = null;
        function refreshLatest(){
            deviceIds.forEach(async (id) => {
                try {
                    await fetch(`/devices/${id}/simulate-sample`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin', cache: 'no-store' });
                    const res = await fetch(`/devices/${id}/latest`, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                    const data = await res.json();
                    const s = data.series || {};
                    // cachear ajustes
                    if (data.settings) { lastSettings[id] = data.settings; }
                    // Chips por dispositivo específico para evitar parpadeos
                    if (id===2) {
                        const st = lastSettings[2] || {};
                        setChip('chip-tv', !!st.tv_on);
                        setChip('chip-fridge', !!st.fridge_on);
                        setChip('chip-light-living', (s.light_living?.value ?? st.living_light_level ?? 0) > 0);
                        setChip('chip-light-kitchen', (s.light_kitchen?.value ?? st.kitchen_light_level ?? 0) > 0);
                        setChip('chip-light-bath', (s.light_bath?.value ?? st.bath_light_level ?? 0) > 0);
                        updateSquareIcons();
                    }
                    if (id===4) {
                        const st4 = lastSettings[4] || {};
                        setChip('chip-gas', !!st4.gas_valve_open);
                    }
                    // Actualizar indicadores clave
                    const tempEl = document.getElementById('val-1-temp');
                    if (id===1) {
                        const st1 = lastSettings[1] || {};
                        const t = typeof st1.temperature_target === 'number' ? st1.temperature_target : (s.temperature?.value ?? 22);
                        tempEl.textContent = `${t} °C`;
                    }
                    if (id===2 && s.tv_power) document.getElementById('val-2-tv').textContent = `${s.tv_power.value} ${s.tv_power.unit||''}`;
                    if (id===2) {
                        const st2 = lastSettings[2] || {};
                        const lv = typeof st2.living_light_level === 'number' ? st2.living_light_level : (s.light_living?.value ?? 0);
                        const kv = typeof st2.kitchen_light_level === 'number' ? st2.kitchen_light_level : (s.light_kitchen?.value ?? 0);
                        const bv = typeof st2.bath_light_level === 'number' ? st2.bath_light_level : (s.light_bath?.value ?? 0);
                        document.getElementById('val-2-light-living').textContent = `${lv}%`;
                        document.getElementById('val-2-light-kitchen').textContent = `${kv}%`;
                        document.getElementById('val-2-light-bath').textContent = `${bv}%`;
                    }
                    if (id===2 && s.fridge_power) document.getElementById('val-2-fridge').textContent = `${s.fridge_power.value} ${s.fridge_power.unit||''}`;
                    if (id===3) document.getElementById('val-3-air').textContent = `${s.co2?.value ?? '—'} ${s.co2?.unit ?? ''}`;
                    if (id===5) {
                        const ov = (lastSettings[5]||{}).pm25_override;
                        const val = typeof ov==='number' ? ov : (s.pm10?.value ?? '—');
                        const unit = s.pm10?.unit ?? 'µg/m³';
                        document.getElementById('val-5-dust').textContent = `${val} ${unit}`;
                    }
                    if (id===4) document.getElementById('val-4-gas').textContent = `${s.gas?.value ?? '—'} ${s.gas?.unit ?? ''}`;
                } catch (e) { console.warn('refresh error', e); }
            });
        }
        function startRealtime(){ if (!interval) { refreshLatest(); interval = setInterval(refreshLatest, 8000); } }
        function stopRealtime(){ if (interval) { clearInterval(interval); interval = null; } }
        startRealtime();

        // === Overlay de cuadrados con iconos ===
        const hsContainer = document.getElementById('map-hotspots');
        const hsImg = document.getElementById('house-map');

        // Tamaño base del cuadrado en porcentaje (ajustable)
        const SIZE_PCT = 3.2;

        // Lista inicial (aproximada). Ajustaremos coordenadas contigo.
        // type: light | clean | power | smoke | temp
        const SQUARES = [
          // Cocina
          
          // Habitación grande arriba a la izquierda
          { left: 29, top: 38, type: 'light', zone: 'z1' },
          { left: 21,  top: 49, type: 'clean' },
          { left: 33, top: 49, type: 'temp' },

          // Sala de estar 2 arriba a la derecha
          { left: 13, top: 33, type: 'light', zone: 'z2' },
          { left: 17,  top: 46, type: 'clean' },

          // Baño de izquierda
          { left: 41, top: 28, type: 'light', zone: 'z3' },

          // Baño de derecha
          { left: 60, top: 28, type: 'light', zone: 'z4' },

          // Cocina izquierda
          { left: 21, top: 60, type: 'smoke' },
          { left: 29, top: 70, type: 'light', zone: 'z5' },
          { left: 36,  top: 54, type: 'clean' },
          { left: 36, top: 70, type: 'air' },

          // El cuarto de atras
          { left: 25, top: 86, type: 'power' },

          // Sala de estar y comedor
          { left: 51, top: 48, type: 'light', zone: 'z6' },
          { left: 51, top: 62, type: 'light', zone: 'z7' },
          { left: 57, top: 81, type: 'temp' },
          { left: 40, top: 39, type: 'clean' },
          { left: 45, top: 81, type: 'air' },

          // Baño habitacion derecha
          { left: 72, top: 22, type: 'light', zone: 'z8' },
          
          // Habitacion derecha
          { left: 72, top: 46, type: 'light', zone: 'z9' },
          { left: 80, top: 56, type: 'clean' },
          { left: 70, top: 56, type: 'temp' },

          // Habitacion derecha abajo
          { left: 72, top: 70, type: 'light', zone: 'z10' },
          { left: 80, top: 81, type: 'clean' },
          { left: 65, top: 81, type: 'temp' },

        ];

        function iconLight(on){ return on ? '🔆' : '💡'; }
        function iconFor(type, on=false){
          switch(type){
            case 'light': return iconLight(on);
            case 'clean': return '🧹';
            case 'power': return '⚡';
            case 'smoke': return '💨';
            case 'temp': return '🌡️';
            case 'air': return '☁️';
            default: return '⬛';
          }
        }

        const squareEls = [];
        function renderSquares(){
          hsContainer.innerHTML = '';
          SQUARES.forEach((s, idx) => {
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'absolute flex items-center justify-center rounded-md';
            el.style.left = s.left + '%';
            el.style.top = s.top + '%';
            el.style.width = SIZE_PCT + '%';
            el.style.height = SIZE_PCT + '%';
            el.style.transform = 'translate(-50%, -50%)';
            el.style.background = '#111';
            el.style.color = '#fff';
            el.style.boxShadow = '0 0 0 2px rgba(255,255,255,0.15)';
            el.style.cursor = 'pointer';
            el.title = s.type;
            el.innerHTML = iconFor(s.type, false);
            el.dataset.type = s.type;
            if(s.zone) el.dataset.zone = s.zone;
            // Click comportamiento (solo luz por ahora)
            el.addEventListener('click', () => {
              if (s.type === 'light') {
                openModal('light', 2, null, s.zone);
              } else if (s.type === 'temp') {
                openModal('temperature', 1, 'living');
              } else if (s.type === 'clean') {
                openModal('clean', 5);
              } else if (['air','smoke','power'].includes(s.type)) {
                // Solo informativo
                openModal('info', 0);
                mRange.style.display = 'none';
                mToggle.style.display = 'none';
                document.getElementById('modal-action').classList.add('hidden');
                // Mostrar valor si disponible en tarjetas
                if(s.type==='air') mValue.textContent = document.getElementById('val-3-air').textContent;
                if(s.type==='smoke') mValue.textContent = document.getElementById('val-4-gas').textContent;
                if(s.type==='power') mValue.textContent = document.getElementById('val-2-fridge').textContent; // consumo de electricidad como ejemplo
              }
            });
            hsContainer.appendChild(el);
            squareEls[idx] = el;
          });
        }

        function updateSquareIcons(){
          squareEls.forEach((el) => {
            const type = el.dataset.type;
            if(type==='light'){
              const zone = el.dataset.zone;
              const lvl = (lastSettings[2]||{})[`light_${zone}_level`];
              el.innerHTML = iconLight((typeof lvl==='number' ? lvl : 0) > 0);
            }
          });
        }

        hsImg.addEventListener('load', renderSquares);
        window.addEventListener('resize', renderSquares);
    </script>
</body>
</html>