<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Forcing;
use Illuminate\Console\Command;

class ClearTestData extends Command
{
    protected $signature = 'clear:test-data';
    protected $description = 'Remove dados de teste criados para paginação';

    public function handle()
    {
        $this->info('🧹 REMOVENDO DADOS DE TESTE');
        $this->line('===========================');
        $this->newLine();

        // Remover usuários de teste
        $testUsers = User::where('name', 'LIKE', 'Usuário Teste %')->get();
        $userCount = $testUsers->count();
        
        if ($userCount > 0) {
            $this->info("👥 Removendo {$userCount} usuários de teste...");
            User::where('name', 'LIKE', 'Usuário Teste %')->delete();
            $this->line("   ✅ {$userCount} usuários removidos");
        }

        // Remover forcings de teste
        $testForcings = Forcing::where('forcing', 'LIKE', 'FORCING-TEST-%')->get();
        $forcingCount = $testForcings->count();
        
        if ($forcingCount > 0) {
            $this->info("⚠️ Removendo {$forcingCount} forcings de teste...");
            Forcing::where('forcing', 'LIKE', 'FORCING-TEST-%')->delete();
            $this->line("   ✅ {$forcingCount} forcings removidos");
        }

        $this->newLine();
        if ($userCount > 0 || $forcingCount > 0) {
            $this->info('✅ DADOS DE TESTE REMOVIDOS COM SUCESSO!');
        } else {
            $this->warn('⚠️ Nenhum dado de teste encontrado para remover.');
        }
        
        $this->newLine();
        $this->info('📊 SISTEMA LIMPO:');
        $this->line('   • Dados originais preservados');
        $this->line('   • Apenas dados de teste removidos');
        
        return 0;
    }
}
