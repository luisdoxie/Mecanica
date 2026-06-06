<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoOrden extends Model
{
    protected $table = 'pagos_orden';

    protected $fillable = [
        'orden_trabajo_id', 'monto', 'metodo_pago', 'estado',
        'fecha_pago', 'registrado_por', 'observacion',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto'      => 'decimal:2',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
