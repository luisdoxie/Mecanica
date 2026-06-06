<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = ['nombre', 'descripcion', 'precio_base', 'activo'];

    protected $casts = [
        'activo'     => 'boolean',
        'precio_base'=> 'decimal:2',
    ];

    public function ordenes()
    {
        return $this->belongsToMany(OrdenTrabajo::class, 'orden_servicio', 'servicio_id', 'orden_id')
                    ->withPivot('precio_aplicado', 'observaciones');
    }
}
