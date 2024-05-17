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
        Schema::create('exhorto_archivos', function (Blueprint $table) {
            $table->id();
            $table->uuid('exhortoOrigenId');
            $table->foreign('exhortoOrigenId')->references('exhortoOrigenId')->on('exhortos')->onDelete('cascade');
            $table->string('archivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhorto_archivos');
    }
};
