<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;

class CreateTestUsers extends Command
{
    protected $signature = 'users:create-test';
    protected $description = 'Cria usuários de teste para todos os perfis';

    public function handle()
    {
        $unit = Unit::first(); // Pega a primeira unidade
        
        // Criar liberador se não existir
        if (!User::where('perfil', 'liberador')->exists()) {
            User::create([
                'name' => 'João Silva Liberador',
                'email' => 'liberador@forcing.com',
                'username' => 'liberador',
                'password' => Hash::make('liberador123'),
                'empresa' => 'Empresa Exemplo',
                'setor' => 'Operações',
                'perfil' => 'liberador',
                'is_super_admin' => false,
                'unit_id' => $unit->id,
                'email_verified_at' => now(),
            ]);
            $this->info('✅ Liberador criado');
        }
        
        // Criar executante se não existir
        if (!User::where('perfil', 'executante')->exists()) {
            User::create([
                'name' => 'Carlos Oliveira Executante',
                'email' => 'executante@forcing.com',
                'username' => 'executante',
                'password' => Hash::make('executante123'),
                'empresa' => 'Empresa Exemplo',
                'setor' => 'Manutenção',
                'perfil' => 'executante',
                'is_super_admin' => false,
                'unit_id' => $unit->id,
                'email_verified_at' => now(),
            ]);
            $this->info('✅ Executante criado');
        }
        
        // Criar usuário comum se não existir
        if (!User::where('perfil', 'user')->where('username', 'usuario')->exists()) {
            User::create([
                'name' => 'Maria Santos',
                'email' => 'usuario@forcing.com',
                'username' => 'usuario',
                'password' => Hash::make('usuario123'),
                'empresa' => 'Empresa Exemplo',
                'setor' => 'Produção',
                'perfil' => 'user',
                'is_super_admin' => false,
                'unit_id' => $unit->id,
                'email_verified_at' => now(),
            ]);
            $this->info('✅ Usuário comum criado');
        }
        
        $this->info("\n📋 CREDENCIAIS DE TESTE:");
        $this->line("• admin / admin123 (Administrador)");
        $this->line("• liberador / liberador123 (Liberador)");
        $this->line("• executante / executante123 (Executante)");
        $this->line("• usuario / usuario123 (Usuário)");
        $this->line("• superadmin / super123 (Super Admin)");
        $this->info("\n🌐 Acesse: http://localhost:8000/login");
        
        return 0;
    }
}
