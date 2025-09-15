<?php

namespace App\Policies;

use App\Models\Forcing;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ForcingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Todos os usuários autenticados podem ver a lista de forcings
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Forcing $forcing): bool
    {
        // Admins podem ver qualquer forcing
        if ($user->perfil === 'admin') {
            return true;
        }

        // Usuários podem ver forcings da sua unidade
        return $user->unit_id === $forcing->unit_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Todos os usuários autenticados podem criar forcings
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Forcing $forcing): bool
    {
        // Admins podem editar qualquer forcing, independentemente do status
        if ($user->perfil === 'admin') {
            return true;
        }

        // Usuários só podem editar forcings da sua unidade
        if ($user->unit_id !== $forcing->unit_id) {
            return false;
        }

        // Usuários normais só podem editar se o forcing ainda pode ser editado (pendente ou liberado)
        return $forcing->podeSerEditado();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Forcing $forcing): bool
    {
        // Apenas admins podem excluir forcings
        if ($user->perfil === 'admin') {
            return true;
        }

        // Usuários podem excluir apenas forcings pendentes da sua unidade
        return $user->unit_id === $forcing->unit_id && $forcing->status === 'pendente';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Forcing $forcing): bool
    {
        // Apenas admins podem restaurar forcings
        return $user->perfil === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Forcing $forcing): bool
    {
        // Apenas admins podem excluir permanentemente
        return $user->perfil === 'admin';
    }

    /**
     * Determine whether the user can liberate the forcing.
     */
    public function liberar(User $user, Forcing $forcing): bool
    {
        // Apenas liberadores e admins podem liberar
        if ($user->perfil === 'admin') {
            return true;
        }

        // Usuários com perfil 'liberador' podem liberar forcings da sua unidade
        return $user->perfil === 'liberador' && 
               $user->unit_id === $forcing->unit_id && 
               $forcing->podeSerLiberado();
    }

    /**
     * Determine whether the user can execute the forcing.
     */
    public function executar(User $user, Forcing $forcing): bool
    {
        // Admins e executores podem executar
        if ($user->perfil === 'admin') {
            return true;
        }

        // Usuários podem executar forcings liberados da sua unidade
        return $user->unit_id === $forcing->unit_id && $forcing->podeSerExecutado();
    }

    /**
     * Determine whether the user can request withdrawal of the forcing.
     */
    public function solicitarRetirada(User $user, Forcing $forcing): bool
    {
        // Admins podem solicitar retirada de qualquer forcing
        if ($user->perfil === 'admin') {
            return true;
        }

        // Usuários podem solicitar retirada de forcings executados da sua unidade
        return $user->unit_id === $forcing->unit_id && $forcing->podeSolicitarRetirada();
    }

    /**
     * Determine whether the user can withdraw the forcing.
     */
    public function retirar(User $user, Forcing $forcing): bool
    {
        // Admins podem retirar qualquer forcing
        if ($user->perfil === 'admin') {
            return true;
        }

        // Liberadores e executantes podem retirar forcings da sua unidade
        return ($user->perfil === 'liberador' || $user->perfil === 'executante') && 
               $user->unit_id === $forcing->unit_id && 
               $forcing->podeSerRetirado();
    }
}
