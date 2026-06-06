<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\OrdenTrabajo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteVehiculoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('persona')
            ->where('activo', true)
            ->join('personas', 'empleados.persona_id', '=', 'personas.id')
            ->orderBy('personas.apellido')
            ->select('empleados.*')
            ->get();

        return view('gerente.reportes.vehiculos', compact('empleados'));
    }

    public function generar(Request $request)
    {
        $request->validate([
            'desde'       => 'required|date',
            'hasta'       => 'required|date|after_or_equal:desde',
            'empleado_id' => 'nullable|exists:empleados,id',
        ], [
            'desde.required' => 'La fecha de inicio es obligatoria.',
            'hasta.required' => 'La fecha de fin es obligatoria.',
            'hasta.after_or_equal' => 'La fecha de fin no puede ser anterior al inicio.',
        ]);

        $query = OrdenTrabajo::with([
            'vehiculo.cliente.persona',
            'empleado.persona',
            'servicios',
            'repuestos',
            'historialEstados',
            'imagenes',
        ])
        ->whereBetween('fecha_ingreso', [$request->desde . ' 00:00:00', $request->hasta . ' 23:59:59'])
        ->orderBy('fecha_ingreso');

        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        $ordenes  = $query->get();
        $desde    = \Carbon\Carbon::parse($request->desde);
        $hasta    = \Carbon\Carbon::parse($request->hasta);

        // Convertir fotos a base64 para que dompdf las pueda embeber
        $fotosPorOrden = [];
        foreach ($ordenes as $orden) {
            $fotos = [];
            foreach ($orden->imagenes->take(6) as $img) {
                try {
                    // Agregar transformaciones Cloudinary para reducir tamaño
                    $url = str_replace('/upload/', '/upload/w_400,q_60,f_jpg/', $img->cloudinary_url);
                    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
                    $bytes = @file_get_contents($url, false, $ctx);
                    if ($bytes) {
                        $fotos[] = [
                            'tipo'  => $img->tipo,
                            'data'  => 'data:image/jpeg;base64,' . base64_encode($bytes),
                        ];
                    }
                } catch (\Exception $e) {
                    // Continuar sin foto si falla
                }
            }
            $fotosPorOrden[$orden->id] = $fotos;
        }

        $pdf = Pdf::loadView('pdf.reportes.vehiculos', compact('ordenes', 'desde', 'hasta', 'fotosPorOrden'))
            ->setPaper('a4', 'portrait');

        $filename = 'reporte_vehiculos_' . $desde->format('d-m-Y') . '_al_' . $hasta->format('d-m-Y') . '.pdf';

        return $pdf->download($filename);
    }
}
