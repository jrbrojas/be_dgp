<?php

namespace App\Models\Models\Renat;

use Illuminate\Database\Eloquent\Model;

class VistaInstrumentos extends Model
{
    protected $connection = 'pgsql_renat'; // conexión secundaria
    protected $table = 'vista_personas_renat'; // tu vista en BD_RENAT
    public $incrementing = false;
    public $timestamps = false;
}
