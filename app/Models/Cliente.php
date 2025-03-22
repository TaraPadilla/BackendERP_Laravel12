<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'direccion',
        'vereda',
        'finca',
        'municipio',
        'departamento'
    ];

    protected $dates = ['deleted_at'];
}
