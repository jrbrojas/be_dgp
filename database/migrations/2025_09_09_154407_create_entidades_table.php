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
        Schema::create('entidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('departamento_id');
            $table->unsignedInteger('provincia_id');
            $table->unsignedInteger('distrito_id');
            $table->integer('ubigeo');
            $table->string('nombre', 255);
            $table->string('tipo', 255)->default('Municipalidad');
            $table->date('fecha_registro')->nullable();
            $table->integer('anio')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entidades');
    }
};
