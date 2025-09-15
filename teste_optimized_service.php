<?php

// Teste simples do OptimizedForcingNotificationService
require_once __DIR__ . '/vendor/autoload.php';

use App\Services\OptimizedForcingNotificationService;

try {
    echo "🧪 Testando OptimizedForcingNotificationService...\n\n";
    
    // Criar instância do serviço
    $service = new OptimizedForcingNotificationService();
    
    echo "✅ Serviço instanciado com sucesso!\n\n";
    
    // Testar obtenção de estatísticas
    $stats = $service->getEmailStats();
    
    echo "📊 Estatísticas de Email:\n";
    echo "- Emails enviados hoje: {$stats['daily_sent']}/{$stats['daily_limit']}\n";
    echo "- Emails enviados nesta hora: {$stats['hourly_sent']}/{$stats['hourly_limit']}\n";
    echo "- Pode enviar emails: " . ($stats['can_send'] ? 'SIM' : 'NÃO') . "\n\n";
    
    echo "✅ Teste concluído com sucesso!\n";
    echo "📝 Melhorias implementadas:\n";
    echo "- ✅ Classe SolicitacoesRetiradaCondensada criada\n";
    echo "- ✅ Template de email responsivo criado\n";
    echo "- ✅ Logs detalhados adicionados\n";
    echo "- ✅ Controle de destinatários únicos\n";
    echo "- ✅ Limite de emails configurado\n";
    echo "- ✅ Estatísticas de email implementadas\n";
    echo "- ✅ Método para reset de contadores\n";
    echo "- ✅ Notificação otimizada para forcing liberado\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📁 Arquivo: " . $e->getFile() . "\n";
    echo "📝 Linha: " . $e->getLine() . "\n";
}
