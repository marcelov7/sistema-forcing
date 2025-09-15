<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Unit;
use App\Models\Forcing;

class ShowMultiTenantExample extends Command
{
    protected $signature = 'show:multi-tenant';
    protected $description = 'Demonstra como funciona o sistema multi-tenant';

    public function handle()
    {
        $this->info('🏢 DEMONSTRAÇÃO MULTI-TENANT');
        $this->info('==========================');
        
        // Mostrar unidades
        $units = Unit::all();
        $this->info('');
        $this->comment('📍 UNIDADES DISPONÍVEIS:');
        foreach ($units as $unit) {
            $userCount = $unit->users()->count();
            $forcingCount = $unit->forcings()->count();
            $this->line("   • {$unit->code} - {$unit->name} ({$userCount} usuários, {$forcingCount} forcings)");
        }
        
        // Mostrar usuários por unidade
        $this->info('');
        $this->comment('👥 USUÁRIOS POR UNIDADE:');
        foreach ($units as $unit) {
            $this->line("   🏢 {$unit->name}:");
            $users = $unit->users()->get();
            foreach ($users as $user) {
                $this->line("      - {$user->name} ({$user->perfil})");
            }
        }
        
        // Mostrar regras de acesso
        $this->info('');
        $this->warn('🔒 REGRAS DE ISOLAMENTO:');
        $this->line('• Usuário da UND001 SÓ vê forcings da UND001');
        $this->line('• Usuário da UND002 SÓ vê forcings da UND002');
        $this->line('• Usuário da UND003 SÓ vê forcings da UND003');
        $this->line('• SUPER ADMIN vê forcings de TODAS as unidades');
        
        // Super Admin especial
        $superAdmin = User::where('is_super_admin', true)->first();
        $this->info('');
        $this->comment('⭐ SUPER ADMIN (SEM UNIDADE):');
        if ($superAdmin) {
            $this->line("   - {$superAdmin->name} (Vê TODOS os forcings)");
        }
        
        $this->info('');
        $this->info('🔬 TESTE PRÁTICO:');
        $this->line('1. Faça login como "operador.central" (UND001)');
        $this->line('2. Crie um forcing');
        $this->line('3. Faça login como "operador.zonanorte" (UND002)');
        $this->line('4. Verifique que NÃO vê o forcing da UND001');
        $this->line('5. Faça login como "superadmin"');
        $this->line('6. Verifique que vê forcings de TODAS as unidades');
        
        return 0;
    }
}
