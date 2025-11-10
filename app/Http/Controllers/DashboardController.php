<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('welcome', ['devices' => $devices]);
    }

    /**
     * Vista global de Casa con plano e iconos interactivos.
     */
    public function casa()
    {
        $devices = Device::all()->keyBy('id');
        return view('home_global', ['devices' => $devices]);
    }
}
