<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('partes', function (Blueprint $table) {
            $table->id();
            $table->uuid('exhortoOrigenId');
            $table->string('nombre');
            $table->string('apellidoPaterno')->nullable();
            $table->string('apellidoMaterno')->nullable();
            $table->string('genero')->nullable();
            $table->boolean('esPersonaMoral');
            $table->integer('tipoParte');
            $table->string('tipoParteNombre')->nullable();
            $table->timestamps();
            $table->foreign('exhortoOrigenId')->references('exhortoOrigenId')->on('exhortos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partes');
    }
};
