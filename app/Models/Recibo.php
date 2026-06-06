<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'recibos';

    protected $fillable = [
        'orden_trabajo_id', 'numero_recibo', 'pdf_url', 'total', 'emitido_en',
    ];

    protected $casts = [
        'emitido_en' => 'datetime',
        'total'      => 'decimal:2',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }
}
