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
        // Atualizar o ENUM status para incluir novos valores
        try {
            DB::statement("
                ALTER TABLE forcing 
                MODIFY COLUMN status ENUM(
                    'pendente', 
                    'liberado', 
                    'forcado', 
                    'solicitacao_retirada', 
                    'retirado'
                ) NOT NULL DEFAULT 'pendente'
            ");
        } catch (\Exception $e) {
            // Fallback para diferentes versões do MySQL
            try {
                DB::unprepared("
                    ALTER TABLE forcing 
                    CHANGE status status ENUM(
                        'pendente', 
                        'liberado', 
                        'forcado', 
                        'solicitacao_retirada', 
                        'retirado'
                    ) NOT NULL DEFAULT 'pendente'
                ");
            } catch (\Exception $fallbackError) {
                \Log::error('Erro ao atualizar ENUM status: ' . $fallbackError->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o ENUM original
        try {
            DB::statement("
                ALTER TABLE forcing 
                MODIFY COLUMN status ENUM('pendente', 'liberado', 'forcado') 
                NOT NULL DEFAULT 'pendente'
            ");
        } catch (\Exception $e) {
            \Log::warning('Erro ao reverter ENUM status: ' . $e->getMessage());
        }
    }
};