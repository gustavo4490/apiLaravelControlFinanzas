<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'saldo',
        'icono',
        'idusuario',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
