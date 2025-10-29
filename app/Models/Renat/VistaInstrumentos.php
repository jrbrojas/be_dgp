<?php

namespace App\Models\Renat;

use Illuminate\Database\Eloquent\Model;

class VistaInstrumentos extends Model
{
    protected $connection = 'pgsql_renat';
    protected $table = 'vista_unificada_planes_instrumentos_fortalecimiento';
    public $incrementing = false;
    public $timestamps = false;

}
