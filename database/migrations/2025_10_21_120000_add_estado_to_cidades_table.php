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
        Schema::table('cidades', function (Blueprint $table) {
            // adicionar coluna 'estado' (UF) com default para evitar quebra em dados existentes
            $table->char('estado', 2)->default('SP')->after('nome');

            // remover índice único antigo apenas em 'nome'
            $table->dropUnique('cidades_nome_unique');

            // adicionar índice único composto (nome + estado)
            $table->unique(['nome', 'estado'], 'cidades_nome_estado_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cidades', function (Blueprint $table) {
            // remover índice único composto
            $table->dropUnique('cidades_nome_estado_unique');

            // restaurar índice único antigo em 'nome'
            $table->unique('nome', 'cidades_nome_unique');

            // remover coluna 'estado'
            $table->dropColumn('estado');
        });
    }
};
