<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;

class ShowSystemHierarchy extends Command
{
    protected $signature = 'show:hierarchy';
    protected $description = 'Mostra a hierarquia do sistema multi-tenant';

    public function handle()
    {
        $this->info('🏢 HIERARQUIA DO SISTEMA MULTI-TENANT');
        $this->line('=====================================');
        $this->newLine();

        // Super Admin
        $this->info('👑 SUPER ADMINISTRADOR (ÚNICO NO SISTEMA)');
        $superAdmin = User::where('is_super_admin', true)->first();
        if ($superAdmin) {
            $this->line("   • {$superAdmin->name} ({$superAdmin->username})");
            $this->line("   • Gerencia TODAS as unidades");
            $this->line("   • Vê forcings de TODAS as unidades");
            $this->line("   • Único usuário sem unidade específica");
        } else {
            $this->error('   ❌ Nenhum Super Admin encontrado!');
        }
        
        $this->newLine();
        $this->line('├── 🔽 HIERARQUIA POR UNIDADE:');
        $this->newLine();

        // Unidades e seus usuários
        $units = Unit::with(['users' => function($query) {
            $query->orderBy('perfil')->orderBy('name');
        }])->get();

        foreach ($units as $index => $unit) {
            $isLast = $index === $units->count() - 1;
            $prefix = $isLast ? '└──' : '├──';
            
            $this->line("{$prefix} 🏭 {$unit->name} ({$unit->code})");
            
            // Agrupar usuários por perfil
            $usersByProfile = $unit->users->groupBy('perfil');
            
            // Ordem hierárquica
            $profileOrder = ['admin', 'liberador', 'executante', 'user'];
            $profileIcons = [
                'admin' => '👨‍💼',
                'liberador' => '✅',
                'executante' => '🔧',
                'user' => '👤'
            ];
            $profileNames = [
                'admin' => 'Administradores',
                'liberador' => 'Liberadores',
                'executante' => 'Executantes', 
                'user' => 'Usuários'
            ];

            foreach ($profileOrder as $profile) {
                if (isset($usersByProfile[$profile])) {
                    $users = $usersByProfile[$profile];
                    $subPrefix = $isLast ? '    ' : '│   ';
                    $this->line("{$subPrefix}├── {$profileIcons[$profile]} {$profileNames[$profile]} ({$users->count()}):");
                    
                    foreach ($users as $userIndex => $user) {
                        $isLastUser = $userIndex === $users->count() - 1;
                        $userPrefix = $isLastUser ? '└──' : '├──';
                        $this->line("{$subPrefix}│   {$userPrefix} {$user->name} ({$user->username})");
                    }
                }
            }
            
            if (!$isLast) {
                $this->line('│');
            }
        }

        $this->newLine();
        $this->info('🔐 REGRAS DE ACESSO:');
        $this->line('• Super Admin: Vê TODOS os forcings de TODAS as unidades');
        $this->line('• Admin da Unidade: Vê apenas forcings da SUA unidade');
        $this->line('• Liberador: Vê apenas forcings da SUA unidade');
        $this->line('• Executante: Vê apenas forcings da SUA unidade');
        $this->line('• Usuário: Vê apenas forcings da SUA unidade');

        $this->newLine();
        $this->info('⚠️ RESTRIÇÕES:');
        $this->line('• Apenas UM Super Admin existe no sistema');
        $this->line('• Super Admin NÃO pode ser criado pelo formulário');
        $this->line('• Admins de unidade só gerenciam SUA unidade');
        $this->line('• Todos os usuários (exceto Super Admin) pertencem a UMA unidade');

        $this->newLine();
        $this->info('🎯 CRIAÇÃO DE USUÁRIOS:');
        $this->line('• Super Admin: Pode criar usuários para QUALQUER unidade');
        $this->line('• Admin da Unidade: Pode criar usuários para SUA unidade');
        $this->line('• Perfis disponíveis: user, liberador, executante, admin');
        $this->line('• Super Admin NÃO é opção no formulário');

        return 0;
    }
}
