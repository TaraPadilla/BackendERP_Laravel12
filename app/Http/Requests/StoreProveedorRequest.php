<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_completo'     => 'required|string|max:150',
            'tipo_documento'      => 'required|string|max:20',
            'numero_documento'    => 'required|string|max:30|unique:proveedores,numero_documento',
            'telefono'            => 'nullable|string|max:20',
            'direccion'           => 'nullable|string|max:255',
            'municipio'           => 'nullable|string|max:100',
            'departamento'        => 'nullable|string|max:100',
        ];
    }
}
