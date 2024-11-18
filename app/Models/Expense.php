<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'empresa',
        'cantidad',
        'detalle',
        'id_tarjeta',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Accesor para formatear la cantidad
    public function getFormattedCantidadAttribute()
    {
        return number_format($this->cantidad, 2, '.', ',');
    }

}
