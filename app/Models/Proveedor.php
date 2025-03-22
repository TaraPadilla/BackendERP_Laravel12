<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'direccion',
        'municipio',
        'departamento',
    ];

    protected $dates = ['deleted_at'];
}
