<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistema de Escala Padrão Rotativa (5 semanas)
     * 
     * Estrutura:
     * - Cada unidade tem UMA escala padrão
     * - Escala padrão tem 5 semanas (template que se repete)
     * - Cada semana tem 7 dias
     * - Cada dia tem turnos configurados
     * - Cada turno+dia tem setores e vagas
     */
    public function up(): void
    {
        // Tabela: Escala Padrão da Unidade (template mestre)
        Schema::create('escalas_padrao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')->constrained('unidades')->onDelete('cascade');
            $table->string('nome')->default('Escala Padrão'); // Ex: "Escala Padrão 2025"
            $table->text('descricao')->nullable();
            $table->enum('status', ['ativo', 'inativo', 'arquivado'])->default('ativo');
            $table->date('vigencia_inicio')->nullable(); // Quando começou a valer
            $table->timestamps();

            $table->unique(['unidade_id']); // Cada unidade tem apenas UMA escala padrão ativa
        });

        // Tabela: Configuração de cada SEMANA da escala (1 a 5)
        Schema::create('semanas_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_padrao_id')->constrained('escalas_padrao')->onDelete('cascade');
            $table->integer('numero_semana'); // 1, 2, 3, 4, 5
            $table->string('nome')->nullable(); // Ex: "Semana A", "Semana de Alta Demanda"
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['escala_padrao_id', 'numero_semana']);
        });

        // Tabela: Configuração de cada DIA dentro de uma semana
        Schema::create('dias_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semana_template_id')->constrained('semanas_template')->onDelete('cascade');
            $table->enum('dia_semana', ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo']);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['semana_template_id', 'dia_semana']);
        });

        // Tabela: Configuração TURNO + SETOR + VAGAS para cada dia
        Schema::create('configuracoes_turno_setor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dia_template_id')->constrained('dias_template')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            $table->foreignId('setor_id')->constrained('setores')->onDelete('cascade');
            $table->integer('quantidade_necessaria')->default(1); // Quantos médicos
            $table->text('observacoes')->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();

            // Não pode ter turno+setor duplicado no mesmo dia
            $table->unique(['dia_template_id', 'turno_id', 'setor_id'], 'unique_dia_turno_setor');
        });

        // Índices para performance
        Schema::table('semanas_template', function (Blueprint $table) {
            $table->index(['escala_padrao_id', 'numero_semana']);
        });

        Schema::table('dias_template', function (Blueprint $table) {
            $table->index(['semana_template_id', 'dia_semana']);
        });

        Schema::table('configuracoes_turno_setor', function (Blueprint $table) {
            $table->index(['dia_template_id', 'turno_id', 'setor_id'], 'idx_config_dia_turno_setor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracoes_turno_setor');
        Schema::dropIfExists('dias_template');
        Schema::dropIfExists('semanas_template');
        Schema::dropIfExists('escalas_padrao');
    }
};
