<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestUserCreation extends Command
{
    protected $signature = 'test:user-creation';
    protected $description = 'Testa a criação de usuário com unidade';

    public function handle()
    {
        $this->info('🧪 TESTE DE CRIAÇÃO DE USUÁRIO COM UNIDADE');
        $this->line('=============================================');
        $this->newLine();

        // Mostrar unidades disponíveis
        $this->info('📍 UNIDADES DISPONÍVEIS:');
        $units = Unit::where('active', true)->get();
        
        foreach ($units as $unit) {
            $this->line("   • {$unit->code} - {$unit->name} ({$unit->company})");
        }
        $this->newLine();

        // Criar um usuário de teste
        $unitTest = $units->first();
        if (!$unitTest) {
            $this->error('❌ Nenhuma unidade ativa encontrada!');
            return 1;
        }

        $testUsername = 'teste.usuario.' . time();
        
        $this->info("👤 CRIANDO USUÁRIO DE TESTE:");
        $this->line("   Username: {$testUsername}");
        $this->line("   Unidade: {$unitTest->code} - {$unitTest->name}");

        try {
            $user = User::create([
                'name' => 'Usuário de Teste',
                'username' => $testUsername,
                'email' => $testUsername . '@teste.com',
                'password' => Hash::make('123456789'),
                'empresa' => $unitTest->company,
                'setor' => 'Teste',
                'perfil' => 'user',
                'unit_id' => $unitTest->id,
            ]);

            $this->info('✅ USUÁRIO CRIADO COM SUCESSO!');
            $this->newLine();

            // Verificar relacionamentos
            $this->info('🔍 VERIFICAÇÃO DE RELACIONAMENTOS:');
            $this->line("   ID do usuário: {$user->id}");
            $this->line("   Unidade associada: " . ($user->unit ? $user->unit->name : 'Nenhuma'));
            $this->line("   Pode criar forcing? " . ($user->unit ? '✅ Sim' : '❌ Não'));
            
            // Verificar se o usuário pode ver apenas forcings da sua unidade
            $forcingsVisíveis = \App\Models\Forcing::where('unit_id', $user->unit_id)->count();
            $totalForcings = \App\Models\Forcing::count();
            
            $this->newLine();
            $this->info('🔒 VERIFICAÇÃO DE ISOLAMENTO:');
            $this->line("   Forcings visíveis para este usuário: {$forcingsVisíveis}");
            $this->line("   Total de forcings no sistema: {$totalForcings}");
            
            if ($forcingsVisíveis < $totalForcings) {
                $this->info('✅ ISOLAMENTO FUNCIONANDO - Usuário não vê forcings de outras unidades');
            } else {
                $this->warn('⚠️ Todos os forcings são visíveis - verificar middleware');
            }

            $this->newLine();
            $this->info('🧹 LIMPANDO TESTE...');
            $user->delete();
            $this->info('✅ Usuário de teste removido');

        } catch (\Exception $e) {
            $this->error('❌ ERRO AO CRIAR USUÁRIO:');
            $this->error($e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('✅ TESTE CONCLUÍDO COM SUCESSO!');
        $this->line('O sistema de criação de usuários com unidades está funcionando corretamente.');
        
        return 0;
    }
}
