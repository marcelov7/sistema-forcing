<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsersCommand extends Command
{
    protected $signature = 'users:list';
    protected $description = 'Lista todos os usuários do sistema';

    public function handle()
    {
        $this->info('👥 USUÁRIOS DO SISTEMA');
        $this->info('====================');
        
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->warn('❌ Nenhum usuário encontrado');
            return;
        }

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                $user->id,
                $user->name,
                $user->username,
                $user->email,
                strtoupper($user->perfil),
                $user->empresa,
                $user->setor
            ];
        }

        $this->table(
            ['ID', 'Nome', 'Username', 'E-mail', 'Perfil', 'Empresa', 'Setor'],
            $data
        );

        $this->newLine();
        $this->info('📋 CREDENCIAIS DE TESTE:');
        $this->info('• admin / admin123 (Administrador)');
        $this->info('• liberador / liberador123 (Liberador)');
        $this->info('• executante / executante123 (Executante)');
        $this->info('• usuario / usuario123 (Usuário)');
        $this->newLine();
        $this->info('🌐 Acesse: http://localhost:8000/login');

        return 0;
    }
}
