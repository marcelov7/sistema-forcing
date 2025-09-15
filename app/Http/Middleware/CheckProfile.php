<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $profile
     */
    public function handle(Request $request, Closure $next, string $profile): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verifica se o usuário tem o perfil necessário
        $hasAccess = $this->checkUserAccess($user, $profile);
        
        if (!$hasAccess) {
            $message = $this->getAccessDeniedMessage($profile);
            
            // Log da tentativa de acesso negado
            Log::warning('Acesso negado ao middleware', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_profile' => $user->perfil,
                'required_profile' => $profile,
                'url' => $request->url(),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);
            
            abort(403, $message);
        }

        return $next($request);
    }

    /**
     * Verifica se o usuário tem acesso baseado no perfil requerido
     */
    private function checkUserAccess($user, string $profile): bool
    {
        // Admin tem acesso a tudo
        if ($user->isAdmin()) {
            return true;
        }

        switch ($profile) {
            case 'admin':
                return false; // Apenas admin real
                
            case 'liberador':
                return $user->isLiberador();
                
            case 'executante':
                return $user->isExecutante();
                
            case 'user':
                return $user->isUser() || $user->isLiberador() || $user->isExecutante();
                
            default:
                return false;
        }
    }

    /**
     * Retorna mensagem personalizada de acesso negado
     */
    private function getAccessDeniedMessage(string $profile): string
    {
        $messages = [
            'admin' => 'Acesso negado. Apenas administradores podem acessar esta página.',
            'liberador' => 'Acesso negado. Apenas liberadores e administradores podem acessar esta página.',
            'executante' => 'Acesso negado. Apenas executantes e administradores podem acessar esta página.',
            'user' => 'Acesso negado. Você não tem permissão para acessar esta página.',
        ];

        return $messages[$profile] ?? 'Acesso negado.';
    }
}
