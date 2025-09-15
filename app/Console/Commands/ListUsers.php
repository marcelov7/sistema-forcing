<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    protected $signature = 'users:list';
    protected $description = 'Lista todos os usuários do sistema';

    public function handle()
    {
        $users = User::all();
        
        $this->info('=== USUÁRIOS DO SISTEMA ===');
        
        foreach ($users as $user) {
            $superAdmin = $user->is_super_admin ? '[SUPER ADMIN]' : '';
            $unit = $user->unit ? $user->unit->name : 'Sem unidade';
            
            $this->line(sprintf(
                '- %s (%s) - %s - Unidade: %s %s',
                $user->name,
                $user->email,
                strtoupper($user->perfil),
                $unit,
                $superAdmin
            ));
        }
        
        $this->info("\nTotal: " . $users->count() . " usuários");
    }
}
