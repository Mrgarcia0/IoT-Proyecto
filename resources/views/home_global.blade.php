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

        <!-- Plano simple de casa con áreas -->
        <div class="mt-6 grid grid-cols-12 gap-2 bg-gray-800 p-4 rounded-lg">
            <!-- Sala (1) -->
            <div class="col-span-7 row-span-4 bg-gray-700 rounded p-4 relative">
                <div class="text-sm text-gray-300">Sala</div>
                <button class="control-btn absolute top-2 right-2 flex items-center gap-2 px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded" data-type="temperature" data-device="1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-red-300"><path d="M6 2a2 2 0 012-2h8a2 2 0 012 2v20a2 2 0 01-2 2H8a2 2 0 01-2-2V2zm6 2a1 1 0 00-1 1v10.28a2 2 0 10-2 3.45V20h6v-1.27a2 2 0 10-2-3.45V5a1 1 0 00-1-1z"/></svg>
                    <span id="val-1-temp">—</span>
                </button>
                <button class="control-btn absolute bottom-2 right-2 flex items-center gap-2 px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded" data-type="tv" data-device="2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-blue-300"><path d="M4 5h16v11H4zM2 18h20v2H2z"/></svg>
                    <span id="val-2-power">—</span>
                </button>
            </div>

            <!-- Comedor (4) / Cocina (3) -->
            <div class="col-span-5 row-span-4 bg-gray-700 rounded p-4 relative">
                <div class="text-sm text-gray-300">Cocina / Comedor</div>
                <button class="control-btn absolute top-2 right-2 flex items-center gap-2 px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded" data-type="light" data-device="2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-300"><path d="M12 2a7 7 0 00-7 7 7 7 0 005 6.7V20h4v-4.3A7 7 0 0019 9a7 7 0 00-7-7z"/></svg>
                    <span id="val-2-light">—</span>
                </button>
                <button class="control-btn absolute bottom-2 right-2 flex items-center gap-2 px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded" data-type="fridge" data-device="2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-cyan-300"><path d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm1 4h8v2H8V6z"/></svg>
                    <span id="val-2-fridge">—</span>
                </button>
            </div>

            <!-- Dormitorios (5,7) y baño (6) -->
            <div class="col-span-6 row-span-3 bg-gray-700 rounded p-4">
                <div class="text-sm text-gray-300">Dormitorio</div>
                <div class="mt-2 text-xs text-gray-400">Calidad aire</div>
                <div class="mt-1" id="val-3-air">—</div>
            </div>
            <div class="col-span-6 row-span-3 bg-gray-700 rounded p-4">
                <div class="text-sm text-gray-300">Dormitorio</div>
                <div class="mt-2 text-xs text-gray-400">Polvo</div>
                <div class="mt-1" id="val-5-dust">—</div>
            </div>

            <!-- Laboratorio / Gas -->
            <div class="col-span-12 bg-gray-700 rounded p-4 relative">
                <div class="text-sm text-gray-300">Laboratorio</div>
                <button class="control-btn absolute top-2 right-2 flex items-center gap-2 px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded" data-type="gas" data-device="4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-emerald-300"><path d="M12 2C8 7 4 9 4 13a8 8 0 0016 0c0-4-4-6-8-11z"/></svg>
                    <span id="val-4-gas">—</span>
                </button>
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
                <button id="modal-toggle" class="mt-3 w-full px-3 py-2 bg-gray-800 text-white rounded">Encendido</button>
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
            btn.addEventListener('click', () => openModal(btn.dataset.type, btn.dataset.device));
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
                if (r.ok && json && json.ok) mSub.textContent = 'Ajuste aplicado';
                else mSub.textContent = 'Error aplicando ajuste';
            }).catch(()=>{ mSub.textContent = 'Error aplicando ajuste'; });
        }

        mRange.addEventListener('input', () => {
            const v = parseInt(mRange.value, 10);
            if(current.type==='temperature'){
                const target = 16 + Math.round(v/100 * 12); // 16–28°C
                mValue.textContent = target + '°C';
                postSettings(current.deviceId, { temperature_target: target });
            } else if(current.type==='light'){
                mValue.textContent = v + '%';
                postSettings(current.deviceId, { light_level: v });
            }
        });
        mToggle.addEventListener('click', () => {
            const nowOn = mToggle.textContent !== 'Apagado';
            const next = !nowOn;
            mToggle.textContent = next ? 'Encendido' : 'Apagado';
            if(current.type==='gas'){
                postSettings(current.deviceId, { gas_valve_open: next });
            } else if(current.type==='tv'){
                postSettings(current.deviceId, { power_profile: next ? 'high' : 'eco' });
            } else if(current.type==='fridge'){
                postSettings(current.deviceId, { power_profile: next ? 'normal' : 'eco' });
            } else if(current.type==='light'){
                if(!next) postSettings(current.deviceId, { light_level: 0 });
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
                    // Actualizar algunos indicadores clave
                    if (id===1 && s.temperature) document.getElementById('val-1-temp').textContent = `${s.temperature.value} ${s.temperature.unit||''}`;
                    if (id===2 && s.power) document.getElementById('val-2-power').textContent = `${s.power.value} ${s.power.unit||''}`;
                    if (id===2 && s.light) document.getElementById('val-2-light').textContent = `${s.light.value}%`;
                    if (id===2 && s.fridge_power) document.getElementById('val-2-fridge').textContent = `${s.fridge_power.value} ${s.fridge_power.unit||''}`;
                    if (id===3) document.getElementById('val-3-air').textContent = `${s.co2?.value ?? '—'} ${s.co2?.unit ?? ''}`;
                    if (id===5) document.getElementById('val-5-dust').textContent = `${s.pm25?.value ?? '—'} ${s.pm25?.unit ?? ''}`;
                    if (id===4) document.getElementById('val-4-gas').textContent = `${s.gas?.value ?? '—'} ${s.gas?.unit ?? ''}`;
                } catch (e) { console.warn('refresh error', e); }
            });
        }
        function startRealtime(){ if (!interval) { refreshLatest(); interval = setInterval(refreshLatest, 8000); } }
        function stopRealtime(){ if (interval) { clearInterval(interval); interval = null; } }
        startRealtime();
    </script>
</body>
</html>