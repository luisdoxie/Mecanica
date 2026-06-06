<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepuestoUtilizado extends Model
{
    protected $table = 'repuestos_utilizados';

    protected $fillable = [
        'orden_id', 'nombre', 'origen', 'calidad_observada', 'costo', 'cantidad',
    ];

    protected $casts = [
        'costo'    => 'decimal:2',
        'cantidad' => 'integer',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_id');
    }
}
