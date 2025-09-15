<?php

namespace App\Console\Commands;

use App\Models\TermsAcceptance;
use App\Models\User;
use Illuminate\Console\Command;

class CheckTermsAcceptances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'terms:check {--user= : ID do usuário específico} {--recent : Mostrar apenas os últimos 30 dias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar aceites dos termos de responsabilidade';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📋 RELATÓRIO DE ACEITES DOS TERMOS DE RESPONSABILIDADE');
        $this->info('='.str_repeat('=', 60));

        $query = TermsAcceptance::with('user');

        // Filtro por usuário específico
        if ($this->option('user')) {
            $query->where('user_id', $this->option('user'));
            $user = User::find($this->option('user'));
            if ($user) {
                $this->info("📍 Filtrado para usuário: {$user->name} ({$user->email})");
            } else {
                $this->error('❌ Usuário não encontrado!');
                return 1;
            }
        }

        // Filtro por período recente
        if ($this->option('recent')) {
            $query->where('accepted_at', '>=', now()->subDays(30));
            $this->info('⏰ Mostrando apenas os últimos 30 dias');
        }

        $acceptances = $query->orderBy('accepted_at', 'desc')->get();

        if ($acceptances->isEmpty()) {
            $this->warn('⚠️  Nenhum aceite de termo encontrado com os filtros aplicados.');
            return 0;
        }

        $this->info("\n🔍 Total de aceites encontrados: {$acceptances->count()}");
        $this->info('─'.str_repeat('─', 60));

        // Tabela com os dados
        $headers = ['ID', 'Usuário', 'Email', 'Data/Hora', 'Procedimento', 'IP', 'Navegador'];
        $rows = [];

        foreach ($acceptances as $acceptance) {
            $rows[] = [
                $acceptance->id,
                $acceptance->user->name ?? 'N/A',
                $acceptance->user->email ?? 'N/A',
                $acceptance->accepted_at->format('d/m/Y H:i:s'),
                $acceptance->procedure_version ?? 'N/A',
                $acceptance->ip_address ?? 'N/A',
                substr($acceptance->user_agent ?? 'N/A', 0, 30) . '...'
            ];
        }

        $this->table($headers, $rows);

        // Estatísticas
        $this->info("\n📊 ESTATÍSTICAS:");
        $this->info('─'.str_repeat('─', 30));

        $userStats = $acceptances->groupBy('user_id');
        $this->info("👥 Usuários únicos: " . $userStats->count());

        $today = $acceptances->where('accepted_at', '>=', now()->startOfDay())->count();
        $this->info("📅 Aceites hoje: {$today}");

        $thisWeek = $acceptances->where('accepted_at', '>=', now()->startOfWeek())->count();
        $this->info("📅 Aceites esta semana: {$thisWeek}");

        $thisMonth = $acceptances->where('accepted_at', '>=', now()->startOfMonth())->count();
        $this->info("📅 Aceites este mês: {$thisMonth}");

        // Usuários com mais aceites
        if ($userStats->count() > 1 && !$this->option('user')) {
            $this->info("\n🏆 TOP USUÁRIOS (mais aceites):");
            $topUsers = $userStats->map(function ($group) {
                return [
                    'user' => $group->first()->user,
                    'count' => $group->count(),
                    'last_acceptance' => $group->max('accepted_at')
                ];
            })->sortByDesc('count')->take(5);

            foreach ($topUsers as $data) {
                $lastAcceptance = \Carbon\Carbon::parse($data['last_acceptance'])->format('d/m/Y H:i');
                $this->line("   • {$data['user']->name}: {$data['count']} aceites (último: {$lastAcceptance})");
            }
        }

        $this->info("\n✅ Relatório concluído!");
        return 0;
    }
}
