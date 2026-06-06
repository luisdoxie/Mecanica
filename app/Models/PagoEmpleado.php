<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoEmpleado extends Model
{
    protected $table = 'pagos_empleado';

    protected $fillable = [
        'empleado_id', 'periodo', 'monto', 'fecha_pago', 'observacion',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto'      => 'decimal:2',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
