<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigTaller extends Model
{
    protected $table = 'config_taller';

    protected $fillable = [
        'clave', 'valor', 'descripcion', 'tipo',
    ];
}
