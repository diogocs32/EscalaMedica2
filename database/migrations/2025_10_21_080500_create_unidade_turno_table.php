<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unidade_turno', function (Blueprint $table) {
            $table->unsignedBigInteger('unidade_id');
            $table->unsignedBigInteger('turno_id');

            $table->foreign('unidade_id')->references('id')->on('unidades')->onDelete('cascade');
            $table->foreign('turno_id')->references('id')->on('turnos')->onDelete('cascade');

            $table->unique(['unidade_id', 'turno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidade_turno');
    }
};
