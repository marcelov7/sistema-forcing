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
        // Corrigir datas de liberação para forcings existentes
        try {
            // Atualizar forcings liberados que não têm data_liberacao
            DB::statement("
                UPDATE forcing 
                SET data_liberacao = updated_at 
                WHERE status = 'liberado' 
                AND data_liberacao IS NULL
            ");
            
            // Atualizar forcings executados que não têm data_liberacao
            DB::statement("
                UPDATE forcing 
                SET data_liberacao = created_at 
                WHERE status IN ('forcado') 
                AND data_liberacao IS NULL
            ");
        } catch (\Exception $e) {
            // Log do erro mas não falha a migration
            \Log::warning('Erro ao corrigir datas de liberação: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não há necessidade de reverter esta correção
        // pois são dados históricos
    }
};