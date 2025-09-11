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
        Schema::create('plantilla_a', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('escenario_id');
            $table->unsignedInteger('formulario_id');
            $table->string('tipo');
            $table->string('cod_cp')->nullable();
            $table->string('cod_ubigeo')->nullable();
            $table->string('poblacion')->nullable();
            $table->string('vivienda')->nullable();
            $table->string('ie')->nullable();
            $table->string('is')->nullable();
            $table->string('nivel_riesgo')->nullable();
            $table->string('nivel_riesgo_agricola')->nullable();
            $table->string('nivel_riesgo_pecuario')->nullable();
            $table->string('cantidad_cp')->nullable();
            $table->string('nivel_susceptibilidad')->nullable();
            $table->string('nivel_exposicion_1_mm')->nullable();
            $table->string('nivel_exposicion_2_inu')->nullable();
            $table->string('nivel_exposicion_3_bt')->nullable();
            $table->string('alumnos')->nullable();
            $table->string('docentes')->nullable();
            $table->string('vias')->nullable();
            $table->string('superficie_agricola')->nullable();
            $table->string('pob_5')->nullable();
            $table->string('pob_60')->nullable();
            $table->string('pob_urb')->nullable();
            $table->string('pob_rural')->nullable();
            $table->string('viv_tipo1')->nullable();
            $table->string('viv_tipo2')->nullable();
            $table->string('viv_tipo3')->nullable();
            $table->string('viv_tipo4')->nullable();
            $table->string('viv_tipo5')->nullable();
            $table->string('hogares')->nullable();
            $table->string('sa_riego')->nullable();
            $table->string('sa_secano')->nullable();
            $table->string('prod_agropecuarios')->nullable();
            $table->string('prod_agropecuarios_65')->nullable();
            $table->string('superficie_de_pastos')->nullable();
            $table->string('alpacas')->nullable();
            $table->string('ovinos')->nullable();
            $table->string('vacunos')->nullable();
            $table->string('areas_naturales')->nullable();
            $table->string('nivel_sequia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantilla_a');
    }
};
