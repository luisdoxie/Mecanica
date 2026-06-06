<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    protected $table = 'historial_estados';
    public $timestamps = false;

    protected $fillable = [
        'orden_id', 'empleado_id', 'estado_anterior', 'estado_nuevo', 'nota', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
