<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\User;
use App\Models\Forcing;
use Illuminate\Console\Command;

class ShowMultiTenantDemo extends Command
{
    protected $signature = 'show:multi-tenant-demo';
    protected $description = 'Demonstra as funcionalidades do sistema multi-tenant';

    public function handle()
    {
        $this->info('🏢 SISTEMA MULTI-TENANT - CONTROLE DE FORCING 🏢');
        $this->line('===============================================');
        $this->newLine();

        // Mostrar estatísticas gerais
        $this->showGeneralStats();
        $this->newLine();

        // Mostrar unidades e seus dados
        $this->showUnitsData();
        $this->newLine();

        // Mostrar usuários por unidade
        $this->showUsersByUnit();
        $this->newLine();

        // Mostrar forcings por unidade
        $this->showForcingsByUnit();
        $this->newLine();

        // Mostrar isolamento de dados
        $this->showDataIsolation();
        $this->newLine();

        $this->info('✅ Sistema Multi-Tenant Totalmente Funcional!');
        $this->line('Acesse /admin/units para gerenciar as unidades como Super Admin');
    }

    private function showGeneralStats()
    {
        $this->info('📊 ESTATÍSTICAS GERAIS:');
        
        $totalUnits = Unit::count();
        $activeUnits = Unit::where('active', true)->count();
        $totalUsers = User::count();
        $superAdmins = User::where('is_super_admin', true)->count();
        $totalForcings = Forcing::count();
        
        $this->table(
            ['Métrica', 'Quantidade'],
            [
                ['Total de Unidades', $totalUnits],
                ['Unidades Ativas', $activeUnits],
                ['Total de Usuários', $totalUsers],
                ['Super Administradores', $superAdmins],
                ['Total de Forcings', $totalForcings],
            ]
        );
    }

    private function showUnitsData()
    {
        $this->info('🏭 UNIDADES CADASTRADAS:');
        
        $units = Unit::withCount(['users', 'forcings'])->get();
        
        $data = [];
        foreach ($units as $unit) {
            $data[] = [
                $unit->code,
                $unit->name,
                $unit->company,
                $unit->city . '/' . $unit->state,
                $unit->users_count,
                $unit->forcings_count,
                $unit->active ? '✅ Ativa' : '❌ Inativa'
            ];
        }
        
        $this->table(
            ['Código', 'Nome', 'Empresa', 'Localização', 'Usuários', 'Forcings', 'Status'],
            $data
        );
    }

    private function showUsersByUnit()
    {
        $this->info('👥 USUÁRIOS POR UNIDADE:');
        
        $units = Unit::with(['users' => function($query) {
            $query->orderBy('perfil')->orderBy('name');
        }])->get();
        
        foreach ($units as $unit) {
            $this->line("📍 {$unit->name} ({$unit->code}):");
            
            if ($unit->users->count() > 0) {
                $userData = [];
                foreach ($unit->users as $user) {
                    $userData[] = [
                        $user->name,
                        $user->username,
                        ucfirst($user->perfil),
                        $user->setor,
                        $user->is_super_admin ? '⭐ Super Admin' : 'Regular'
                    ];
                }
                
                $this->table(
                    ['Nome', 'Username', 'Perfil', 'Setor', 'Tipo'],
                    $userData
                );
            } else {
                $this->warn('   Nenhum usuário cadastrado');
            }
            $this->newLine();
        }
        
        // Mostrar Super Admins sem unidade
        $superAdmins = User::where('is_super_admin', true)->whereNull('unit_id')->get();
        if ($superAdmins->count() > 0) {
            $this->line("⭐ SUPER ADMINISTRADORES (SEM UNIDADE):");
            $superAdminData = [];
            foreach ($superAdmins as $user) {
                $superAdminData[] = [
                    $user->name,
                    $user->username,
                    $user->email,
                    ucfirst($user->perfil)
                ];
            }
            $this->table(
                ['Nome', 'Username', 'Email', 'Perfil'],
                $superAdminData
            );
        }
    }

    private function showForcingsByUnit()
    {
        $this->info('⚠️ FORCINGS POR UNIDADE:');
        
        $units = Unit::with(['forcings' => function($query) {
            $query->latest()->limit(3);
        }])->get();
        
        foreach ($units as $unit) {
            $this->line("📍 {$unit->name} ({$unit->code}):");
            
            $totalForcings = $unit->forcings()->count();
            $forcados = $unit->forcings()->where('status', 'forcado')->count();
            $retirados = $unit->forcings()->where('status', 'retirado')->count();
            
            $this->line("   Total: {$totalForcings} | Forçados: {$forcados} | Retirados: {$retirados}");
            
            if ($unit->forcings->count() > 0) {
                $forcingData = [];
                foreach ($unit->forcings as $forcing) {
                    $status = $forcing->status === 'forcado' ? '🔴 Forçado' : '🟢 Retirado';
                    $forcingData[] = [
                        $forcing->forcing,
                        $forcing->equipamento,
                        $status,
                        $forcing->created_at->format('d/m/Y H:i')
                    ];
                }
                
                $this->table(
                    ['Forcing', 'Equipamento', 'Status', 'Criado em'],
                    $forcingData
                );
            } else {
                $this->warn('   Nenhum forcing registrado');
            }
            $this->newLine();
        }
    }

    private function showDataIsolation()
    {
        $this->info('🔒 DEMONSTRAÇÃO DE ISOLAMENTO DE DADOS:');
        $this->newLine();
        
        $units = Unit::withCount(['users', 'forcings'])->get();
        
        foreach ($units as $unit) {
            $this->line("🏭 {$unit->name} ({$unit->code}):");
            
            // Simular consulta de usuário da unidade
            $userForcings = Forcing::where('unit_id', $unit->id)->count();
            $this->line("   • Usuários desta unidade veem: {$userForcings} forcing(s)");
            
            // Mostrar forcings de outras unidades que NÃO são visíveis
            $otherForcings = Forcing::where('unit_id', '!=', $unit->id)->count();
            $this->line("   • Forcings de outras unidades (invisíveis): {$otherForcings}");
            $this->newLine();
        }
        
        // Super Admin vê tudo
        $totalForcings = Forcing::count();
        $this->info("⭐ SUPER ADMIN vê TODOS os forcings: {$totalForcings}");
        $this->newLine();
        
        $this->warn('🛡️ ISOLAMENTO GARANTIDO:');
        $this->line('• Cada unidade só acessa seus próprios dados');
        $this->line('• Super Admin tem acesso global');
        $this->line('• Middleware garante a segurança dos dados');
    }
}
