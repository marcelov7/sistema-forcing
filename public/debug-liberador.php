<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Forcing;

echo "<h1>Debug - Sistema de Liberadores</h1>";

echo "<h2>1. Usuários com perfil 'liberador':</h2>";
$liberadores = User::where('perfil', 'liberador')->get(['id', 'name', 'username', 'perfil', 'unit_id']);
if ($liberadores->count() > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Username</th><th>Perfil</th><th>Unit ID</th></tr>";
    foreach ($liberadores as $liberador) {
        echo "<tr>";
        echo "<td>{$liberador->id}</td>";
        echo "<td>{$liberador->name}</td>";
        echo "<td>{$liberador->username}</td>";
        echo "<td>{$liberador->perfil}</td>";
        echo "<td>{$liberador->unit_id}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Nenhum usuário com perfil 'liberador' encontrado!</p>";
}

echo "<h2>2. Usuários com perfil 'admin':</h2>";
$admins = User::where('perfil', 'admin')->get(['id', 'name', 'username', 'perfil', 'unit_id']);
if ($admins->count() > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Username</th><th>Perfil</th><th>Unit ID</th></tr>";
    foreach ($admins as $admin) {
        echo "<tr>";
        echo "<td>{$admin->id}</td>";
        echo "<td>{$admin->name}</td>";
        echo "<td>{$admin->username}</td>";
        echo "<td>{$admin->perfil}</td>";
        echo "<td>{$admin->unit_id}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Nenhum usuário com perfil 'admin' encontrado!</p>";
}

echo "<h2>3. Forcings pendentes (que podem ser liberados):</h2>";
$forcingsPendentes = Forcing::where('status', 'pendente')->with(['user', 'unit'])->get();
if ($forcingsPendentes->count() > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>TAG</th><th>Status</th><th>Criado por</th><th>Unit ID</th><th>Pode ser liberado?</th></tr>";
    foreach ($forcingsPendentes as $forcing) {
        $podeSerLiberado = $forcing->podeSerLiberado() ? '✅ Sim' : '❌ Não';
        echo "<tr>";
        echo "<td>{$forcing->id}</td>";
        echo "<td>{$forcing->tag}</td>";
        echo "<td>{$forcing->status}</td>";
        echo "<td>{$forcing->user->name} (Unit: {$forcing->user->unit_id})</td>";
        echo "<td>{$forcing->unit_id}</td>";
        echo "<td>{$podeSerLiberado}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Nenhum forcing pendente encontrado!</p>";
}

echo "<h2>4. Teste de Políticas de Liberação:</h2>";
if ($liberadores->count() > 0 && $forcingsPendentes->count() > 0) {
    $liberador = $liberadores->first();
    $forcing = $forcingsPendentes->first();
    
    echo "<p><strong>Testando liberador:</strong> {$liberador->name} (ID: {$liberador->id}, Unit: {$liberador->unit_id})</p>";
    echo "<p><strong>Testando forcing:</strong> {$forcing->tag} (ID: {$forcing->id}, Unit: {$forcing->unit_id})</p>";
    
    // Simular autenticação
    auth()->login($liberador);
    
    // Testar política
    $podeLiberar = $liberador->can('liberar', $forcing);
    
    echo "<p><strong>Resultado da política:</strong> " . ($podeLiberar ? '✅ PODE liberar' : '❌ NÃO PODE liberar') . "</p>";
    
    // Verificar condições específicas
    echo "<h3>Condições da política:</h3>";
    echo "<ul>";
    echo "<li>Perfil é 'liberador': " . ($liberador->perfil === 'liberador' ? '✅' : '❌') . "</li>";
    echo "<li>Unit ID do liberador: {$liberador->unit_id}</li>";
    echo "<li>Unit ID do forcing: {$forcing->unit_id}</li>";
    echo "<li>Units são iguais: " . ($liberador->unit_id === $forcing->unit_id ? '✅' : '❌') . "</li>";
    echo "<li>Forcing pode ser liberado: " . ($forcing->podeSerLiberado() ? '✅' : '❌') . "</li>";
    echo "</ul>";
    
    auth()->logout();
} else {
    echo "<p style='color: red;'>❌ Não é possível testar - faltam liberadores ou forcings pendentes!</p>";
}

echo "<h2>5. Todos os perfis de usuários:</h2>";
$perfis = User::select('perfil')->distinct()->pluck('perfil');
echo "<p><strong>Perfis encontrados:</strong> " . implode(', ', $perfis->toArray()) . "</p>";

echo "<h2>6. Verificação de Unidades:</h2>";
$units = \App\Models\Unit::all(['id', 'name']);
if ($units->count() > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th></tr>";
    foreach ($units as $unit) {
        echo "<tr>";
        echo "<td>{$unit->id}</td>";
        echo "<td>{$unit->name}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Nenhuma unidade encontrada!</p>";
}

echo "<hr>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
?>
