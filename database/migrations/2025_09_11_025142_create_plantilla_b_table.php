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
            $table->string('ubigeo');
            $table->integer('viviendas')->default(0);
            $table->integer('poblacion')->default(0);
            $table->integer('red_agua')->default(0);
            $table->integer('reservorios')->default(0);
            $table->integer('ptar')->default(0);
            $table->integer('ptap')->default(0);
            $table->integer('grupos_vulnerables')->default(0);
            $table->integer('material_pared_predominante')->default(0);
            $table->decimal('red_vial_nacional', 10,3)->default(0.000);
            $table->decimal('red_vial_departamental', 10,3)->default(0.000);
            $table->decimal('red_vial_vecinal', 10,3)->default(0.000);
            $table->integer('puentes')->default(0);
            $table->decimal('red_ferroviaria', 10,3)->default(0.000);
            $table->integer('aerodromos')->default(0);
            $table->integer('puertos')->default(0);
            $table->integer('locales_educativos')->default(0);
            $table->integer('poblacion_indigena')->default(0);
            $table->integer('bienes_inmuebles')->default(0);
            $table->integer('patrimonio_historico')->default(0);
            $table->integer('museos')->default(0);
            $table->integer('es_cr')->default(0);
            $table->integer('es')->default(0);
            $table->string('vulnerabilidad')->nullable();
            $table->string('nivel_peligro_sismo')->nullable();
            $table->string('nivel_peligro_tsunami')->nullable();
            $table->string('nivel_peligro_glaciar')->nullable();
            $table->string('nivel_peligro_movimientos_masa')->nullable();
            $table->decimal('valor_riesgo_sismo', 10,3)->default(0.000);
            $table->decimal('valor_riesgo_tsunami', 10,3)->default(0.000);
            $table->decimal('valor_riesgo_glaciar', 10,3)->default(0.000);
            $table->decimal('valor_riesgo_movimientos_masa', 10,3)->default(0.000);
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
