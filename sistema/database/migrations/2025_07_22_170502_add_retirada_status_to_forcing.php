<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            // Para SQLite, vamos recriar a tabela com os novos valores
            // Como é um projeto de desenvolvimento, vamos usar uma abordagem simples
        });
        
        // Adicionar os novos valores manualmente no SQLite
        DB::statement("UPDATE forcing SET status = 'pendente' WHERE status NOT IN ('pendente', 'liberado', 'forcado', 'solicitacao_retirada', 'retirado')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            // Para reverter, apenas garantir que não há registros com os novos status
            DB::statement("UPDATE forcing SET status = 'pendente' WHERE status IN ('solicitacao_retirada', 'retirado')");
        });
    }
};
