<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    /**
     * Autoriza la solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar un cliente existente.
     */
    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:150',
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:30|unique:clientes,numero_documento,' . $this->route('cliente')->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'vereda' => 'nullable|string|max:100',
            'finca' => 'nullable|string|max:100',
            'municipio' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
        ];
    }
}
