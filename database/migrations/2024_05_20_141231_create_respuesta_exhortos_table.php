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
        Schema::create('respuesta_exhortos', function (Blueprint $table) {
            $table->id();
            $table->uuid('exhortoId');
            $table->uuid('respuestaOrigenId');
            $table->unsignedBigInteger('municipioTurnadoId');
            $table->string('areaTurnadoId')->nullable();
            $table->string('areaTurnadoNombre');
            $table->string('numeroExhorto')->nullable();
            $table->integer('tipoDiligenciado');
            $table->string('observaciones');
            // Se adjuntan los archivos en el modelo
            $table->foreign('exhortoId')->references('exhortoOrigenId')->on('exhortos')->onDelete('cascade');
            $table->foreign('municipioTurnadoId')->references('id')->on('municipios')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuesta_exhortos');
    }
};
