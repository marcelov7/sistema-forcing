<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    /**
     * Página inicial com detecção automática
     */
    public function index()
    {
        $user = Auth::user();
        $deviceInfo = request()->get('device_info', []);
        
        // Se usuário não estiver logado, redirecionar para login
        if (!$user) {
            return redirect()->route('login');
        }

        // Se for mobile e não estiver na API, sugerir o app
        if ($deviceInfo['is_mobile'] && !request()->is('api/*')) {
            return view('mobile-suggestion', compact('user', 'deviceInfo'));
        }

        // Para desktop, redirecionar para lista de forcings
        return redirect()->route('forcing.index');
    }

    /**
     * Dashboard principal - redireciona para lista de forcings
     */
    public function dashboard()
    {
        // Sempre redirecionar para a lista de forcings
        return redirect()->route('forcing.index');
    }

    /**
     * Página de sugestão para mobile
     */
    public function mobileSuggestion()
    {
        $user = Auth::user();
        $deviceInfo = request()->get('device_info', []);
        
        return view('mobile-suggestion', compact('user', 'deviceInfo'));
    }

    /**
     * API para obter informações do dispositivo
     */
    public function deviceInfo()
    {
        $deviceInfo = request()->get('device_info', []);
        
        return response()->json([
            'success' => true,
            'data' => $deviceInfo
        ]);
    }
}
