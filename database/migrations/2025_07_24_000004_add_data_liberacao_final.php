<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Sistema de Controle de Forcing - Laravel 12.x
     * Finaliza estrutura com campos essenciais para controle completo
     * Perfis: user, liberador, admin
     * Status: pendente → liberado → forcado → solicitacao_retirada → retirado
     */
    public function up(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $columns = Schema::getColumnListing('forcing');
            
            // Verificar se $columns é realmente um array
            if (!is_array($columns)) {
                Log::warning('Schema::getColumnListing retornou tipo inválido', ['type' => gettype($columns)]);
                return;
            }
            
            // Campo para observações durante execução do forcing
            if (!in_array('observacoes_execucao', $columns)) {
                $table->text('observacoes_execucao')
                      ->nullable()
                      ->comment('Observações durante a execução do forcing');
            }
            
            // Status de execução (pendente/executado)
            if (!in_array('status_execucao', $columns)) {
                $table->enum('status_execucao', ['pendente', 'executado'])
                      ->default('pendente')
                      ->comment('Controle de execução: pendente ou executado');
            }
            
            // Descrição da resolução para solicitação de retirada
            if (!in_array('descricao_resolucao', $columns)) {
                $table->text('descricao_resolucao')
                      ->nullable()
                      ->comment('Descrição da resolução para retirada do forcing');
            }
        });

        // Corrigir dados históricos do sistema
        $this->corrigirDadosHistoricos();
        
        // Atualizar ENUM status para fluxo completo (MySQL)
        if (DB::getDriverName() === 'mysql') {
            $this->atualizarStatusEnum();
        }
        
        Log::info('Sistema de Controle de Forcing: Estrutura finalizada com sucesso');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $columns = Schema::getColumnListing('forcing');
            
            // Verificar se é array antes de usar in_array
            if (!is_array($columns)) {
                Log::warning('Erro ao obter lista de colunas para rollback');
                return;
            }
            
            $camposRemover = ['descricao_resolucao', 'status_execucao', 'observacoes_execucao'];
            
            foreach ($camposRemover as $campo) {
                if (in_array($campo, $columns)) {
                    $table->dropColumn($campo);
                }
            }
        });
        
        // Reverter ENUM status para valores originais (MySQL)
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("
                    ALTER TABLE forcing 
                    MODIFY COLUMN status ENUM('pendente', 'liberado', 'forcado') 
                    NOT NULL DEFAULT 'pendente'
                ");
                Log::info('Sistema de Controle de Forcing: ENUM status revertido');
            } catch (\Exception $e) {
                Log::warning('Erro ao reverter ENUM status: ' . $e->getMessage());
            }
        }
    }

    /**
     * Corrige dados históricos do Sistema de Controle de Forcing
     */
    private function corrigirDadosHistoricos(): void
    {
        try {
            // Atualizar status_execucao para forcings já executados
            $registrosAtualizados = DB::table('forcing')
                ->whereIn('status', ['forcado', 'solicitacao_retirada', 'retirado'])
                ->where('status_execucao', 'pendente')
                ->update(['status_execucao' => 'executado']);
            
            // Corrigir data_liberacao para forcings liberados sem data
            $liberacoesCorrigidas = DB::table('forcing')
                ->where('status', 'liberado')
                ->whereNull('data_liberacao')
                ->update(['data_liberacao' => DB::raw('updated_at')]);
            
            Log::info('Sistema de Controle de Forcing: Dados históricos corrigidos', [
                'registros_atualizados' => $registrosAtualizados,
                'liberacoes_corrigidas' => $liberacoesCorrigidas
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Erro ao corrigir dados históricos: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza ENUM status para fluxo completo do Sistema de Controle de Forcing:
     * pendente → liberado → forcado → solicitacao_retirada → retirado
     */
    private function atualizarStatusEnum(): void
    {
        try {
            // Verificar valores atuais do ENUM status
            $result = DB::select("SHOW COLUMNS FROM forcing WHERE Field = 'status'");
            
            if (!empty($result) && isset($result[0]->Type)) {
                $enumValues = $result[0]->Type;
                
                // Verificar se 'solicitacao_retirada' já está presente
                if (is_string($enumValues) && strpos($enumValues, 'solicitacao_retirada') === false) {
                    DB::statement("
                        ALTER TABLE forcing 
                        MODIFY COLUMN status ENUM(
                            'pendente', 
                            'liberado', 
                            'forcado', 
                            'solicitacao_retirada', 
                            'retirado'
                        ) NOT NULL DEFAULT 'pendente'
                        COMMENT 'Fluxo: pendente→liberado→forcado→solicitacao_retirada→retirado'
                    ");
                    
                    Log::info('Sistema de Controle de Forcing: ENUM status atualizado para fluxo completo');
                } else {
                    Log::info('Sistema de Controle de Forcing: ENUM status já contém todos os valores necessários');
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar ENUM status: ' . $e->getMessage());
            
            // Fallback: tentar comando alternativo
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
                    COMMENT 'Sistema de Controle de Forcing - Fluxo completo'
                ");
                Log::info('Sistema de Controle de Forcing: ENUM status atualizado via fallback');
            } catch (\Exception $fallbackError) {
                Log::error('Falha no fallback do ENUM status: ' . $fallbackError->getMessage());
            }
        }
    }
};

