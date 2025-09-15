<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Unit;
use App\Models\Forcing;

class SystemStatus extends Command
{
    protected $signature = 'system:status';
    protected $description = 'Verifica status completo do sistema';

    public function handle()
    {
        $this->info('📊 STATUS DO SISTEMA DE FORCING');
        $this->line('===============================');
        $this->newLine();

        // 1. Informações do Ambiente
        $this->info('🌍 AMBIENTE:');
        $this->line('   Laravel: ' . app()->version());
        $this->line('   Ambiente: ' . app()->environment());
        $this->line('   Debug: ' . (config('app.debug') ? 'Ativo' : 'Inativo'));
        $this->line('   URL: ' . config('app.url'));
        $this->newLine();

        // 2. Banco de Dados
        $this->info('🗄️ BANCO DE DADOS:');
        try {
            $pdo = DB::connection()->getPdo();
            $this->line('   ✅ Conexão: OK');
            $this->line('   Driver: ' . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
            $this->line('   Versão: ' . $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION));
        } catch (\Exception $e) {
            $this->line('   ❌ Erro: ' . $e->getMessage());
        }
        $this->newLine();

        // 3. Tabelas e Dados
        $this->info('📋 ESTATÍSTICAS:');
        try {
            $totalUsers = User::count();
            $superAdmins = User::where('is_super_admin', true)->count();
            $totalUnits = Unit::count();
            $activeUnits = Unit::where('active', true)->count();
            $totalForcings = Forcing::count();
            $forcingsForcados = Forcing::where('status', 'Forçado')->count();
            $forcingsRetirados = Forcing::where('status', 'Retirado')->count();

            $this->line("   👥 Usuários: $totalUsers (Super Admins: $superAdmins)");
            $this->line("   🏢 Unidades: $totalUnits (Ativas: $activeUnits)");
            $this->line("   ⚡ Forcings: $totalForcings (Forçados: $forcingsForcados, Retirados: $forcingsRetirados)");
            
        } catch (\Exception $e) {
            $this->line('   ❌ Erro ao buscar dados: ' . $e->getMessage());
        }
        $this->newLine();

        // 4. Super Admin
        $this->info('👑 SUPER ADMIN:');
        try {
            $superAdmin = User::where('is_super_admin', true)->first();
            if ($superAdmin) {
                $this->line('   ✅ Encontrado: ' . $superAdmin->name);
                $this->line('   📧 Email: ' . $superAdmin->email);
                $this->line('   👤 Username: ' . $superAdmin->username);
            } else {
                $this->line('   ❌ Super Admin não encontrado!');
                $this->warn('   Execute: php artisan create:super-admin');
            }
        } catch (\Exception $e) {
            $this->line('   ❌ Erro: ' . $e->getMessage());
        }
        $this->newLine();

        // 5. Unidades por Perfil
        $this->info('🏢 UNIDADES POR PERFIL:');
        try {
            $profileStats = DB::table('users')
                ->join('units', 'users.unit_id', '=', 'units.id')
                ->select('units.name', 'users.profile', DB::raw('count(*) as total'))
                ->where('users.is_super_admin', false)
                ->groupBy('units.name', 'users.profile')
                ->orderBy('units.name')
                ->get();

            $currentUnit = null;
            foreach ($profileStats as $stat) {
                if ($currentUnit !== $stat->name) {
                    $this->line("   📍 {$stat->name}:");
                    $currentUnit = $stat->name;
                }
                $this->line("      • {$stat->profile}: {$stat->total}");
            }
        } catch (\Exception $e) {
            $this->line('   ❌ Erro: ' . $e->getMessage());
        }
        $this->newLine();

        // 6. Últimos Forcings
        $this->info('⚡ ÚLTIMOS FORCINGS:');
        try {
            $latestForcings = Forcing::with(['unit', 'user'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            foreach ($latestForcings as $forcing) {
                $status = $forcing->getStatusTexto();
                $unit = $forcing->unit ? $forcing->unit->name : 'N/A';
                $user = $forcing->user ? $forcing->user->name : 'N/A';
                $date = $forcing->created_at->format('d/m/Y H:i');
                
                $this->line("   • [{$status}] {$forcing->equipamento} ({$unit}) - {$user} em {$date}");
            }
        } catch (\Exception $e) {
            $this->line('   ❌ Erro: ' . $e->getMessage());
        }
        $this->newLine();

        // 7. Cache e Performance
        $this->info('🚀 PERFORMANCE:');
        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        $viewsCached = is_dir(storage_path('framework/views')) && count(glob(storage_path('framework/views/*.php'))) > 0;

        $this->line('   Config Cache: ' . ($configCached ? '✅ Ativo' : '❌ Inativo'));
        $this->line('   Routes Cache: ' . ($routesCached ? '✅ Ativo' : '❌ Inativo'));
        $this->line('   Views Cache: ' . ($viewsCached ? '✅ Ativo' : '❌ Inativo'));
        $this->newLine();

        // 8. Permissões
        $this->info('📁 PERMISSÕES:');
        $storageWritable = is_writable(storage_path());
        $bootstrapWritable = is_writable(base_path('bootstrap/cache'));
        
        $this->line('   Storage: ' . ($storageWritable ? '✅ Gravável' : '❌ Sem permissão'));
        $this->line('   Bootstrap Cache: ' . ($bootstrapWritable ? '✅ Gravável' : '❌ Sem permissão'));
        $this->newLine();

        // 9. URLs Importantes
        $this->info('🔗 ACESSO AO SISTEMA:');
        $baseUrl = rtrim(config('app.url'), '/');
        $this->line("   🏠 Home: {$baseUrl}");
        $this->line("   🔐 Login: {$baseUrl}/login");
        $this->line("   📊 Dashboard: {$baseUrl}/dashboard");
        $this->line("   👑 Admin: {$baseUrl}/admin/units");
        $this->newLine();

        // 10. Servidor Web
        $this->info('🌐 SERVIDOR WEB:');
        $this->line('   IP: 31.97.168.137');
        $this->line('   🔗 Acesso Direto: http://31.97.168.137/');
        $this->line('   🔗 Domínio: http://forcing.devaxis.com.br/');
        $this->line('   👤 Login: superadmin / admin123');
        $this->newLine();

        $this->info('✅ Verificação concluída!');
        
        return 0;
    }
}
