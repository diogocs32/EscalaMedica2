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
        Schema::create('escalas_publicadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')->constrained('unidades')->onDelete('cascade');
            $table->foreignId('escala_padrao_id')->constrained('escalas_padrao')->onDelete('cascade');
            $table->integer('ano');
            $table->string('mes', 2); // '01' a '12'
            $table->enum('status', ['em_edicao', 'publicado', 'arquivado'])->default('em_edicao');
            $table->timestamps();

            // Índices para otimizar queries
            $table->index(['unidade_id', 'ano', 'mes']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalas_publicadas');
    }
};
