<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            Log::warning('Tentativa de acesso a área super admin sem autenticação', [
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->header('User-Agent')
            ]);
            
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        $user = Auth::user();

        // Verifica se o usuário é super admin
        if (!$user->isSuperAdmin()) {
            Log::warning('Tentativa de acesso a área super admin sem permissão', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_profile' => $user->perfil,
                'is_super_admin' => $user->is_super_admin,
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->header('User-Agent')
            ]);

            abort(403, 'Acesso negado. Apenas Super Administradores podem acessar esta área.');
        }

        // Log de acesso bem-sucedido para auditoria
        Log::info('Acesso de Super Admin autorizado', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'url' => $request->url(),
            'ip' => $request->ip()
        ]);

        return $next($request);
    }

    /**
     * Verifica se um usuário tem permissão de super admin
     */
    public static function isUserSuperAdmin($user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        return $user && $user->isSuperAdmin();
    }

    /**
     * Verifica se o usuário atual pode acessar funcionalidades de super admin
     */
    public static function canAccess(): bool
    {
        return Auth::check() && static::isUserSuperAdmin();
    }
}
