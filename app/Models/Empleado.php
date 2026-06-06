<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'persona_id', 'cargo', 'fecha_ingreso', 'salario', 'activo',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'activo'        => 'boolean',
        'salario'       => 'decimal:2',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class, 'empleado_especialidad');
    }

    public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoEmpleado::class);
    }
}
