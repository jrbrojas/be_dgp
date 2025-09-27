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
            $table->foreignId('formulario_id')->constrained('formularios')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('nombre');
            $table->string('subtitulo')->nullable();
            $table->string('titulo_base')->nullable();
            $table->smallInteger('aviso')->nullable();
            $table->string('url_base');
            $table->string('plantilla_subida')->nullable();
            $table->string('excel')->nullable();
            $table->string('mapa_centro')->nullable();
            $table->string('mapa_izquierdo')->nullable();
            $table->string('mapa_derecho')->nullable();
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
