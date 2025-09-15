<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class FixDataLiberacao extends Command
{
    protected $signature = 'forcing:fix-data-liberacao';
    protected $description = 'Adiciona coluna data_liberacao e migra dados existentes';

    public function handle()
    {
        try {
            $this->info('Iniciando correção da coluna data_liberacao...');
            
            // Verificar se a coluna já existe usando Schema Builder
            $hasDataLiberacao = false;
            try {
                $hasDataLiberacao = Schema::hasColumn('forcing', 'data_liberacao');
            } catch (Exception $e) {
                // Se der erro, verificar com PRAGMA
                $columns = DB::select("PRAGMA table_info(forcing)");
                $hasDataLiberacao = collect($columns)->contains('name', 'data_liberacao');
            }
            
            if (!$hasDataLiberacao) {
                $this->info('Adicionando coluna data_liberacao...');
                try {
                    // Tentar usar Schema Builder primeiro
                    Schema::table('forcing', function ($table) {
                        $table->timestamp('data_liberacao')->nullable();
                    });
                    $this->info('✅ Coluna data_liberacao adicionada via Schema Builder!');
                } catch (Exception $e) {
                    // Se falhar, usar SQL direto
                    $this->warn('Schema Builder falhou, tentando SQL direto...');
                    DB::statement('ALTER TABLE forcing ADD COLUMN data_liberacao DATETIME NULL');
                    $this->info('✅ Coluna data_liberacao adicionada via SQL direto!');
                }
            } else {
                $this->info('✅ Coluna data_liberacao já existe!');
            }
            
            // Migrar dados existentes
            $this->info('Migrando dados existentes...');
            $affected = DB::update("
                UPDATE forcing 
                SET data_liberacao = updated_at 
                WHERE status = 'liberado' 
                AND liberador_id IS NOT NULL 
                AND data_liberacao IS NULL
            ");
            
            $this->info("✅ Migração concluída! {$affected} registros atualizados.");
            
            // Verificar se funcionou
            $this->info('Verificando se a coluna foi criada...');
            $hasDataLiberacaoFinal = Schema::hasColumn('forcing', 'data_liberacao');
            if ($hasDataLiberacaoFinal) {
                $this->info('✅ Verificação confirmada: coluna data_liberacao existe!');
            } else {
                $this->error('❌ Erro: coluna data_liberacao ainda não existe!');
                return 1;
            }
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
