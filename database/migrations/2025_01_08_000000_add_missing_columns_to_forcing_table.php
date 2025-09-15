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
        // Verificar se a coluna já existe
        $hasColumn = Schema::hasColumn('forcing', 'data_liberacao');
        
        if (!$hasColumn) {
            Schema::table('forcing', function (Blueprint $table) {
                $table->timestamp('data_liberacao')->nullable();
            });
            
            // Migrar dados existentes
            DB::table('forcing')
                ->where('status', 'liberado')
                ->whereNotNull('liberador_id')
                ->whereNull('data_liberacao')
                ->update(['data_liberacao' => DB::raw('updated_at')]);
        }
        
        // Verificar se a coluna descricao_resolucao existe
        $hasDescricaoColumn = Schema::hasColumn('forcing', 'descricao_resolucao');
        
        if (!$hasDescricaoColumn) {
            Schema::table('forcing', function (Blueprint $table) {
                $table->text('descricao_resolucao')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $table->dropColumn(['data_liberacao', 'descricao_resolucao']);
        });
    }
};
