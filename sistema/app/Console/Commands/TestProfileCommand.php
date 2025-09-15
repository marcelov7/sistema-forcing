<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Forcing;

class TestProfileCommand extends Command
{
    protected $signature = 'test:profile {username}';
    protected $description = 'Testa a funcionalidade do perfil de um usuário';

    public function handle()
    {
        $username = $this->argument('username');
        
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            $this->error("❌ Usuário '{$username}' não encontrado");
            return 1;
        }

        $this->info("🧪 TESTANDO PERFIL DO USUÁRIO: {$user->name}");
        $this->info('============================================');
        
        $this->newLine();
        $this->info('📊 DADOS DO USUÁRIO:');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID', $user->id],
                ['Nome', $user->name],
                ['Username', $user->username],
                ['E-mail', $user->email],
                ['Empresa', $user->empresa],
                ['Setor', $user->setor],
                ['Perfil', strtoupper($user->perfil)],
                ['Criado em', $user->created_at->format('d/m/Y H:i')],
                ['Atualizado em', $user->updated_at->format('d/m/Y H:i')],
            ]
        );

        $this->newLine();
        $this->info('📈 ESTATÍSTICAS:');
        
        try {
            $forcingsCriados = $user->forcings()->count();
            $this->info("✅ Forcing criados: {$forcingsCriados}");
        } catch (\Exception $e) {
            $this->error("❌ Erro ao contar forcing criados: " . $e->getMessage());
        }

        try {
            $forcingsLiberados = $user->forcingsLiberados()->count();
            $this->info("✅ Forcing liberados: {$forcingsLiberados}");
        } catch (\Exception $e) {
            $this->error("❌ Erro ao contar forcing liberados: " . $e->getMessage());
        }

        try {
            $forcingsExecutados = $user->forcingsExecutados()->count();
            $this->info("✅ Forcing executados: {$forcingsExecutados}");
        } catch (\Exception $e) {
            $this->error("❌ Erro ao contar forcing executados: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('🔑 PERMISSÕES:');
        $this->info("Admin: " . ($user->isAdmin() ? '✅' : '❌'));
        $this->info("Liberador: " . ($user->isLiberador() ? '✅' : '❌'));
        $this->info("Executante: " . ($user->isExecutante() ? '✅' : '❌'));
        $this->info("User: " . ($user->isUser() ? '✅' : '❌'));

        $this->newLine();
        $this->info('🎯 TESTE CONCLUÍDO!');
        
        return 0;
    }
}
