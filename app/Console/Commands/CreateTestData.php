<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\User;
use App\Models\Forcing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestData extends Command
{
    protected $signature = 'create:test-data {count=50}';
    protected $description = 'Cria dados de teste para verificar paginação';

    public function handle()
    {
        $count = (int) $this->argument('count');
        
        $this->info("🧪 CRIANDO {$count} REGISTROS DE TESTE PARA PAGINAÇÃO");
        $this->line('================================================');
        $this->newLine();

        // Obter unidade para testes
        $unit = Unit::first();
        if (!$unit) {
            $this->error('❌ Nenhuma unidade encontrada! Execute os seeders primeiro.');
            return 1;
        }

        $this->info("📍 Usando unidade: {$unit->name} ({$unit->code})");
        $this->newLine();

        // Criar usuários de teste
        $userCount = min(20, $count / 3); // Máximo 20 usuários
        $this->info("👥 Criando {$userCount} usuários de teste...");
        
        $createdUsers = [];
        for ($i = 1; $i <= $userCount; $i++) {
            $user = User::create([
                'name' => "Usuário Teste {$i}",
                'username' => "teste{$i}_" . time(),
                'email' => "teste{$i}_" . time() . "@teste.com",
                'password' => Hash::make('123456789'),
                'empresa' => $unit->company,
                'setor' => 'Teste',
                'perfil' => ['user', 'liberador', 'executante'][rand(0, 2)],
                'unit_id' => $unit->id,
            ]);
            $createdUsers[] = $user;
            
            if ($i % 5 == 0) {
                $this->line("   ✅ {$i} usuários criados");
            }
        }

        // Criar forcings de teste
        $forcingCount = $count;
        $this->info("⚠️ Criando {$forcingCount} forcings de teste...");
        
        $areas = ['Produção', 'Manutenção', 'Utilidades', 'Laboratório', 'Almoxarifado'];
        $situacoes = ['operacao', 'manutencao', 'teste'];
        $status = ['forcado', 'retirado'];
        
        for ($i = 1; $i <= $forcingCount; $i++) {
            $randomUser = $createdUsers[array_rand($createdUsers)];
            
            Forcing::create([
                'forcing' => "FORCING-TEST-{$i}",
                'equipamento' => "Equipamento Teste {$i}",
                'situacao_equipamento' => $situacoes[array_rand($situacoes)],
                'local_execucao' => ['campo', 'sala_controle', 'ambos'][rand(0, 2)],
                'descricao' => "Descrição detalhada do forcing de teste número {$i} para verificar funcionalidade de paginação.",
                'motivo' => "Motivo do forcing {$i} - teste de paginação do sistema.",
                'medidas_seguranca' => "Medidas de segurança padrão para forcing {$i}.",
                'status' => $status[array_rand($status)],
                'user_id' => $randomUser->id,
                'unit_id' => $unit->id,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
            
            if ($i % 10 == 0) {
                $this->line("   ✅ {$i} forcings criados");
            }
        }

        $this->newLine();
        $this->info('✅ DADOS DE TESTE CRIADOS COM SUCESSO!');
        $this->newLine();
        
        $this->info('📊 RESUMO:');
        $this->line("   • {$userCount} usuários de teste criados");
        $this->line("   • {$forcingCount} forcings de teste criados");
        $this->line("   • Todos vinculados à unidade: {$unit->name}");
        
        $this->newLine();
        $this->info('🔍 VERIFICAR PAGINAÇÃO:');
        $this->line('   • Lista de Forcings: 15 itens por página');
        $this->line('   • Lista de Usuários: 20 itens por página');
        $this->line('   • Lista de Unidades: 10 itens por página');
        
        $this->newLine();
        $this->info('🧹 LIMPEZA:');
        $this->line('   • Para remover dados de teste: php artisan clear:test-data');
        
        return 0;
    }
}
