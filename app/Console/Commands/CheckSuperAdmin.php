<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckSuperAdmin extends Command
{
    protected $signature = 'check:super-admin';
    protected $description = 'Verifica configuração do Super Admin';

    public function handle()
    {
        $superAdmin = User::where('username', 'superadmin')->first();
        
        if (!$superAdmin) {
            $this->error('❌ Super Admin não encontrado!');
            return 1;
        }
        
        $this->info("🔍 VERIFICAÇÃO DO SUPER ADMIN:");
        $this->line("📧 Nome: {$superAdmin->name}");
        $this->line("👤 Username: {$superAdmin->username}");
        $this->line("✉️ Email: {$superAdmin->email}");
        $this->line("🏢 Unidade: " . ($superAdmin->unit_id ? "ID {$superAdmin->unit_id}" : "Sem unidade (correto para Super Admin)"));
        $this->line("⭐ Super Admin Flag: " . ($superAdmin->is_super_admin ? '✅ TRUE' : '❌ FALSE'));
        
        if (!$superAdmin->is_super_admin) {
            $this->error('🔧 CORRIGINDO...');
            $superAdmin->update(['is_super_admin' => true]);
            $this->info('✅ Super Admin flag configurada!');
        }
        
        return 0;
    }
}
