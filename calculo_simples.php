<?php
echo "=== CONTABILIZAÇÃO ATUALIZADA ===\n";

// Dados simulados baseados no banco atual
$admins = 5;
$liberadores = 3;
$executantes = 7;
$usuarios = 6;

echo "👥 USUÁRIOS:\n";
echo "- Admins: {$admins}\n";
echo "- Liberadores: {$liberadores}\n";
echo "- Executantes: {$executantes}\n";
echo "- Usuários: {$usuarios}\n\n";

echo "📧 EMAILS POR ETAPA (ATUALIZADO):\n\n";

// 1. Criação
echo "1️⃣ CRIAÇÃO: 1 email (liberador específico)\n";

// 2. Liberação  
$liberacaoEmails = $executantes + $admins;
echo "2️⃣ LIBERAÇÃO: {$liberacaoEmails} emails (executantes + admins)\n";

// 3. Execução
$execucaoEmails = $liberadores + $admins;
echo "3️⃣ EXECUÇÃO: {$execucaoEmails} emails (liberadores + admins)\n";

// 4. Solicitação (NOVA LÓGICA)
$solicitacaoEmails = 1 + 1 + $executantes + $admins; // criador + liberador + executantes + admins
echo "4️⃣ SOLICITAÇÃO: {$solicitacaoEmails} emails (criador + liberador + executantes + admins)\n";

// 5. Confirmação
$confirmacaoEmails = 1 + $admins;
echo "5️⃣ CONFIRMAÇÃO: {$confirmacaoEmails} emails (criador + admins)\n\n";

$total = 1 + $liberacaoEmails + $execucaoEmails + $solicitacaoEmails + $confirmacaoEmails;

echo "🎯 TOTAL: {$total} emails por ciclo\n\n";

echo "📊 PROJEÇÕES:\n";
foreach ([10, 25, 50, 100] as $forcings) {
    $emailsTotal = $forcings * $total;
    echo "• {$forcings} forcings/mês = {$emailsTotal} emails/mês\n";
}

echo "\n💰 CUSTO ESTIMADO (Amazon SES):\n";
foreach ([10, 25, 50, 100] as $forcings) {
    $emailsTotal = $forcings * $total;
    $custo = $emailsTotal * 0.0001;
    echo "• {$forcings} forcings = \${$custo}/mês\n";
}
?>
