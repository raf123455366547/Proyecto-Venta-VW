<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
