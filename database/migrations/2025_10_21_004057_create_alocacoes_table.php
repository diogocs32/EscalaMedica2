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
        Schema::create('alocacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantonista_id')->constrained('plantonistas')->onDelete('cascade');
            $table->foreignId('vaga_id')->constrained('vagas')->onDelete('cascade');
            $table->date('data_plantao');
            $table->datetime('data_hora_inicio');
            $table->datetime('data_hora_fim');
            $table->enum('status', ['agendado', 'em_andamento', 'concluido', 'cancelado'])->default('agendado');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['plantonista_id', 'data_hora_inicio', 'data_hora_fim']);
            $table->index(['data_plantao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alocacoes');
    }
};
