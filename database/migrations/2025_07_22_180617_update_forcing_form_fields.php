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
        Schema::table('forcing', function (Blueprint $table) {
            // Remover campos título e descrição
            $table->dropColumn(['titulo', 'descricao']);
            
            // Adicionar campo para descrição da resolução
            $table->text('descricao_resolucao')->nullable()->after('observacoes_retirada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            // Restaurar campos título e descrição
            $table->string('titulo')->after('id');
            $table->text('descricao')->nullable()->after('titulo');
            
            // Remover campo de resolução
            $table->dropColumn('descricao_resolucao');
        });
    }
};
