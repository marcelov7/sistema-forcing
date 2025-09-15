<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adiciona campos relacionados à liberação de forcing para o Sistema de Controle de Forcing:
     * - data_liberacao: timestamp da liberação pelo liberador
     * - liberado_por: referência ao usuário liberador que aprovou o forcing
     * - observacoes_liberacao: observações do liberador durante a aprovação
     */
    public function up(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $columns = Schema::getColumnListing('forcing');
            
            // Adicionar data_liberacao se não existir
            if (!in_array('data_liberacao', $columns)) {
                $table->timestamp('data_liberacao')->nullable()->after('data_forcing');
            }
            
            // Adicionar liberado_por se não existir (referência ao usuário com perfil liberador)
            if (!in_array('liberado_por', $columns)) {
                $table->unsignedBigInteger('liberado_por')->nullable()->after('data_liberacao');
                $table->foreign('liberado_por')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null')
                      ->name('forcing_liberado_por_foreign');
                $table->index('liberado_por');
            }
            
            // Adicionar observacoes_liberacao se não existir
            if (!in_array('observacoes_liberacao', $columns)) {
                $table->text('observacoes_liberacao')->nullable()->after('liberado_por');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * Remove os campos adicionados para liberação de forcing
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            // Remover foreign key e índice primeiro
            if (Schema::hasColumn('forcing', 'liberado_por')) {
                $table->dropForeign('forcing_liberado_por_foreign');
                $table->dropIndex(['liberado_por']);
                $table->dropColumn('liberado_por');
            }
            
            // Remover observacoes_liberacao
            if (Schema::hasColumn('forcing', 'observacoes_liberacao')) {
                $table->dropColumn('observacoes_liberacao');
            }
            
            // Remover data_liberacao
            if (Schema::hasColumn('forcing', 'data_liberacao')) {
                $table->dropColumn('data_liberacao');
            }
        });
    }
};