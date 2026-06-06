<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGasto extends Model
{
    protected $table = 'categorias_gasto';

    protected $fillable = ['nombre', 'tipo'];

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'categoria_id');
    }
}
