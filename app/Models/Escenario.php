<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'subtitulo',
        'titulo_base',
        'aviso',
        'url_base',
        'plantilla_subida',
        'excel_adjunto',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function scopeSearch(Builder $query, $value)
    {
        $query->where('nombre', 'ilike', "%{$value}%")
            ->orWhere('titulo_base', 'ilike', "%{$value}%")
            ->orWhere('aviso', 'ilike', "%{$value}%")
            ->orWhere('fecha_inicio', 'ilike', "%{$value}%")
            ->orWhere('fecha_fin', 'ilike', "%{$value}%")
            ->orWhere('url_base', 'ilike', "%{$value}%")
            ->orWhereHas('formulario', function ($query) use ($value) {
                $query->where('nombre', 'ilike', "%{$value}%")
                    ->orWhere('peligro', 'ilike', "%{$value}%");
            });
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

    public function mapas()
    {
        return $this->hasMany(Mapa::class);
    }

    public static function getByFormulario(Escenario $escenario)
    {
        switch ($escenario->formulario_id) {
            case 1:
                return PlantillaA::getByFormularioAvisoMeteorologico($escenario);

            case 2:
                return PlantillaA::getByFormularioAvisoTrimestral($escenario);

            case 3:
                return PlantillaA::getByFormularioInformacionClimatica($escenario);

            case 4:
                return PlantillaA::getByFormularioBajasTempAvisoMeteorologico($escenario);

            case 5:
                return PlantillaA::getByFormularioBajasTempAvisoTrimestral($escenario);

            case 6:
                return PlantillaA::getByFormularioBajasTempInformacionClimatica($escenario);

            case 7:
                return PlantillaA::getByFormularioIncForestalesNacionales($escenario);

            case 8:
                return PlantillaA::getByFormularioIncForestalesRegionales($escenario);

            case 9:
                return PlantillaB::getByFormularioSismosTsunamiNacional($escenario);

            default:
                return []; // O puedes lanzar una excepción si quieres forzar el manejo de errores
        }
    }
}
