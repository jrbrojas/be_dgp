<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Escenario extends Model
{
    use HasFactory;

    protected $fillable = [
        'formulario_id',
        'fecha_inicio',
        'fecha_fin',
        'nombre',
        'url_base',
        'plantilla_subida',
        'excel',
        'mapa_centro',
        'mapa_izquierda',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function formulario(): BelongsTo
    {
        return $this->belongsTo(Formulario::class);
    }

    public function plantillasA()
    {
        return $this->hasMany(PlantillaA::class);
    }

    public function plantillasB()
    {
        return $this->hasMany(PlantillaB::class);
    }
}
