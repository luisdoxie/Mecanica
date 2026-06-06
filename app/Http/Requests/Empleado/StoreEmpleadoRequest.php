<?php

namespace App\Http\Requests\Empleado;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'ci'              => 'required|string|max:20|unique:personas,ci',
            'telefono'        => 'nullable|string|max:20',
            'email'           => 'required|email|max:150|unique:personas,email',
            'direccion'       => 'nullable|string|max:500',
            'cargo'           => 'required|string|max:100',
            'fecha_ingreso'   => 'required|date',
            'especialidades'  => 'nullable|array',
            'especialidades.*'=> 'exists:especialidades,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'        => 'El nombre es obligatorio.',
            'apellido.required'      => 'El apellido es obligatorio.',
            'ci.required'            => 'La cédula es obligatoria.',
            'ci.unique'              => 'Ya existe un empleado con esa cédula.',
            'email.required'         => 'El correo es obligatorio para crear el acceso web.',
            'email.email'            => 'El correo no tiene un formato válido.',
            'email.unique'           => 'Ya existe un registro con ese correo electrónico.',
            'cargo.required'         => 'El cargo es obligatorio.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
        ];
    }
}
