<?php

namespace App\Services;

use App\Models\Forcing;
use App\Models\User;
use App\Mail\ForcingCriado;
use App\Mail\ForcingLiberado;
use App\Mail\ForcingExecutado;
use App\Mail\SolicitacaoRetirada;
use App\Mail\ForcingRetirado;
use App\Mail\ConfirmacaoRetiradaForcing;
use App\Services\EmailCounterService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ForcingNotificationService
{
    /**
     * Envia notificação quando um forcing é criado
     * Para: Todos os liberadores
     */
    public function notificarForcingCriado(Forcing $forcing)
    {
        try {
            // Buscar todos os liberadores
            $liberadores = User::where('perfil', 'liberador')
                ->orWhere('perfil', 'admin')
                ->get();

            foreach ($liberadores as $liberador) {
                Mail::to($liberador->email)->send(new ForcingCriado($forcing));
            }

            Log::info("Notificação de forcing criado enviada", [
                'forcing_id' => $forcing->id,
                'destinatarios' => $liberadores->pluck('email')->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de forcing criado", [
                'forcing_id' => $forcing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envia notificação quando um forcing é criado para um liberador específico
     * Para: Liberador específico selecionado pelo solicitante
     */
    public function notificarForcingCriadoParaLiberador(Forcing $forcing, User $liberador)
    {
        try {
            Mail::to($liberador->email)->send(new ForcingCriado($forcing));

            // Contar email enviado
            EmailCounterService::incrementar('criacao', 1, $forcing->id);

            Log::info("Notificação de forcing criado enviada para liberador específico", [
                'forcing_id' => $forcing->id,
                'liberador_id' => $liberador->id,
                'liberador_email' => $liberador->email,
                'liberador_nome' => $liberador->name
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de forcing criado para liberador específico", [
                'forcing_id' => $forcing->id,
                'liberador_id' => $liberador->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envia notificação quando um forcing é liberado
     * Para: Todos os executantes
     */
    public function notificarForcingLiberado(Forcing $forcing)
    {
        try {
            // Buscar todos os executantes
            $executantes = User::where('perfil', 'executante')
                ->orWhere('perfil', 'admin')
                ->get();

            foreach ($executantes as $executante) {
                Mail::to($executante->email)->send(new ForcingLiberado($forcing));
            }

            // Contar emails enviados
            EmailCounterService::incrementar('liberacao', $executantes->count(), $forcing->id);

            Log::info("Notificação de forcing liberado enviada", [
                'forcing_id' => $forcing->id,
                'destinatarios' => $executantes->pluck('email')->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de forcing liberado", [
                'forcing_id' => $forcing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envia notificação quando um forcing é executado
     * Para: Criador, liberadores e executante que executou
     */
    public function notificarForcingExecutado(Forcing $forcing)
    {
        try {
            $destinatarios = collect();

            // Adicionar criador
            $destinatarios->push($forcing->user);

            // Adicionar liberador se existir
            if ($forcing->liberador) {
                $destinatarios->push($forcing->liberador);
            }

            // Adicionar executante que executou
            if ($forcing->executante) {
                $destinatarios->push($forcing->executante);
            }

            // Adicionar todos os liberadores
            $liberadores = User::where('perfil', 'liberador')
                ->orWhere('perfil', 'admin')
                ->get();
            
            $destinatarios = $destinatarios->concat($liberadores);

            // Remover duplicatas
            $destinatarios = $destinatarios->unique('id');

            foreach ($destinatarios as $destinatario) {
                Mail::to($destinatario->email)->send(new ForcingExecutado($forcing));
            }

            // Contar emails enviados
            EmailCounterService::incrementar('execucao', $destinatarios->count(), $forcing->id);

            Log::info("Notificação de forcing executado enviada", [
                'forcing_id' => $forcing->id,
                'destinatarios' => $destinatarios->pluck('email')->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de forcing executado", [
                'forcing_id' => $forcing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envia notificação quando uma solicitação de retirada é feita
     * Para: Solicitante (criador) + Liberador responsável + Todos os executantes + Admins
     */
    public function notificarSolicitacaoRetirada(Forcing $forcing)
    {
        try {
            $destinatarios = collect();

            // Adicionar o criador do forcing (solicitante original)
            $destinatarios->push($forcing->user);

            // Adicionar o liberador responsável específico
            if ($forcing->liberador) {
                $destinatarios->push($forcing->liberador);
            }

            // Adicionar todos os executantes
            $executantes = User::where('perfil', 'executante')->get();
            $destinatarios = $destinatarios->concat($executantes);

            // Adicionar todos os administradores
            $admins = User::where('perfil', 'admin')->get();
            $destinatarios = $destinatarios->concat($admins);

            // Remover duplicatas (caso criador ou liberador sejam admin/executante)
            $destinatarios = $destinatarios->unique('id');

            foreach ($destinatarios as $destinatario) {
                Mail::to($destinatario->email)->send(new SolicitacaoRetirada($forcing));
            }

            // Contar emails enviados
            EmailCounterService::incrementar('solicitacao', $destinatarios->count(), $forcing->id);

            Log::info("Notificação de solicitação de retirada enviada", [
                'forcing_id' => $forcing->id,
                'solicitante_id' => $forcing->user_id,
                'liberador_id' => $forcing->liberador_id,
                'destinatarios' => $destinatarios->pluck('email')->toArray(),
                'total_destinatarios' => $destinatarios->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de solicitação de retirada", [
                'forcing_id' => $forcing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envia notificação quando um forcing é retirado
     * Para: Todos os envolvidos no processo
     */
    public function notificarForcingRetirado(Forcing $forcing)
    {
        try {
            $destinatarios = collect();

            // Adicionar criador
            $destinatarios->push($forcing->user);

            // Adicionar liberador se existir
            if ($forcing->liberador) {
                $destinatarios->push($forcing->liberador);
            }

            // Adicionar executante que executou
            if ($forcing->executante) {
                $destinatarios->push($forcing->executante);
            }

            // Adicionar quem solicitou a retirada
            if ($forcing->solicitadoRetiradaPor) {
                $destinatarios->push($forcing->solicitadoRetiradaPor);
            }

            // Adicionar quem retirou
            if ($forcing->retiradoPor) {
                $destinatarios->push($forcing->retiradoPor);
            }

            // Adicionar todos os liberadores e executantes
            $usuarios = User::whereIn('perfil', ['liberador', 'executante', 'admin'])->get();
            $destinatarios = $destinatarios->concat($usuarios);

            // Remover duplicatas
            $destinatarios = $destinatarios->unique('id');

            foreach ($destinatarios as $destinatario) {
                Mail::to($destinatario->email)->send(new ForcingRetirado($forcing));
            }

            Log::info("Notificação de forcing retirado enviada", [
                'forcing_id' => $forcing->id,
                'destinatarios' => $destinatarios->pluck('email')->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de forcing retirado", [
                'forcing_id' => $forcing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envia notificação específica de confirmação de retirada
     * Para: Apenas o solicitante (criador) e administradores
     */
    public function notificarConfirmacaoRetirada(Forcing $forcing)
    {
        try {
            $destinatarios = collect();

            // Adicionar o criador do forcing (solicitante original)
            $destinatarios->push($forcing->user);

            // Adicionar apenas administradores
            $admins = User::where('perfil', 'admin')->get();
            $destinatarios = $destinatarios->concat($admins);

            // Remover duplicatas (caso o criador seja admin)
            $destinatarios = $destinatarios->unique('id');

            foreach ($destinatarios as $destinatario) {
                Mail::to($destinatario->email)->send(new ConfirmacaoRetiradaForcing($forcing));
            }

            // Contar emails enviados
            EmailCounterService::incrementar('confirmacao', $destinatarios->count(), $forcing->id);

            Log::info("Confirmação de retirada enviada para solicitante e admins", [
                'forcing_id' => $forcing->id,
                'solicitante_id' => $forcing->user_id,
                'solicitante_email' => $forcing->user->email,
                'destinatarios' => $destinatarios->pluck('email')->toArray(),
                'total_destinatarios' => $destinatarios->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar confirmação de retirada", [
                'forcing_id' => $forcing->id,
                'solicitante_id' => $forcing->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
