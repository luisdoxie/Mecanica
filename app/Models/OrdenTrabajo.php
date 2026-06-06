<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'vehiculo_id', 'empleado_id', 'estado',
        'descripcion_problema', 'diagnostico', 'costo_total',
        'fecha_ingreso', 'fecha_entrega',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_entrega' => 'datetime',
        'costo_total'   => 'decimal:2',
    ];

    const ESTADOS = ['RECIBIDO', 'DIAGNOSTICO', 'REPARACION', 'LISTO', 'ENTREGADO'];

    public function siguienteEstado(): ?string
    {
        $idx = array_search($this->estado, self::ESTADOS);
        return isset(self::ESTADOS[$idx + 1]) ? self::ESTADOS[$idx + 1] : null;
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'orden_servicio', 'orden_id', 'servicio_id')
                    ->withPivot('precio_aplicado', 'observaciones');
    }

    public function repuestos()
    {
        return $this->hasMany(RepuestoUtilizado::class, 'orden_id');
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'orden_id')->orderBy('created_at');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenOrden::class, 'orden_trabajo_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoOrden::class);
    }
}
