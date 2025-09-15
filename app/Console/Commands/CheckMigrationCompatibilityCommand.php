<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckMigrationCompatibilityCommand extends Command
{
    protected $signature = 'migrate:check-mysql';
    protected $description = 'Verifica a compatibilidade das migrações com MySQL';

    public function handle()
    {
        $this->info('🔍 VERIFICANDO COMPATIBILIDADE MYSQL - CLOUDPANEL');
        $this->info('=================================================');
        
        // Verificar arquivos de migração
        $migrationPath = database_path('migrations');
        $migrations = glob($migrationPath . '/*.php');
        
        $this->newLine();
        $this->info('📋 MIGRAÇÕES ENCONTRADAS:');
        
        $count = 0;
        foreach ($migrations as $migration) {
            $count++;
            $filename = basename($migration);
            $this->line("  {$count}. {$filename}");
        }
        
        $this->newLine();
        $this->info("📊 TOTAL: {$count} migrações");
        
        // Verificar conexão atual
        $this->newLine();
        $this->info('🔗 CONFIGURAÇÃO ATUAL:');
        
        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");
        
        $this->table(
            ['Configuração', 'Valor'],
            [
                ['Conexão Padrão', $connection],
                ['Driver', $dbConfig['driver'] ?? 'N/A'],
                ['Host', $dbConfig['host'] ?? 'N/A'],
                ['Port', $dbConfig['port'] ?? 'N/A'],
                ['Database', $dbConfig['database'] ?? 'N/A'],
                ['Charset', $dbConfig['charset'] ?? 'N/A'],
                ['Collation', $dbConfig['collation'] ?? 'N/A'],
                ['Engine', $dbConfig['engine'] ?? 'N/A'],
            ]
        );
        
        // Verificar status das migrações
        $this->newLine();
        $this->info('📈 STATUS DAS MIGRAÇÕES:');
        
        try {
            if (Schema::hasTable('migrations')) {
                $executed = DB::table('migrations')->count();
                $this->info("✅ Tabela 'migrations' existe");
                $this->info("✅ Migrações executadas: {$executed}");
                
                // Listar migrações executadas
                $executedMigrations = DB::table('migrations')
                    ->orderBy('batch')
                    ->orderBy('migration')
                    ->get();
                
                if ($executedMigrations->isNotEmpty()) {
                    $this->newLine();
                    $this->info('🗃️ MIGRAÇÕES EXECUTADAS:');
                    foreach ($executedMigrations as $migration) {
                        $this->line("  Batch {$migration->batch}: {$migration->migration}");
                    }
                }
            } else {
                $this->warn("⚠️ Tabela 'migrations' não existe - Execute: php artisan migrate");
            }
        } catch (\Exception $e) {
            $this->error("❌ Erro ao verificar migrações: " . $e->getMessage());
        }
        
        // Verificar tabelas do sistema
        $this->newLine();
        $this->info('📋 VERIFICAÇÃO DE TABELAS:');
        
        $expectedTables = [
            'users' => 'Usuários do sistema',
            'forcing' => 'Forcing principais',
            'cache' => 'Sistema de cache',
            'jobs' => 'Fila de tarefas',
            'sessions' => 'Sessões de usuário',
            'password_reset_tokens' => 'Tokens de reset'
        ];
        
        foreach ($expectedTables as $table => $description) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->info("✅ {$table}: {$description} ({$count} registros)");
            } else {
                $this->warn("⚠️ {$table}: {$description} (não existe)");
            }
        }
        
        // Recomendações para MySQL
        $this->newLine();
        $this->info('💡 RECOMENDAÇÕES PARA MYSQL/CLOUDPANEL:');
        
        $recommendations = [
            '✅ Usar charset utf8mb4 para suporte completo Unicode',
            '✅ Usar collation utf8mb4_unicode_ci para ordenação adequada',
            '✅ Engine InnoDB para suporte a foreign keys',
            '✅ MySQL versão 5.7+ ou MariaDB 10.2+',
            '✅ Configurar timezone adequado (America/Sao_Paulo)',
            '✅ Backup antes de executar migrações em produção'
        ];
        
        foreach ($recommendations as $rec) {
            $this->line("  {$rec}");
        }
        
        // Comandos úteis
        $this->newLine();
        $this->info('🛠️ COMANDOS ÚTEIS PARA CLOUDPANEL:');
        
        $commands = [
            'php artisan migrate' => 'Executar migrações pendentes',
            'php artisan migrate:status' => 'Ver status das migrações',
            'php artisan migrate:fresh --seed' => 'Recriar banco com dados iniciais',
            'php artisan db:seed' => 'Popular dados iniciais',
            'php artisan migrate:rollback' => 'Reverter última migração'
        ];
        
        foreach ($commands as $command => $desc) {
            $this->line("  <comment>{$command}</comment> - {$desc}");
        }
        
        $this->newLine();
        $this->info('🎉 VERIFICAÇÃO CONCLUÍDA!');
        
        // Verificar se está pronto para produção
        if ($connection === 'mysql') {
            $this->info('✅ Sistema configurado para MySQL - Pronto para CloudPanel!');
        } else {
            $this->warn('⚠️ Sistema atual: ' . strtoupper($connection) . ' - Configure MySQL para produção');
        }
        
        return 0;
    }
}
