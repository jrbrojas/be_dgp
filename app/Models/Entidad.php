<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = 'entidades';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'ubigueo',
        'nombre',
        'tipo',
        'fecha_registro',
        'anio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_registro' => 'date',
        ];
    }
}
