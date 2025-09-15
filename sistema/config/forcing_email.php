<?php

return [
    // Configurações de email otimizadas para Hostinger
    'hostinger_limits' => [
        'daily_limit' => 100,
        'recipients_per_email' => 100,
        'max_email_size' => 35 * 1024 * 1024, // 35MB
        'attachment_size' => 25 * 1024 * 1024, // 25MB
    ],

    // Estratégias de otimização
    'optimization' => [
        'batch_notifications' => true,
        'smart_grouping' => true,
        'priority_emails' => true,
        'daily_limit_buffer' => 85, // Usar só 85% do limite
    ],

    // Configuração de prioridades
    'email_priorities' => [
        'forcing_criado' => 'medium',      // Pode aguardar algumas horas
        'forcing_liberado' => 'high',      // Executantes precisam saber
        'forcing_executado' => 'medium',   // Informativo
        'solicitacao_retirada' => 'urgent', // Crítico para segurança
        'forcing_retirado' => 'low',       // Apenas informativo
    ],

    // Agrupamento inteligente
    'smart_batching' => [
        'enabled' => true,
        'batch_window' => 30, // minutos
        'max_batch_size' => 10, // forcing por email
    ],
];
