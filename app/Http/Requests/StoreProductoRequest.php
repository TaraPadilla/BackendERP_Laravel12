<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Asumes que no hay autenticación en este MVP
    }

    public function rules()
    {
        return [
            'descripcion'       => 'required|string|max:255',
            'categoria_id'      => 'required|exists:categorias,id',
            'proveedor_id'      => 'nullable|exists:proveedores,id',
            'referencia'        => ['required', 'string', 'max:100', 'unique:productos,referencia'],
            'color'             => 'nullable|string|max:50',
            'talla'             => 'nullable|string|max:50',
            'marca'             => 'nullable|string|max:100',
            'precio_compra'     => 'required|numeric|min:0',
            'porcentaje_venta'  => 'nullable|numeric|min:0|max:100',
            'precio_venta'      => 'required|numeric|min:0',
            'ganancia'          => 'nullable|numeric|min:0',
            'stock'             => 'nullable|integer|min:0',
        ];
    }
}
