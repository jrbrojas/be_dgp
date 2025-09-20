<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $fillable = [
        'provincia_id',
        'codigo',
        'nombre',
        'capital',
    ];

}
