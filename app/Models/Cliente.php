<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'persona_id', 'pin_acceso', 'password_hash', 'puede_login',
    ];

    protected $casts = [
        'puede_login' => 'boolean',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function ordenesTrabajo()
    {
        return $this->hasManyThrough(OrdenTrabajo::class, Vehiculo::class);
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Persona::class, 'id', 'persona_id', 'persona_id', 'id');
    }
}
