<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmailCounterService
{
    private const CACHE_KEY = 'email_counter';
    
    /**
     * Incrementa o contador de emails enviados
     */
    public static function incrementar($tipo, $quantidade = 1, $forcing_id = null)
    {
        $hoje = now()->format('Y-m-d');
        $mes = now()->format('Y-m');
        
        // Contadores do dia
        Cache::increment("emails_dia_{$hoje}", $quantidade);
        
        // Contadores do mês
        Cache::increment("emails_mes_{$mes}", $quantidade);
        
        // Contadores por tipo
        Cache::increment("emails_tipo_{$tipo}_{$hoje}", $quantidade);
        
        // Log detalhado
        Log::info("Email enviado - Contador atualizado", [
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'forcing_id' => $forcing_id,
            'data' => $hoje,
            'total_dia' => Cache::get("emails_dia_{$hoje}", 0),
            'total_mes' => Cache::get("emails_mes_{$mes}", 0)
        ]);
    }
    
    /**
     * Obtém estatísticas de emails
     */
    public static function obterEstatisticas()
    {
        $hoje = now()->format('Y-m-d');
        $mes = now()->format('Y-m');
        
        return [
            'hoje' => Cache::get("emails_dia_{$hoje}", 0),
            'mes' => Cache::get("emails_mes_{$mes}", 0),
            'por_tipo' => [
                'criacao' => Cache::get("emails_tipo_criacao_{$hoje}", 0),
                'liberacao' => Cache::get("emails_tipo_liberacao_{$hoje}", 0),
                'execucao' => Cache::get("emails_tipo_execucao_{$hoje}", 0),
                'solicitacao' => Cache::get("emails_tipo_solicitacao_{$hoje}", 0),
                'confirmacao' => Cache::get("emails_tipo_confirmacao_{$hoje}", 0),
            ]
        ];
    }
    
    /**
     * Reseta contadores (para testes)
     */
    public static function resetar()
    {
        $hoje = now()->format('Y-m-d');
        $mes = now()->format('Y-m');
        
        Cache::forget("emails_dia_{$hoje}");
        Cache::forget("emails_mes_{$mes}");
        
        $tipos = ['criacao', 'liberacao', 'execucao', 'solicitacao', 'confirmacao'];
        foreach ($tipos as $tipo) {
            Cache::forget("emails_tipo_{$tipo}_{$hoje}");
        }
    }
}
