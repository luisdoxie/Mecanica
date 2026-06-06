<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\ReporteOrdenesExport;
use App\Exports\ReporteFinancieroExport;
use App\Exports\ReporteCobrosExport;
use App\Exports\ReporteServiciosExport;
use App\Exports\ReporteClientesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    public function ordenes(Request $request)
    {
        $desde  = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta  = $request->get('hasta', now()->toDateString());
        $formato = $request->get('formato', 'pdf');

        $ordenes = DB::table('ordenes_trabajo')
            ->join('vehiculos', 'ordenes_trabajo.vehiculo_id', '=', 'vehiculos.id')
            ->join('clientes', 'vehiculos.cliente_id', '=', 'clientes.id')
            ->join('personas', 'clientes.persona_id', '=', 'personas.id')
            ->leftJoin('empleados', 'ordenes_trabajo.empleado_id', '=', 'empleados.id')
            ->leftJoin('personas as pe', 'empleados.persona_id', '=', 'pe.id')
            ->whereBetween('ordenes_trabajo.fecha_ingreso', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->select(
                'ordenes_trabajo.id',
                'ordenes_trabajo.estado',
                'ordenes_trabajo.costo_total',
                'ordenes_trabajo.fecha_ingreso',
                'vehiculos.placa',
                'vehiculos.marca',
                'vehiculos.modelo',
                DB::raw("personas.nombre || ' ' || personas.apellido as cliente"),
                DB::raw("COALESCE(pe.nombre || ' ' || pe.apellido, 'Sin asignar') as mecanico")
            )
            ->orderByDesc('ordenes_trabajo.fecha_ingreso')
            ->get();

        if ($formato === 'excel') {
            return Excel::download(new ReporteOrdenesExport($ordenes), "ordenes_{$desde}_{$hasta}.xlsx");
        }

        $pdf = Pdf::loadView('pdf.reportes.ordenes', compact('ordenes', 'desde', 'hasta'))
            ->setPaper('a4', 'landscape');
        return $pdf->download("ordenes_{$desde}_{$hasta}.pdf");
    }

    public function financiero(Request $request)
    {
        $mes     = $request->get('mes', now()->format('Y-m'));
        $formato = $request->get('formato', 'pdf');

        [$anio, $mesNum] = explode('-', $mes);

        $ingresos = DB::table('pagos_orden')
            ->where('estado', 'PAGADO')
            ->whereYear('fecha_pago', $anio)->whereMonth('fecha_pago', $mesNum)
            ->sum('monto');
        $gastos = DB::table('gastos')
            ->whereYear('fecha', $anio)->whereMonth('fecha', $mesNum)
            ->sum('monto');
        $salarios = DB::table('pagos_empleado')
            ->where('periodo', $mes)->sum('monto');
        $ganancia = $ingresos - $gastos - $salarios;

        $detalleGastos = DB::table('gastos')
            ->join('categorias_gasto', 'gastos.categoria_id', '=', 'categorias_gasto.id')
            ->whereYear('gastos.fecha', $anio)->whereMonth('gastos.fecha', $mesNum)
            ->select('categorias_gasto.nombre as categoria', DB::raw('SUM(gastos.monto) as total'))
            ->groupBy('categorias_gasto.nombre')->get();

        $data = compact('mes', 'ingresos', 'gastos', 'salarios', 'ganancia', 'detalleGastos');

        if ($formato === 'excel') {
            return Excel::download(new ReporteFinancieroExport($data), "financiero_{$mes}.xlsx");
        }

        $pdf = Pdf::loadView('pdf.reportes.financiero', $data)->setPaper('a4', 'portrait');
        return $pdf->download("financiero_{$mes}.pdf");
    }

    public function cobros(Request $request)
    {
        $formato = $request->get('formato', 'pdf');

        $cobros = DB::table('pagos_orden')
            ->join('ordenes_trabajo', 'pagos_orden.orden_trabajo_id', '=', 'ordenes_trabajo.id')
            ->join('vehiculos', 'ordenes_trabajo.vehiculo_id', '=', 'vehiculos.id')
            ->join('clientes', 'vehiculos.cliente_id', '=', 'clientes.id')
            ->join('personas', 'clientes.persona_id', '=', 'personas.id')
            ->whereIn('pagos_orden.estado', ['PENDIENTE', 'PARCIAL', 'FIADO'])
            ->select(
                'pagos_orden.id',
                'pagos_orden.estado',
                'pagos_orden.monto',
                'pagos_orden.metodo_pago',
                'pagos_orden.created_at',
                'ordenes_trabajo.id as orden_id',
                'vehiculos.placa',
                DB::raw("personas.nombre || ' ' || personas.apellido as cliente"),
                DB::raw("personas.telefono")
            )
            ->orderBy('pagos_orden.created_at')
            ->get();

        if ($formato === 'excel') {
            return Excel::download(new ReporteCobrosExport($cobros), 'cuentas_por_cobrar.xlsx');
        }

        $pdf = Pdf::loadView('pdf.reportes.cobros', compact('cobros'))->setPaper('a4', 'portrait');
        return $pdf->download('cuentas_por_cobrar.pdf');
    }

    public function servicios(Request $request)
    {
        $formato = $request->get('formato', 'pdf');

        $servicios = DB::table('orden_servicio')
            ->join('servicios', 'orden_servicio.servicio_id', '=', 'servicios.id')
            ->select('servicios.nombre', DB::raw('COUNT(*) as veces'), DB::raw('SUM(orden_servicio.precio_aplicado) as total_generado'))
            ->groupBy('servicios.nombre')
            ->orderByDesc('veces')->get();

        if ($formato === 'excel') {
            return Excel::download(new ReporteServiciosExport($servicios), 'servicios_solicitados.xlsx');
        }

        $pdf = Pdf::loadView('pdf.reportes.servicios', compact('servicios'))->setPaper('a4', 'portrait');
        return $pdf->download('servicios_solicitados.pdf');
    }

    public function clientes(Request $request)
    {
        $formato = $request->get('formato', 'pdf');

        $clientes = DB::table('ordenes_trabajo')
            ->join('vehiculos', 'ordenes_trabajo.vehiculo_id', '=', 'vehiculos.id')
            ->join('clientes', 'vehiculos.cliente_id', '=', 'clientes.id')
            ->join('personas', 'clientes.persona_id', '=', 'personas.id')
            ->select(
                DB::raw("personas.nombre || ' ' || personas.apellido as cliente"),
                'personas.ci',
                'personas.telefono',
                DB::raw('COUNT(ordenes_trabajo.id) as total_ordenes'),
                DB::raw('SUM(ordenes_trabajo.costo_total) as total_gastado')
            )
            ->groupBy('personas.nombre', 'personas.apellido', 'personas.ci', 'personas.telefono')
            ->orderByDesc('total_ordenes')->limit(20)->get();

        if ($formato === 'excel') {
            return Excel::download(new ReporteClientesExport($clientes), 'clientes_frecuentes.xlsx');
        }

        $pdf = Pdf::loadView('pdf.reportes.clientes', compact('clientes'))->setPaper('a4', 'portrait');
        return $pdf->download('clientes_frecuentes.pdf');
    }
}
