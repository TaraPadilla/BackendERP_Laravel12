<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'venta_id',
        'monto',
        'fecha',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
