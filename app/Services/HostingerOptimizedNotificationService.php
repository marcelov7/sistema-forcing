<?php

namespace App\Services;

use App\Models\Forcing;
use App\Models\User;
use App\Mail\ForcingCriado;
use App\Mail\ForcingLiberado;
use App\Mail\ForcingExecutado;
use App\Mail\SolicitacaoRetirada;
use App\Mail\ForcingRetirado;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class HostingerOptimizedNotificationService
{
    protected $dailyLimit = 85; // Buffer de segurança (85% de 100)
    protected $batchWindow = 30; // minutos para agrupar notificações

    /**
     * Verifica limite diário de emails
     */
    protected function getDailyEmailCount(): int
    {
        $today = now()->format('Y-m-d');
        return Cache::get("hostinger_email_count_{$today}", 0);
    }

    /**
     * Incrementa contador diário
     */
    protected function incrementDailyCount(int $count = 1): void
    {
        $today = now()->format('Y-m-d');
        $ttl = now()->endOfDay()->diffInSeconds();
        Cache::increment("hostinger_email_count_{$today}", $count, $ttl);
    }

    /**
     * Verifica se pode enviar emails
     */
    protected function canSendEmails(int $count = 1): bool
    {
        return ($this->getDailyEmailCount() + $count) <= $this->dailyLimit;
    }

    /**
     * Envia email único otimizado
     */
    protected function sendOptimizedEmail($recipients, $mailable, string $type): bool
    {
        if (!$this->canSendEmails(1)) {
            $this->logLimitReached($type, count($recipients));
            return false;
        }

        try {
            // Hostinger permite até 100 destinatários por email
            $recipientEmails = collect($recipients)
                ->pluck('email')
                ->filter()
                ->unique()
                ->take(100) // Limite Hostinger
                ->toArray();

            if (empty($recipientEmails)) {
                return false;
            }

            // Enviar um único email para todos os destinatários
            Mail::to($recipientEmails[0])
                ->cc(array_slice($recipientEmails, 1))
                ->send($mailable);

            $this->incrementDailyCount(1);

            Log::info("Email Hostinger enviado", [
                'type' => $type,
                'recipients_count' => count($recipientEmails),
                'daily_count' => $this->getDailyEmailCount()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Erro email Hostinger", [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Log quando limite for atingido
     */
    protected function logLimitReached(string $type, int $recipients): void
    {
        Log::warning("Limite diário Hostinger atingido", [
            'type' => $type,
            'recipients' => $recipients,
            'daily_count' => $this->getDailyEmailCount(),
            'limit' => $this->dailyLimit
        ]);

        // Agendar para envio no próximo dia
        $this->scheduleForNextDay($type, $recipients);
    }

    /**
     * Agenda emails para o próximo dia
     */
    protected function scheduleForNextDay(string $type, int $recipients): void
    {
        $tomorrow = now()->addDay()->format('Y-m-d');
        $pending = Cache::get("pending_emails_{$tomorrow}", []);
        
        $pending[] = [
            'type' => $type,
            'recipients' => $recipients,
            'scheduled_at' => now()->toISOString()
        ];

        Cache::put("pending_emails_{$tomorrow}", $pending, now()->addDays(2));
    }

    /**
     * Notificação forcing criado (prioridade média)
     */
    public function notificarForcingCriado(Forcing $forcing): bool
    {
        $liberadores = User::whereIn('perfil', ['liberador', 'admin'])
            ->whereNotNull('email')
            ->get();

        return $this->sendOptimizedEmail(
            $liberadores,
            new ForcingCriado($forcing),
            'forcing_criado'
        );
    }

    /**
     * Notificação forcing liberado (prioridade alta)
     */
    public function notificarForcingLiberado(Forcing $forcing): bool
    {
        $executantes = User::whereIn('perfil', ['executante', 'admin'])
            ->whereNotNull('email')
            ->get();

        return $this->sendOptimizedEmail(
            $executantes,
            new ForcingLiberado($forcing),
            'forcing_liberado'
        );
    }

    /**
     * Notificação forcing executado (prioridade média)
     */
    public function notificarForcingExecutado(Forcing $forcing): bool
    {
        $destinatarios = collect();

        // Criador
        $destinatarios->push($forcing->user);

        // Liberador
        if ($forcing->liberador) {
            $destinatarios->push($forcing->liberador);
        }

        // Todos os liberadores
        $liberadores = User::whereIn('perfil', ['liberador', 'admin'])->get();
        $destinatarios = $destinatarios->concat($liberadores);

        $destinatarios = $destinatarios->unique('id')->filter(function($user) {
            return $user && $user->email;
        });

        return $this->sendOptimizedEmail(
            $destinatarios,
            new ForcingExecutado($forcing),
            'forcing_executado'
        );
    }

    /**
     * Notificação solicitação retirada (prioridade URGENTE)
     */
    public function notificarSolicitacaoRetirada(Forcing $forcing): bool
    {
        // Esta é crítica - sempre tenta enviar
        $executantes = User::whereIn('perfil', ['executante', 'admin'])
            ->whereNotNull('email')
            ->get();

        // Para emails urgentes, use limite total se necessário
        if (!$this->canSendEmails(1) && $this->getDailyEmailCount() < 100) {
            // Usa os últimos emails disponíveis para urgência
            return $this->sendOptimizedEmail(
                $executantes,
                new SolicitacaoRetirada($forcing),
                'solicitacao_retirada_urgente'
            );
        }

        return $this->sendOptimizedEmail(
            $executantes,
            new SolicitacaoRetirada($forcing),
            'solicitacao_retirada'
        );
    }

    /**
     * Notificação forcing retirado (prioridade baixa)
     */
    public function notificarForcingRetirado(Forcing $forcing): bool
    {
        // Só envia se tiver limite disponível
        if (!$this->canSendEmails(1)) {
            $this->logLimitReached('forcing_retirado', 10);
            return false;
        }

        $destinatarios = collect();

        // Principais envolvidos
        $destinatarios->push($forcing->user);
        if ($forcing->liberador) $destinatarios->push($forcing->liberador);
        if ($forcing->executante) $destinatarios->push($forcing->executante);
        if ($forcing->retiradoPor) $destinatarios->push($forcing->retiradoPor);

        $destinatarios = $destinatarios->unique('id')->filter(function($user) {
            return $user && $user->email;
        });

        return $this->sendOptimizedEmail(
            $destinatarios,
            new ForcingRetirado($forcing),
            'forcing_retirado'
        );
    }

    /**
     * Status do sistema de emails
     */
    public function getEmailStatus(): array
    {
        $dailyCount = $this->getDailyEmailCount();
        
        return [
            'daily_used' => $dailyCount,
            'daily_limit' => $this->dailyLimit,
            'daily_available' => $this->dailyLimit - $dailyCount,
            'percentage_used' => round(($dailyCount / $this->dailyLimit) * 100, 1),
            'can_send_urgent' => $this->canSendEmails(1),
            'hostinger_limit' => 100,
            'buffer_emails' => 100 - $this->dailyLimit,
        ];
    }
}
