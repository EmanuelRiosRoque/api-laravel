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
        Schema::create('acuses', function (Blueprint $table) {
            $table->id();
            $table->uuid('folioSeguimiento')->unique();

            $table->uuid('exhortoOrigenId');
            $table->foreign('exhortoOrigenId')->references('exhortoOrigenId')->on('exhortos')->onDelete('cascade');

            $table->unsignedBigInteger('municipioAreaRecibeId')->nullable();
            $table->foreign('municipioAreaRecibeId')->references('id')->on('municipios')->onDelete('cascade');

            $table->unsignedBigInteger('areaRecibeId')->nullable();
            $table->foreign('areaRecibeId')->references('id')->on('areas')->onDelete('cascade');

            $table->string('areaRecibeNombre')->nullable();
            $table->string('urlInfo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acuses');
    }
};
