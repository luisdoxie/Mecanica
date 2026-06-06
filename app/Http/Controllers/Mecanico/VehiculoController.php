<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehiculo\StoreVehiculoRequest;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehiculo::with(['cliente.persona'])
            ->join('clientes', 'vehiculos.cliente_id', '=', 'clientes.id')
            ->join('personas', 'clientes.persona_id', '=', 'personas.id')
            ->select('vehiculos.*');

        if ($buscar = $request->get('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('vehiculos.placa', 'ilike', "%{$buscar}%")
                  ->orWhere('vehiculos.marca', 'ilike', "%{$buscar}%")
                  ->orWhere('vehiculos.modelo', 'ilike', "%{$buscar}%")
                  ->orWhere('personas.nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('personas.apellido', 'ilike', "%{$buscar}%");
            });
        }

        $vehiculos = $query->orderByDesc('vehiculos.created_at')->paginate(15)->withQueryString();

        return view('mecanico.vehiculos.index', compact('vehiculos', 'buscar'));
    }

    public function create(Request $request)
    {
        $clienteId = $request->get('cliente_id');
        $cliente   = $clienteId ? Cliente::with('persona')->find($clienteId) : null;

        // Todos los clientes: el nuevo primero, luego por apellido
        $todos = Cliente::with('persona')->get()
            ->sortBy(fn($c) => [$c->id == $clienteId ? 0 : 1, $c->persona?->apellido ?? '']);

        return view('mecanico.vehiculos.create', compact('todos', 'cliente', 'clienteId'));
    }

    public function store(StoreVehiculoRequest $request)
    {
        $vehiculo = Vehiculo::create($request->validated());

        ActivityLogger::log('Vehículo registrado por mecánico', 'VehiculoController', $vehiculo->id);

        session()->put('mecanico_nuevo_vehiculo_id', $vehiculo->id);

        return redirect()
            ->route('mecanico.ordenes.create')
            ->with('success', "Vehículo {$vehiculo->placa} registrado. Ahora crea la orden de trabajo.");
    }
}
