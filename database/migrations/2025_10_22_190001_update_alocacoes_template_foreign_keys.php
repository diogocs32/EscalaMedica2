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
        Schema::table('alocacoes_template', function (Blueprint $table) {
            // Drop existing FKs
            $table->dropForeign(['setor_id']);
            $table->dropForeign(['turno_id']);
            $table->dropForeign(['plantonista_id']);

            // Recreate with cascade on delete to allow deleting referenced rows
            $table->foreign('setor_id')
                ->references('id')->on('setores')
                ->cascadeOnDelete();

            $table->foreign('turno_id')
                ->references('id')->on('turnos')
                ->cascadeOnDelete();

            $table->foreign('plantonista_id')
                ->references('id')->on('plantonistas')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alocacoes_template', function (Blueprint $table) {
            $table->dropForeign(['setor_id']);
            $table->dropForeign(['turno_id']);
            $table->dropForeign(['plantonista_id']);

            // Recreate without cascading (default RESTRICT/NO ACTION)
            $table->foreign('setor_id')
                ->references('id')->on('setores');

            $table->foreign('turno_id')
                ->references('id')->on('turnos');

            $table->foreign('plantonista_id')
                ->references('id')->on('plantonistas');
        });
    }
};
