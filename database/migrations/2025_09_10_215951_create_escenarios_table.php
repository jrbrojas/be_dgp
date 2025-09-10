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
        Schema::create('escenarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_formulario');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('nombre');
            $table->string('url_base');
            $table->string('plantilla_subida');
            $table->string('excel');
            $table->string('mapa_centro');
            $table->string('mapa_izquierda');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escenarios');
    }
};
