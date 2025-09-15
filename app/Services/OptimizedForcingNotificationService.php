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

class OptimizedForcingNotificationService
{
    protected $dailyLimit;
    protected $hourlyLimit;

    public function __construct()
    {
        $this->dailyLimit = config('mail.daily_limit', 200);
        $this->hourlyLimit = config('mail.hourly_limit', 20);
    }

    /**
     * Verifica se ainda pode enviar emails
     */
    protected function canSendEmail(): bool
    {
        $today = now()->format('Y-m-d');
        $hour = now()->format('Y-m-d H');
        
        $dailyCount = Cache::get("email_count_daily_{$today}", 0);
        $hourlyCount = Cache::get("email_count_hourly_{$hour}", 0);
        
        return $dailyCount < $this->dailyLimit && $hourlyCount < $this->hourlyLimit;
    }

    /**
     * Incrementa contador de emails
     */
    protected function incrementEmailCount(): void
    {
        $today = now()->format('Y-m-d');
        $hour = now()->format('Y-m-d H');
        
        Cache::increment("email_count_daily_{$today}", 1, 86400); // 24h
        Cache::increment("email_count_hourly_{$hour}", 1, 3600);  // 1h
    }

    /**
     * Envia email com verificação de limites
     */
    protected function sendEmailSafely($to, $mailable, $context = []): bool
    {
        if (!$this->canSendEmail()) {
            Log::warning('Limite de emails atingido', [
                'to' => $to,
                'context' => $context
            ]);
            return false;
        }

        try {
            Mail::to($to)->send($mailable);
            $this->incrementEmailCount();
            
            Log::info('Email enviado com sucesso', [
                'to' => $to,
                'context' => $context
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email', [
                'to' => $to,
                'error' => $e->getMessage(),
                'context' => $context
            ]);
            return false;
        }
    }

    /**
     * Agrupa destinatários únicos para evitar duplicatas
     */
    protected function getUniqueRecipients($users): \Illuminate\Support\Collection
    {
        return collect($users)->unique('id')->filter(function ($user) {
            return $user && $user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL);
        });
    }

    /**
     * Notificação otimizada para forcing criado
     */
    public function notificarForcingCriado(Forcing $forcing)
    {
        $liberadores = User::whereIn('perfil', ['liberador', 'admin'])
            ->whereNotNull('email')
            ->get();

        $recipients = $this->getUniqueRecipients($liberadores);
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $recipient) {
            if ($this->sendEmailSafely(
                $recipient->email, 
                new ForcingCriado($forcing),
                ['forcing_id' => $forcing->id, 'type' => 'criado']
            )) {
                $sent++;
            } else {
                $failed++;
            }
        }

        Log::info("Notificação forcing criado processada", [
            'forcing_id' => $forcing->id,
            'enviados' => $sent,
            'falharam' => $failed,
            'total_destinatarios' => $recipients->count()
        ]);
    }

    /**
     * Notificação condensada para múltiplas solicitações
     */
    public function notificarSolicitacoesRetiradaCondensada()
    {
        $forcings = Forcing::where('status', 'solicitacao_retirada')
            ->with(['user', 'liberador', 'solicitadoRetiradaPor'])
            ->get();

        if ($forcings->isEmpty()) {
            Log::info('Nenhuma solicitação de retirada pendente encontrada');
            return;
        }

        $executantes = User::whereIn('perfil', ['executante', 'admin'])
            ->whereNotNull('email')
            ->get();

        $recipients = $this->getUniqueRecipients($executantes);
        $sent = 0;
        $failed = 0;

        // Enviar um email consolidado ao invés de um por forcing
        foreach ($recipients as $executante) {
            if ($this->sendEmailSafely(
                $executante->email,
                new \App\Mail\SolicitacoesRetiradaCondensada($forcings),
                ['type' => 'solicitacoes_condensadas', 'count' => $forcings->count()]
            )) {
                $sent++;
            } else {
                $failed++;
            }
        }

        Log::info("Notificação consolidada de solicitações processada", [
            'total_forcings' => $forcings->count(),
            'forcings_ids' => $forcings->pluck('id')->toArray(),
            'enviados' => $sent,
            'falharam' => $failed,
            'total_destinatarios' => $recipients->count()
        ]);

        return [
            'forcings_processados' => $forcings->count(),
            'emails_enviados' => $sent,
            'emails_falharam' => $failed,
            'destinatarios' => $recipients->count()
        ];
    }

    /**
     * Obter estatísticas de uso de email
     */
    public function getEmailStats(): array
    {
        $today = now()->format('Y-m-d');
        $hour = now()->format('Y-m-d H');
        
        $dailyCount = Cache::get("email_count_daily_{$today}", 0);
        $hourlyCount = Cache::get("email_count_hourly_{$hour}", 0);
        
        return [
            'daily_sent' => $dailyCount,
            'daily_limit' => $this->dailyLimit,
            'daily_remaining' => max(0, $this->dailyLimit - $dailyCount),
            'hourly_sent' => $hourlyCount,
            'hourly_limit' => $this->hourlyLimit,
            'hourly_remaining' => max(0, $this->hourlyLimit - $hourlyCount),
            'can_send' => $this->canSendEmail()
        ];
    }

    /**
     * Resetar contadores de email (para testes)
     */
    public function resetEmailCounters(): void
    {
        $today = now()->format('Y-m-d');
        $hour = now()->format('Y-m-d H');
        
        Cache::forget("email_count_daily_{$today}");
        Cache::forget("email_count_hourly_{$hour}");
        
        Log::info('Contadores de email resetados');
    }

    /**
     * Notificação otimizada para forcing liberado
     */
    public function notificarForcingLiberado(Forcing $forcing)
    {
        // Notificar criador e executantes
        $recipients = collect();
        
        // Adicionar criador
        if ($forcing->user && $forcing->user->email) {
            $recipients->push($forcing->user);
        }
        
        // Adicionar executantes
        $executantes = User::whereIn('perfil', ['executante', 'admin'])
            ->whereNotNull('email')
            ->get();
        
        $recipients = $recipients->merge($executantes);
        $recipients = $this->getUniqueRecipients($recipients);
        
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $recipient) {
            if ($this->sendEmailSafely(
                $recipient->email,
                new ForcingLiberado($forcing),
                ['forcing_id' => $forcing->id, 'type' => 'liberado']
            )) {
                $sent++;
            } else {
                $failed++;
            }
        }

        Log::info("Notificação forcing liberado processada", [
            'forcing_id' => $forcing->id,
            'enviados' => $sent,
            'falharam' => $failed,
            'total_destinatarios' => $recipients->count()
        ]);

        return [
            'emails_enviados' => $sent,
            'emails_falharam' => $failed,
            'destinatarios' => $recipients->count()
        ];
    }
}
