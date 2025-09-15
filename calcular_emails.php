<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== CONTABILIZAÇÃO DE EMAILS POR CICLO DE FORCING ===\n\n";

try {
    // Contar usuários por perfil
    $users = \App\Models\User::all()->groupBy('perfil');
    
    $admins = $users->get('admin', collect())->count();
    $liberadores = $users->get('liberador', collect())->count();
    $executantes = $users->get('executante', collect())->count();
    $usuarios = $users->get('user', collect())->count();
    
    echo "📊 USUÁRIOS POR PERFIL:\n";
    echo "- Administradores: {$admins}\n";
    echo "- Liberadores: {$liberadores}\n";
    echo "- Executantes: {$executantes}\n";
    echo "- Usuários: {$usuarios}\n";
    echo "- TOTAL: " . ($admins + $liberadores + $executantes + $usuarios) . "\n\n";
    
    echo "📧 EMAILS POR ETAPA DO CICLO:\n\n";
    
    // 1. Criação do Forcing
    echo "1️⃣ CRIAÇÃO DO FORCING:\n";
    echo "   → Para: 1 liberador específico selecionado\n";
    echo "   → Emails enviados: 1\n\n";
    
    // 2. Liberação do Forcing
    echo "2️⃣ LIBERAÇÃO DO FORCING:\n";
    $executantesTotal = $executantes + $admins;
    echo "   → Para: Todos os executantes + admins\n";
    echo "   → Emails enviados: {$executantesTotal}\n\n";
    
    // 3. Execução do Forcing
    echo "3️⃣ EXECUÇÃO DO FORCING:\n";
    // Criador + liberador específico + executante + todos liberadores + admins
    $liberadoresTotal = $liberadores + $admins;
    $execucaoTotal = 1 + 1 + 1 + $liberadoresTotal; // Pode ter duplicatas, mas são removidas
    $execucaoTotalReal = max($liberadoresTotal + 2, $liberadoresTotal + 1); // Estimativa mais conservadora
    echo "   → Para: Criador + Liberador responsável + Executante + Todos liberadores + Admins\n";
    echo "   → Emails enviados (estimativa): {$liberadoresTotal} (duplicatas removidas)\n\n";
    
    // 4. Solicitação de Retirada
    echo "4️⃣ SOLICITAÇÃO DE RETIRADA:\n";
    // Criador + liberador específico + executantes + admins (duplicatas removidas)
    $solicitacaoTotal = 1 + 1 + $executantes + $admins; // Estima-se que sejam únicos na maioria dos casos
    $solicitacaoTotalReal = max($executantes + $admins + 1, $executantes + $admins + 2); // Estimativa conservadora
    echo "   → Para: Criador + Liberador responsável + Todos executantes + Admins\n";
    echo "   → Emails enviados: {$solicitacaoTotalReal}\n\n";
    
    // 5. Retirada Final (nossa nova implementação)
    echo "5️⃣ CONFIRMAÇÃO DE RETIRADA:\n";
    $retiradaTotal = 1 + $admins; // Criador + admins (duplicatas removidas se criador for admin)
    echo "   → Para: Solicitante (criador) + Admins\n";
    echo "   → Emails enviados: {$retiradaTotal}\n\n";
    
    // Total do ciclo
    $totalCiclo = 1 + $executantesTotal + $liberadoresTotal + $solicitacaoTotalReal + $retiradaTotal;
    
    echo "🎯 TOTAL POR CICLO COMPLETO:\n";
    echo "═══════════════════════════════════════\n";
    echo "Criação:           1 email\n";
    echo "Liberação:         {$executantesTotal} emails\n";
    echo "Execução:          {$liberadoresTotal} emails\n";
    echo "Solicitação:       {$solicitacaoTotalReal} emails\n";
    echo "Confirmação:       {$retiradaTotal} emails\n";
    echo "───────────────────────────────────────\n";
    echo "TOTAL POR FORCING: {$totalCiclo} emails\n";
    echo "═══════════════════════════════════════\n\n";
    
    // Projeções
    echo "📈 PROJEÇÕES DE USO:\n\n";
    
    $forcingsPorMes = [10, 20, 50, 100];
    
    foreach ($forcingsPorMes as $qtd) {
        $emailsMes = $qtd * $totalCiclo;
        echo "• {$qtd} forcings/mês = {$emailsMes} emails/mês\n";
    }
    
    echo "\n💡 OBSERVAÇÕES:\n";
    echo "- Valores consideram ciclo COMPLETO (criação → retirada)\n";
    echo "- Duplicatas são automaticamente removidas pelo sistema\n";
    echo "- Admin pode receber emails em múltiplas etapas\n";
    echo "- Liberador específico reduz significativamente os emails na criação\n";
    echo "- Nova implementação (confirmação) é muito mais econômica\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
