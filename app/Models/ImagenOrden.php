<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenOrden extends Model
{
    protected $table = 'imagenes_orden';

    protected $fillable = [
        'orden_trabajo_id', 'tipo', 'cloudinary_url', 'cloudinary_id', 'descripcion', 'subida_por',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }
}
