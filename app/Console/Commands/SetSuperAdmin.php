<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SetSuperAdmin extends Command
{
    protected $signature = 'users:set-super-admin {username}';
    protected $description = 'Define um usuário como Super Admin';

    public function handle()
    {
        $username = $this->argument('username');
        
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            $this->error("Usuário '{$username}' não encontrado!");
            return 1;
        }
        
        $user->update(['is_super_admin' => true]);
        
        $this->info("Usuário '{$user->name}' ({$username}) definido como Super Admin!");
        
        return 0;
    }
}
