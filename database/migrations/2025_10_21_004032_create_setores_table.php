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
        Schema::create('setores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->foreignId('unidade_id')->constrained('unidades')->onDelete('cascade');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();

            $table->unique(['nome', 'unidade_id']); // Setores únicos por unidade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setores');
    }
};
