<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\PanelItem;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function index(Device $device)
    {
        $variables = SensorReading::where('device_id', $device->id)
            ->select('variable_name')
            ->distinct()
            ->pluck('variable_name');

        $items = PanelItem::where('device_id', $device->id)
            ->orderBy('position')
            ->get()
            ->map(function ($item) use ($device) {
                $vars = $item->variables ? json_decode($item->variables, true) : ($item->variable_name ? [$item->variable_name] : []);
                $computed = [];
                $units = [];
                foreach ($vars as $v) {
                    $computed[$v] = $this->computeMetricValue(
                        $device->id,
                        $v,
                        $item->metric,
                        $item->window_days,
                        $item->critical_min,
                        $item->critical_max
                    );
                    $units[$v] = $this->getVariableUnit($device->id, $v);
                }
                $item->computed_values = $computed;
                $item->computed_units = $units;
                return $item;
            });

        return view('devices.panels', compact('device', 'variables', 'items'));
    }

    public function store(Request $request, Device $device)
    {
        $rules = [
            'metric' => 'required|in:lkv,max,min,avg,roc,critical_time',
            'variable_names' => 'required|array|min:1',
            'variable_names.*' => 'string',
            'window_days' => 'nullable|integer|min:1|max:365',
            'title' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
        ];

        if ($request->input('metric') === 'critical_time') {
            $rules['critical_min'] = 'nullable|numeric|required_without:critical_max';
            $rules['critical_max'] = 'nullable|numeric|required_without:critical_min';
        } else {
            // Se permiten pero no se usan en otras métricas
            $rules['critical_min'] = 'nullable|numeric';
            $rules['critical_max'] = 'nullable|numeric';
        }

        $validated = $request->validate($rules);

        $validated['device_id'] = $device->id;
        $validated['window_days'] = $validated['window_days'] ?? 7;
        $validated['position'] = $validated['position'] ?? 0;

        // Manejo de múltiples variables (obligatorio en create)
        $validated['variables'] = json_encode(array_values($validated['variable_names']));
        unset($validated['variable_names']);
        $validated['variable_name'] = null;

        PanelItem::create($validated);

        return redirect()->route('devices.panels', $device)->with('status', 'Panel creado');
    }

    public function update(Request $request, Device $device, PanelItem $panelItem)
    {
        abort_unless($panelItem->device_id === $device->id, 404);

        $rules = [
            'metric' => 'required|in:lkv,max,min,avg,roc,critical_time',
            'variable_names' => 'nullable|array',
            'variable_names.*' => 'string',
            'window_days' => 'nullable|integer|min:1|max:365',
            'title' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
        ];

        if ($request->input('metric') === 'critical_time') {
            $rules['critical_min'] = 'nullable|numeric|required_without:critical_max';
            $rules['critical_max'] = 'nullable|numeric|required_without:critical_min';
        } else {
            $rules['critical_min'] = 'nullable|numeric';
            $rules['critical_max'] = 'nullable|numeric';
        }

        $validated = $request->validate($rules);

        $validated['window_days'] = $validated['window_days'] ?? 7;

        if (!empty($validated['variable_names'])) {
            $validated['variables'] = json_encode(array_values($validated['variable_names']));
            unset($validated['variable_names']);
            $validated['variable_name'] = null;
        }

        $panelItem->update($validated);

        return redirect()->route('devices.panels', $device)->with('status', 'Panel actualizado');
    }

    public function destroy(Device $device, PanelItem $panelItem)
    {
        abort_unless($panelItem->device_id === $device->id, 404);
        $panelItem->delete();

        return redirect()->route('devices.panels', $device)->with('status', 'Panel eliminado');
    }

    private function computeMetricValue(int $deviceId, string $variable, string $metric, int $windowDays, ?float $criticalMin = null, ?float $criticalMax = null): ?float
    {
        $query = SensorReading::where('device_id', $deviceId)
            ->where('variable_name', $variable);

        if ($metric === 'lkv') {
            $reading = $query->orderByDesc('recorded_at')->first();
            return $reading?->value;
        }

        $from = Carbon::now()->subDays($windowDays);
        $query->where('recorded_at', '>=', $from);

        switch ($metric) {
            case 'max':
                return (float) $query->max('value');
            case 'min':
                return (float) $query->min('value');
            case 'avg':
                $avg = $query->avg('value');
                return $avg !== null ? (float) $avg : null;
            case 'roc': // tasa de cambio por hora (último vs anterior)
                $lastTwo = $query->orderByDesc('recorded_at')->take(2)->get();
                if ($lastTwo->count() < 2) {
                    return null;
                }
                $latest = $lastTwo[0];
                $previous = $lastTwo[1];
                $seconds = Carbon::parse($previous->recorded_at)->diffInSeconds(Carbon::parse($latest->recorded_at));
                if ($seconds === 0) {
                    return null;
                }
                $perHour = ($latest->value - $previous->value) / ($seconds / 3600);
                return (float) $perHour;
            case 'critical_time':
                if ($criticalMin === null && $criticalMax === null) {
                    return null;
                }
                $readings = $query->orderBy('recorded_at')->get(['value', 'recorded_at']);
                if ($readings->isEmpty()) {
                    return null;
                }
                // Aproximar tiempo crítico sumando intervalos donde el punto cae fuera de rango
                // Asumiendo muestreo horario en seeder, contamos horas fuera de rango
                $criticalCount = 0;
                foreach ($readings as $r) {
                    $v = (float) $r->value;
                    $outOfRange = false;
                    if ($criticalMin !== null && $v < $criticalMin) {
                        $outOfRange = true;
                    }
                    if ($criticalMax !== null && $v > $criticalMax) {
                        $outOfRange = true;
                    }
                    if ($outOfRange) {
                        $criticalCount++;
                    }
                }
                // Cada lectura ~1 hora
                return (float) $criticalCount; // horas
            default:
                return null;
        }
    }

    private function getVariableUnit(int $deviceId, string $variable): ?string
    {
        $reading = SensorReading::where('device_id', $deviceId)
            ->where('variable_name', $variable)
            ->orderByDesc('recorded_at')
            ->first();
        return $reading?->unit;
    }
}