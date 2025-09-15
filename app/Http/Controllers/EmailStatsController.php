<?php

namespace App\Http\Controllers;

use App\Services\EmailCounterService;
use Illuminate\Http\Request;

class EmailStatsController extends Controller
{
    /**
     * Exibe estatísticas de emails
     */
    public function index()
    {
        $stats = EmailCounterService::obterEstatisticas();
        
        return view('admin.email-stats', compact('stats'));
    }
    
    /**
     * Retorna estatísticas via API
     */
    public function api()
    {
        return response()->json(EmailCounterService::obterEstatisticas());
    }
    
    /**
     * Reseta contadores (apenas para admins)
     */
    public function reset()
    {
        EmailCounterService::resetar();
        
        return redirect()->back()->with('success', 'Contadores de email resetados com sucesso!');
    }
}
