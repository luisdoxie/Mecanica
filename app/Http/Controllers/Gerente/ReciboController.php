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
        // Verificar que no tenga recibo ya
        $existente = Recibo::where('orden_trabajo_id', $orden->id)->first();
        if ($existente) {
            return redirect()->route('gerente.recibos.descargar', $existente)
                ->with('success', 'Este recibo ya fue generado anteriormente.');
        }

        $orden->load([
            'vehiculo.cliente.persona',
            'empleado.persona',
            'servicios',
            'repuestos',
            'pagos',
        ]);

        // Número de recibo único
        $ultimo   = Recibo::whereYear('created_at', now()->year)->count();
        $numRecibo = 'REC-' . now()->year . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);

        // Generar PDF
        $pdf = Pdf::loadView('pdf.recibo', compact('orden', 'numRecibo'))
            ->setPaper('a4', 'portrait');

        $pdfContent = $pdf->output();

        // Subir a Cloudinary como raw
        $pdfUrl = null;
        try {
            $tmpPath = tempnam(sys_get_temp_dir(), 'recibo_') . '.pdf';
            file_put_contents($tmpPath, $pdfContent);

            $resultado = cloudinary()->upload($tmpPath, [
                'folder'        => 'taller-mecanico/recibos',
                'resource_type' => 'raw',
                'public_id'     => $numRecibo,
            ]);
            $pdfUrl = $resultado->getSecurePath();
            @unlink($tmpPath);
        } catch (\Exception $e) {
            // Si falla Cloudinary, igual guardamos el recibo sin URL
        }

        $recibo = Recibo::create([
            'orden_trabajo_id' => $orden->id,
            'numero_recibo'    => $numRecibo,
            'pdf_url'          => $pdfUrl,
            'total'            => $orden->costo_total ?? 0,
            'emitido_en'       => now(),
        ]);

        ActivityLogger::log("Recibo {$numRecibo} generado", 'ReciboController', $recibo->id);

        // Descargar directamente
        return $pdf->download("{$numRecibo}.pdf");
    }

    public function descargar(Recibo $recibo)
    {
        if ($recibo->pdf_url) {
            return redirect($recibo->pdf_url);
        }

        // Re-generar si no hay URL
        $recibo->load('orden.vehiculo.cliente.persona', 'orden.empleado.persona', 'orden.servicios', 'orden.repuestos', 'orden.pagos');
        $numRecibo = $recibo->numero_recibo;
        $orden     = $recibo->orden;

        $pdf = Pdf::loadView('pdf.recibo', compact('orden', 'numRecibo'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$numRecibo}.pdf");
    }
}
