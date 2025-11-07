<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Device;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Al iniciar la aplicación, si la variable de entorno indica reset,
        // pon todos los dispositivos en OFF una sola vez por proceso.
        static $didReset = false;
        if (!$didReset) {
            try {
                Device::query()->update(['is_active' => false]);
            } catch (\Throwable $e) {
                // Evitar que falle el arranque si aún no hay migraciones
            }
            $didReset = true;
        }
    }
}
