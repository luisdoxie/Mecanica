<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table    = 'activity_log';
    public    $timestamps = false;

    protected $fillable = [
        'usuario_id', 'rol', 'accion', 'modulo', 'registro_id',
        'datos_anteriores', 'datos_nuevos', 'ip', 'dispositivo', 'created_at',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
        'created_at'       => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
