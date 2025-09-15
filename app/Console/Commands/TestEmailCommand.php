<?php

namespace App\Console\Commands;

use App\Models\Forcing;
use App\Services\ForcingNotificationService;
use Illuminate\Console\Command;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {type=criado} {--forcing-id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de emails do sistema de forcing';

    protected $notificationService;

    public function __construct(ForcingNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $forcingId = $this->option('forcing-id');

        $forcing = Forcing::with(['user', 'liberador', 'executante', 'retiradoPor', 'solicitadoRetiradaPor'])
            ->find($forcingId);

        if (!$forcing) {
            $this->error("Forcing #{$forcingId} não encontrado!");
            return 1;
        }

        $this->info("Testando email tipo: {$type}");
        $this->info("Forcing: #{$forcing->id} - {$forcing->tag}");

        try {
            switch ($type) {
                case 'criado':
                    $this->notificationService->notificarForcingCriado($forcing);
                    $this->info('✅ Email de forcing criado enviado!');
                    break;

                case 'liberado':
                    $this->notificationService->notificarForcingLiberado($forcing);
                    $this->info('✅ Email de forcing liberado enviado!');
                    break;

                case 'executado':
                    $this->notificationService->notificarForcingExecutado($forcing);
                    $this->info('✅ Email de forcing executado enviado!');
                    break;

                case 'solicitacao':
                    $this->notificationService->notificarSolicitacaoRetirada($forcing);
                    $this->info('✅ Email de solicitação de retirada enviado!');
                    break;

                case 'retirado':
                    $this->notificationService->notificarForcingRetirado($forcing);
                    $this->info('✅ Email de forcing retirado enviado!');
                    break;

                default:
                    $this->error("Tipo de email inválido: {$type}");
                    $this->info("Tipos disponíveis: criado, liberado, executado, solicitacao, retirado");
                    return 1;
            }

            $this->info('📧 Verifique o arquivo de log em storage/logs/laravel.log');
            
        } catch (\Exception $e) {
            $this->error("Erro ao enviar email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
