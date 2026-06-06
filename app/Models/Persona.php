<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'nombre', 'apellido', 'ci', 'telefono', 'email', 'direccion',
    ];

    public function empleado()
    {
        return $this->hasOne(Empleado::class);
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
