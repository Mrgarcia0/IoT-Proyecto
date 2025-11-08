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
        // No forzar el estado de los dispositivos en cada arranque de request.
        // El estado debe persistir según lo que el usuario elija (ON/OFF) y
        // sólo inicializarse mediante seeders/migraciones.
    }
}
