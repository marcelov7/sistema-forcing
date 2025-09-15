<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DeployProduction extends Command
{
    protected $signature = 'deploy:production {--skip-seed : Pular seeders}';
    protected $description = 'Executa deploy completo para produção';

    public function handle()
    {
        $this->info('🚀 INICIANDO DEPLOY PARA PRODUÇÃO');
        $this->line('===================================');
        $this->newLine();

        // 1. Verificar ambiente
        if (!app()->environment('production')) {
            $this->warn('⚠️ Ambiente não é produção (APP_ENV=' . app()->environment() . ')');
            if (!$this->confirm('Continuar mesmo assim?')) {
                return 1;
            }
        }

        // 2. Verificar conexão com banco
        $this->info('🗄️ VERIFICANDO BANCO DE DADOS...');
        try {
            DB::connection()->getPdo();
            $this->line('   ✅ Conexão com banco estabelecida');
        } catch (\Exception $e) {
            $this->error('   ❌ Erro de conexão: ' . $e->getMessage());
            return 1;
        }

        // 3. Verificar se há migrações pendentes
        $this->info('📋 VERIFICANDO MIGRAÇÕES...');
        try {
            $pendingMigrations = Artisan::call('migrate:status');
            $this->line('   ✅ Status das migrações verificado');
        } catch (\Exception $e) {
            $this->warn('   ⚠️ Problema ao verificar migrações: ' . $e->getMessage());
        }

        // 4. Executar migrações
        $this->info('🔄 EXECUTANDO MIGRAÇÕES...');
        if ($this->confirm('Executar migrações? (CUIDADO: isto pode alterar o banco de dados)')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                $this->line('   ✅ Migrações executadas com sucesso');
            } catch (\Exception $e) {
                $this->error('   ❌ Erro nas migrações: ' . $e->getMessage());
                return 1;
            }
        }

        // 5. Executar seeders (opcional)
        if (!$this->option('skip-seed')) {
            $this->info('🌱 EXECUTANDO SEEDERS...');
            if ($this->confirm('Executar seeders? (Criar dados iniciais)')) {
                try {
                    Artisan::call('db:seed', ['--force' => true]);
                    $this->line('   ✅ Seeders executados com sucesso');
                } catch (\Exception $e) {
                    $this->error('   ❌ Erro nos seeders: ' . $e->getMessage());
                    $this->warn('   ⚠️ Continue com o deploy, seeders são opcionais');
                }
            }
        }

        // 6. Limpar e otimizar caches
        $this->info('🚀 OTIMIZANDO PARA PRODUÇÃO...');
        
        // Limpar caches
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->line('   ✅ Caches limpos');

        // Criar caches de produção
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $this->line('   ✅ Caches de produção criados');

        // 7. Verificar permissões
        $this->info('📁 VERIFICANDO PERMISSÕES...');
        $storagePath = storage_path();
        $bootstrapPath = base_path('bootstrap/cache');
        
        if (is_writable($storagePath) && is_writable($bootstrapPath)) {
            $this->line('   ✅ Permissões OK');
        } else {
            $this->warn('   ⚠️ Verifique permissões das pastas:');
            $this->line("      - $storagePath");
            $this->line("      - $bootstrapPath");
            $this->line('   Execute: chmod -R 775 storage/ bootstrap/cache/');
        }

        // 8. Verificar Super Admin
        $this->info('👑 VERIFICANDO SUPER ADMIN...');
        try {
            Artisan::call('check:super-admin');
            $this->line('   ✅ Super Admin verificado');
        } catch (\Exception $e) {
            $this->warn('   ⚠️ Problema com Super Admin: ' . $e->getMessage());
        }

        // 9. Resumo final
        $this->newLine();
        $this->info('✅ DEPLOY CONCLUÍDO COM SUCESSO!');
        $this->newLine();
        
        $this->info('🎯 PRÓXIMOS PASSOS:');
        $this->line('1. Acesse: ' . config('app.url') . '/login');
        $this->line('2. Login: superadmin / 123456789');
        $this->line('3. Teste criação de unidades e usuários');
        $this->line('4. Verifique logs em: storage/logs/laravel.log');
        
        $this->newLine();
        $this->info('📊 COMANDOS ÚTEIS:');
        $this->line('• php artisan show:hierarchy');
        $this->line('• php artisan show:multi-tenant-demo');
        $this->line('• php artisan queue:work (se usar filas)');

        return 0;
    }
}
