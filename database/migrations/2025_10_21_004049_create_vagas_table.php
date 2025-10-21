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
        Schema::create('vagas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')->constrained('unidades')->onDelete('cascade');
            $table->foreignId('setor_id')->constrained('setores')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            $table->integer('quantidade_necessaria')->default(1);
            $table->text('observacoes')->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();

            // Índice único para evitar duplicatas
            $table->unique(['unidade_id', 'setor_id', 'turno_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vagas');
    }
};
