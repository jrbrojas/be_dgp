<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $fillable = [
        'departamento_id',
        'codigo',
        'nombre',
        'latitud',
        'longitud',
    ];
}
