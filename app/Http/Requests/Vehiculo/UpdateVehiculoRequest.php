<?php

namespace App\Http\Requests\Vehiculo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehiculoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $vehiculoId = $this->route('vehiculo')->id;

        return [
            'cliente_id' => 'required|exists:clientes,id',
            'placa'      => "required|string|max:20|unique:vehiculos,placa,{$vehiculoId}",
            'marca'      => 'required|string|max:80',
            'modelo'     => 'required|string|max:80',
            'anio'       => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'km_actual'  => 'nullable|integer|min:0',
            'color'      => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'placa.unique'   => 'Ya existe un vehículo registrado con esa placa.',
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required'=> 'El modelo es obligatorio.',
            'anio.required'  => 'El año es obligatorio.',
        ];
    }
}
