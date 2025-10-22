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
        Schema::create('alocacoes_publicadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_publicada_id')->constrained('escalas_publicadas')->onDelete('cascade');
            $table->date('data');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            $table->foreignId('setor_id')->constrained('setores')->onDelete('cascade');
            $table->foreignId('plantonista_id')->nullable()->constrained('plantonistas')->onDelete('set null');
            $table->enum('status', ['vago', 'preenchido', 'confirmado'])->default('vago');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índices para otimizar queries
            $table->index(['escala_publicada_id', 'data']);
            $table->index(['data', 'turno_id', 'setor_id']);
            $table->index('plantonista_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alocacoes_publicadas');
    }
};
