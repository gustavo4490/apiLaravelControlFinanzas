<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'cantidad',
        'motivo',
        'id_tarjeta',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relación con el modelo CreditCard (tarjeta de crédito).
     * Un pago pertenece a una tarjeta de crédito.
     */
    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class, 'id_tarjeta');
    }
}
