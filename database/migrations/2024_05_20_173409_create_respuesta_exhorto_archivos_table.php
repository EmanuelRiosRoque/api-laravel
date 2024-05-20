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
        Schema::create('respuesta_exhorto_archivos', function (Blueprint $table) {
            $table->id();
            $table->uuid('exhortoId');
            $table->uuid('respuestaOrigenId');
            $table->foreign('exhortoId')->references('exhortoOrigenId')->on('exhortos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuesta_exhorto_archivos');
    }
};
