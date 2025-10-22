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
        Schema::create('alocacoes_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_padrao_id')->constrained('escalas_padrao')->cascadeOnDelete();
            $table->integer('semana'); // 1-5
            $table->integer('dia'); // 1-7 (segunda a domingo)
            $table->foreignId('turno_id')->constrained('turnos');
            $table->foreignId('setor_id')->constrained('setores');
            $table->integer('slot'); // número do buraco
            $table->foreignId('plantonista_id')->constrained('plantonistas');
            $table->timestamps();

            // Unique key: não permite alocar o mesmo slot duas vezes
            $table->unique(['escala_padrao_id', 'semana', 'dia', 'turno_id', 'setor_id', 'slot'], 'alocacoes_template_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alocacoes_template');
    }
};
