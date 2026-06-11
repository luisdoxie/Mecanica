<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Models\Recibo;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReciboController extends Controller
{
    public function index()
    {
        $recibos = Recibo::with(['orden.vehiculo.cliente.persona'])
            ->orderByDesc('emitido_en')
            ->paginate(15);

        return view('gerente.recibos.index', compact('recibos'));
    }

    public function generar(OrdenTrabajo $orden)
    {
        // Si ya existe recibo, descargarlo directamente
        $existente = Recibo::where('orden_trabajo_id', $orden->id)->first();
        if ($existente) {
            return $this->descargar($existente);
        }

        $orden->load([
            'vehiculo.cliente.persona',
            'empleado.persona',
            'servicios',
            'repuestos',
            'pagos',
        ]);

        $ultimo    = Recibo::whereYear('created_at', now()->year)->count();
        $numRecibo = 'REC-' . now()->year . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);

        // Registrar el recibo en BD antes de generar el PDF
        $recibo = Recibo::create([
            'orden_trabajo_id' => $orden->id,
            'numero_recibo'    => $numRecibo,
            'pdf_url'          => null,
            'total'            => $orden->costo_total ?? 0,
            'emitido_en'       => now(),
        ]);

        ActivityLogger::log("Recibo {$numRecibo} generado", 'ReciboController', $recibo->id);

        // Generar y descargar el PDF directamente (sin subir a Cloudinary para evitar timeouts)
        $pdf = Pdf::loadView('pdf.recibo', compact('orden', 'numRecibo'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$numRecibo}.pdf");
    }

    public function descargar(Recibo $recibo)
    {
        // Siempre regenera el PDF desde la BD (más confiable que Cloudinary para PDFs)
        $recibo->load('orden.vehiculo.cliente.persona', 'orden.empleado.persona', 'orden.servicios', 'orden.repuestos', 'orden.pagos');
        $numRecibo = $recibo->numero_recibo;
        $orden     = $recibo->orden;

        $pdf = Pdf::loadView('pdf.recibo', compact('orden', 'numRecibo'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$numRecibo}.pdf");
    }
}
