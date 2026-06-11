<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $personaId = $this->route('cliente')->persona_id;

        return [
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'ci'        => "required|string|max:20|unique:personas,ci,{$personaId}",
            'telefono'  => 'nullable|string|max:20',
            'email'     => "nullable|email|max:150|unique:personas,email,{$personaId}|required_if:puede_login,1",
            'direccion' => 'nullable|string|max:500',
            'puede_login' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre es obligatorio.',
            'apellido.required'    => 'El apellido es obligatorio.',
            'ci.required'          => 'La cédula de identidad es obligatoria.',
            'ci.unique'            => 'Ya existe un cliente registrado con esa cédula.',
            'email.email'          => 'El correo electrónico no tiene un formato válido.',
            'email.unique'         => 'El correo ya está registrado.',
            'email.required_if'    => 'El correo es obligatorio si se activa el acceso web.',
        ];
    }
}
