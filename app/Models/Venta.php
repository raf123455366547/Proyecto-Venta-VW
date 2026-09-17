<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    public $timestamps = true;
    
    protected $table = 'ventas';

    protected $fillable = [
        'producto_id',
        'user_id',
        'cantidad',
        'precio_unitario',
        'total',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}