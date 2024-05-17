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
        Schema::create('archivo_a_recibirs', function (Blueprint $table) {
            $table->id();
            $table->uuid('exhortoOrigenId');
            $table->string('nombreArchivo');
            $table->string('hashSha1')->nullable();
            $table->string('hashSha256')->nullable();
            $table->integer('tipoDocumento');
            $table->timestamps();
            $table->foreign('exhortoOrigenId')->references('exhortoOrigenId')->on('exhortos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivo_a_recibirs');
    }
};
