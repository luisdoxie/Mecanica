<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Auth;

class VehiculoController extends Controller
{
    public function miVehiculo()
    {
        $user = Auth::user();

        $vehiculos = Vehiculo::whereHas('cliente.persona', function ($q) use ($user) {
            $q->where('id', $user->persona_id);
        })->with(['ordenesTrabajo' => fn($q) => $q->orderByDesc('created_at')])->get();

        $ordenActiva = $vehiculos->flatMap->ordenesTrabajo
            ->whereNotIn('estado', ['ENTREGADO'])
            ->sortByDesc('created_at')
            ->first();

        if ($ordenActiva) {
            $ordenActiva->load(['vehiculo', 'empleado.persona', 'servicios', 'imagenes', 'historialEstados']);
        }

        return view('cliente.mi-vehiculo', compact('user', 'vehiculos', 'ordenActiva'));
    }
}
