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
        Schema::create('exhortos', function (Blueprint $table) {
            $table->id();
            $table->uuid('exhortoOrigenId')->unique();
            $table->unsignedBigInteger('municipioDestinoId');
            $table->string('materiaClave');
            $table->unsignedBigInteger('estadoOrigenId');
            $table->unsignedBigInteger('municipioOrigenId');
            $table->unsignedBigInteger('juzgadoOrigenId')->nullable();
            $table->string('juzgadoOrigenNombre');
            $table->string('numeroExpedienteOrigen');
            $table->string('numeroOficioOrigen')->nullable();
            $table->string('tipoJuicioAsuntoDelitos');
            $table->string('juezExhortante')->nullable();
            $table->integer('fojas');
            $table->integer('diasResponder');
            $table->string('tipoDiligenciacionNombre')->nullable();
            $table->date('fechaOrigen')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->foreign('estadoOrigenId')->references('id')->on('estados')->onDelete('cascade');
            $table->foreign('municipioOrigenId')->references('id')->on('municipios')->onDelete('cascade');
            $table->foreign('juzgadoOrigenId')->references('id')->on('juzgados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhortos');
    }
};
