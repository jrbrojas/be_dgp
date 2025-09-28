<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapa extends Model
{
    protected $fillable = [
        'escenario_id',
        'tipo',
        'ruta',
    ];
}
