<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alocacoes_template', function (Blueprint $table) {
            $table->dropForeign(['plantonista_id']);
            $table->unsignedBigInteger('plantonista_id')->nullable()->change();
            $table->foreign('plantonista_id')
                ->references('id')->on('plantonistas')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alocacoes_template', function (Blueprint $table) {
            $table->dropForeign(['plantonista_id']);
            $table->unsignedBigInteger('plantonista_id')->nullable(false)->change();
            $table->foreign('plantonista_id')
                ->references('id')->on('plantonistas')
                ->onDelete('cascade');
        });
    }
};
