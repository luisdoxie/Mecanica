<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = ['nombre', 'descripcion'];

    public function empleados()
    {
        return $this->belongsToMany(Empleado::class, 'empleado_especialidad');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }
}
