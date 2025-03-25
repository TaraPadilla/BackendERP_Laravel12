<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'fecha_venta',
        'tipo_pago',
        'cliente_id',
        'factura_id',
        'total',
        'abono_inicial',
        'cuotas',
        'saldo_pendiente',
        'usuario_id',
    ];

    // Relaciones

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function aplicarAbono($monto)
    {
        if ($this->saldo_pendiente === null) return;

        $this->saldo_pendiente -= $monto;

        if ($this->saldo_pendiente < 0) {
            throw new \Exception('El monto del pago excede el saldo pendiente.');
        }

        $this->save();
    }

}
