<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehiculo\StoreVehiculoRequest;
use App\Http\Requests\Vehiculo\UpdateVehiculoRequest;
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

        return view('gerente.vehiculos.index', compact('vehiculos', 'buscar'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::with('persona')->get()->sortBy(fn($c) => $c->persona->apellido);
        $clienteId = $request->get('cliente_id');
        return view('gerente.vehiculos.create', compact('clientes', 'clienteId'));
    }

    public function store(StoreVehiculoRequest $request)
    {
        $vehiculo = Vehiculo::create($request->validated());

        ActivityLogger::log('Vehículo registrado', class_basename(__CLASS__));

        return redirect()->route('gerente.vehiculos.index')
            ->with('success', "Vehículo {$vehiculo->placa} registrado correctamente.");
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load(['cliente.persona', 'ordenesTrabajo' => fn($q) => $q->orderByDesc('created_at')]);
        return view('gerente.vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehiculo $vehiculo)
    {
        $vehiculo->load('cliente.persona');
        $clientes = Cliente::with('persona')->get()->sortBy(fn($c) => $c->persona->apellido);
        return view('gerente.vehiculos.edit', compact('vehiculo', 'clientes'));
    }

    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo)
    {
        $vehiculo->update($request->validated());

        ActivityLogger::log('Vehículo actualizado', class_basename(__CLASS__));

        return redirect()->route('gerente.vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo actualizado correctamente.');
    }
}
