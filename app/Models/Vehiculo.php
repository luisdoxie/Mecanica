<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'cliente_id', 'placa', 'marca', 'modelo', 'anio', 'km_actual', 'color',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class);
    }

    public function setPlacaAttribute(string $value): void
    {
        $this->attributes['placa'] = strtoupper(trim($value));
    }
}
