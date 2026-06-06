<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = [
        'categoria_id', 'descripcion', 'monto', 'fecha',
        'comprobante_url', 'registrado_por', 'orden_trabajo_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_id');
    }

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
