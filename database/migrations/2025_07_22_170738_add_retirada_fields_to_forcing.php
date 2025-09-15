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
            $table->timestamp('data_solicitacao_retirada')->nullable()->after('data_execucao');
            $table->text('observacoes_solicitacao')->nullable()->after('data_solicitacao_retirada');
            $table->unsignedBigInteger('retirado_por')->nullable()->after('observacoes_solicitacao');
            $table->text('observacoes_retirada')->nullable()->after('retirado_por');
            
            // Foreign key para quem retirou o forcing
            $table->foreign('retirado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $table->dropForeign(['retirado_por']);
            $table->dropColumn([
                'data_solicitacao_retirada',
                'observacoes_solicitacao', 
                'retirado_por',
                'observacoes_retirada'
            ]);
        });
    }
};
