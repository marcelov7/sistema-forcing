<?php

namespace App\Console\Commands;

use App\Services\HostingerOptimizedNotificationService;
use Illuminate\Console\Command;

class EmailStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostra o status do sistema de emails Hostinger';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new HostingerOptimizedNotificationService();
        $status = $service->getEmailStatus();

        $this->info('📧 STATUS DO SISTEMA DE EMAILS HOSTINGER');
        $this->info('==========================================');
        
        $this->line('📊 <info>Uso Diário:</info>');
        $this->line("   Enviados hoje: <fg=yellow>{$status['daily_used']}</fg=yellow>");
        $this->line("   Limite seguro: <fg=cyan>{$status['daily_limit']}</fg=cyan>");
        $this->line("   Limite Hostinger: <fg=blue>{$status['hostinger_limit']}</fg=blue>");
        $this->line("   Disponíveis: <fg=green>{$status['daily_available']}</fg=green>");
        
        $this->line('');
        $this->line('📈 <info>Utilização:</info>');
        $this->line("   Percentual usado: <fg=yellow>{$status['percentage_used']}%</fg=yellow>");
        $this->line("   Buffer de segurança: <fg=cyan>{$status['buffer_emails']} emails</fg=cyan>");
        
        $this->line('');
        $statusColor = $status['can_send_urgent'] ? 'green' : 'red';
        $statusText = $status['can_send_urgent'] ? 'OPERACIONAL' : 'LIMITE ATINGIDO';
        $this->line("🚦 <info>Status:</info> <fg={$statusColor}>{$statusText}</fg={$statusColor}>");
        
        if ($status['percentage_used'] > 80) {
            $this->warn('⚠️  ATENÇÃO: Mais de 80% do limite diário usado!');
        }
        
        if (!$status['can_send_urgent']) {
            $this->error('🚨 CRÍTICO: Limite diário atingido! Emails urgentes podem falhar.');
        }
        
        $this->line('');
        $this->line('💡 <info>Dicas de Otimização:</info>');
        $this->line('   - Emails urgentes (solicitação retirada) têm prioridade');
        $this->line('   - Sistema usa 85% do limite como buffer de segurança');
        $this->line('   - Emails agrupados para economizar envios');
        $this->line('   - Monitoramento automático de limites');
        
        return 0;
    }
}
