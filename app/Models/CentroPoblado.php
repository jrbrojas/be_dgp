<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroPoblado extends Model
{
    protected $fillable = [
        'distrito_id',
        'codigo',
        'nombre',
    ];

}
