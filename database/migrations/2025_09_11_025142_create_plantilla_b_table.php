<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plantilla_b', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escenario_id')->constrained('escenarios')->onDelete('cascade');
            $table->foreignId('formulario_id')->constrained('formularios')->onDelete('cascade');
            $table->string('tipo');
            $table->string('ubigeo')->nullable();
            $table->string('viviendas')->nullable();
            $table->string('poblacion')->nullable();
            $table->string('red_agua')->nullable();
            $table->string('reservorios')->nullable();
            $table->string('ptar')->nullable();
            $table->string('ptap')->nullable();
            $table->string('grupos_vulnerables')->nullable();
            $table->string('material_pared_predominante')->nullable();
            $table->string('red_vial_nacional')->nullable();
            $table->string('red_vial_departamental')->nullable();
            $table->string('red_vial_vecinal')->nullable();
            $table->string('puentes')->nullable();
            $table->string('red_ferroviaria')->nullable();
            $table->string('aerodromos')->nullable();
            $table->string('puertos')->nullable();
            $table->string('locales_educativos')->nullable();
            $table->string('poblacion_indigena')->nullable();
            $table->string('bienes_inmuebles')->nullable();
            $table->string('patrimonio_historico')->nullable();
            $table->string('museos')->nullable();
            $table->string('es_cr')->nullable();
            $table->string('es')->nullable();
            $table->string('vulnerabilidad')->nullable();
            $table->string('nivel_peligro_sismo')->nullable();
            $table->string('nivel_peligro_tsunami')->nullable();
            $table->string('nivel_peligro_glaciar')->nullable();
            $table->string('nivel_peligro_movimientos_masa')->nullable();
            $table->string('valor_riesgo_sismo')->nullable();
            $table->string('valor_riesgo_tsunami')->nullable();
            $table->string('valor_riesgo_glaciar')->nullable();
            $table->string('valor_riesgo_movimientos_masa')->nullable();
            $table->string('nr_sismo')->nullable();
            $table->string('nr_tsunami')->nullable();
            $table->string('nr_glaciar')->nullable();
            $table->string('nr_movimientos_masa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantilla_b');
    }
};
