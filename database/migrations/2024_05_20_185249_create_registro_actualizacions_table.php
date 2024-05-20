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
        Schema::create('registro_actualizacions', function (Blueprint $table) {
            $table->id();
            $table->uuid('exhortoId');
            $table->uuid('actualizacionOrigenId');
            $table->string('tipoActualizacion');
            $table->date('fechaHora');
            $table->string('descripcion');
            $table->foreign('exhortoId')->references('exhortoId')->on('respuesta_exhortos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_actualizacions');
    }
};
