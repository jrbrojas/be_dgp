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
            $table->foreignId('escenario_id')->constrained('escenarios')->onDelete('cascade');
            $table->string('tipo');
            $table->string('cod_cp')->nullable();
            $table->string('cod_ubigeo')->nullable();
            $table->integer('poblacion')->default(0);
            $table->integer('vivienda')->default(0);
            $table->integer('ie')->default(0);
            $table->integer('es')->default(0);
            $table->string('nivel_riesgo')->nullable();
            $table->string('nivel_riesgo_agricola')->nullable();
            $table->string('nivel_riesgo_pecuario')->nullable();
            $table->integer('cantidad_cp')->default(0);
            $table->string('nivel_susceptibilidad')->nullable();
            $table->string('nivel_exposicion_1_mm')->nullable();
            $table->string('nivel_exposicion_2_inu')->nullable();
            $table->string('nivel_exposicion_3_bt')->nullable();
            $table->integer('alumnos')->default(0);
            $table->integer('docentes')->default(0);
            $table->decimal('vias', 8,2)->default(0.00);
            $table->decimal('superficie_agricola', 8,2)->default(0.00);
            $table->integer('pob_5')->default(0);
            $table->integer('pob_60')->default(0);
            $table->integer('pob_urb')->default(0);
            $table->integer('pob_rural')->default(0);
            $table->integer('viv_tipo1')->default(0);
            $table->integer('viv_tipo2')->default(0);
            $table->integer('viv_tipo3')->default(0);
            $table->integer('viv_tipo4')->default(0);
            $table->integer('viv_tipo5')->default(0);
            $table->integer('hogares')->default(0);
            $table->decimal('sa_riego', 8,2)->default(0.00);
            $table->decimal('sa_secano', 8,2)->default(0.00);
            $table->integer('prod_agropecuarios')->default(0);
            $table->decimal('prod_agropecuarios_65', 8,2)->default(0.00);
            $table->decimal('superficie_de_pastos', 8,2)->default(0.00);
            $table->integer('alpacas')->default(0);
            $table->integer('ovinos')->default(0);
            $table->integer('vacunos')->default(0);
            $table->integer('areas_naturales')->default(0);
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
