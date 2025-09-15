<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Forcing;
use App\Policies\ForcingPolicy;

echo "<h1>Teste - Sistema de Liberadores Corrigido</h1>";

echo "<h2>1. Verificando usuários liberadores:</h2>";
$liberadores = User::where('perfil', 'liberador')->get();
if ($liberadores->count() > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Perfil</th><th>Unit ID</th></tr>";
    foreach ($liberadores as $liberador) {
        echo "<tr>";
        echo "<td>{$liberador->id}</td>";
        echo "<td>{$liberador->name}</td>";
        echo "<td>{$liberador->perfil}</td>";
        echo "<td>{$liberador->unit_id}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Nenhum liberador encontrado!</p>";
}

echo "<h2>2. Verificando forcings pendentes:</h2>";
$forcingsPendentes = Forcing::where('status', 'pendente')->with(['user', 'unit'])->get();
if ($forcingsPendentes->count() > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>TAG</th><th>Status</th><th>Unit ID</th><th>Pode ser liberado?</th></tr>";
    foreach ($forcingsPendentes as $forcing) {
        $podeSerLiberado = $forcing->podeSerLiberado() ? '✅ Sim' : '❌ Não';
        echo "<tr>";
        echo "<td>{$forcing->id}</td>";
        echo "<td>{$forcing->tag}</td>";
        echo "<td>{$forcing->status}</td>";
        echo "<td>{$forcing->unit_id}</td>";
        echo "<td>{$podeSerLiberado}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Nenhum forcing pendente encontrado!</p>";
}

echo "<h2>3. Testando políticas corrigidas:</h2>";
if ($liberadores->count() > 0 && $forcingsPendentes->count() > 0) {
    $liberador = $liberadores->first();
    $forcing = $forcingsPendentes->first();
    $policy = new ForcingPolicy();
    
    echo "<p><strong>Testando liberador:</strong> {$liberador->name} (ID: {$liberador->id}, Unit: {$liberador->unit_id})</p>";
    echo "<p><strong>Testando forcing:</strong> {$forcing->tag} (ID: {$forcing->id}, Unit: {$forcing->unit_id})</p>";
    
    // Testar política diretamente
    $podeLiberar = $policy->liberar($liberador, $forcing);
    
    echo "<p><strong>Resultado da política:</strong> " . ($podeLiberar ? '✅ PODE liberar' : '❌ NÃO PODE liberar') . "</p>";
    
    // Verificar condições específicas
    echo "<h3>Condições da política corrigida:</h3>";
    echo "<ul>";
    echo "<li>Perfil é 'liberador': " . ($liberador->perfil === 'liberador' ? '✅' : '❌') . "</li>";
    echo "<li>Forcing pode ser liberado: " . ($forcing->podeSerLiberado() ? '✅' : '❌') . "</li>";
    echo "<li><strong>NOVA LÓGICA:</strong> Liberadores podem liberar forcings de qualquer unidade!</li>";
    echo "</ul>";
    
    if ($podeLiberar) {
        echo "<p style='color: green; font-weight: bold;'>🎉 SUCESSO! O liberador agora pode liberar o forcing!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Ainda há problema na política!</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Não é possível testar - faltam liberadores ou forcings pendentes!</p>";
}

echo "<h2>4. Resumo das correções aplicadas:</h2>";
echo "<ul>";
echo "<li>✅ <strong>Política de liberação:</strong> Removida restrição de unit_id para liberadores</li>";
echo "<li>✅ <strong>Política de retirada:</strong> Liberadores podem retirar forcings de qualquer unidade</li>";
echo "<li>✅ <strong>Modal de liberação:</strong> Corrigido campo observacoes_liberacao</li>";
echo "<li>✅ <strong>Modais:</strong> Corrigido campo titulo para tag e descricao_equipamento</li>";
echo "</ul>";

echo "<h2>5. Como testar no sistema:</h2>";
echo "<ol>";
echo "<li>Acesse o sistema como um usuário com perfil 'liberador'</li>";
echo "<li>Vá para a lista de forcings</li>";
echo "<li>Procure por forcings com status 'Pendente'</li>";
echo "<li>Clique no botão verde (✓) para liberar</li>";
echo "<li>Preencha as observações se desejar</li>";
echo "<li>Clique em 'Liberar Forcing'</li>";
echo "<li>O forcing deve mudar para status 'Liberado'</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<p><strong>Status:</strong> <span style='color: green; font-weight: bold;'>CORREÇÕES APLICADAS ✅</span></p>";
?>
