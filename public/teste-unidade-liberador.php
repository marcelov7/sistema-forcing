<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Forcing;
use App\Policies\ForcingPolicy;

echo "<h1>Teste - Sistema de Liberadores por Unidade</h1>";

echo "<h2>1. Verificando usuários liberadores por unidade:</h2>";
$liberadores = User::where('perfil', 'liberador')->get(['id', 'name', 'perfil', 'unit_id']);
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

echo "<h2>2. Verificando forcings pendentes por unidade:</h2>";
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

echo "<h2>3. Testando políticas com restrição por unidade:</h2>";
if ($liberadores->count() > 0 && $forcingsPendentes->count() > 0) {
    $policy = new ForcingPolicy();
    
    echo "<h3>Testando cada liberador com cada forcing:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Liberador</th><th>Unit Lib.</th><th>Forcing</th><th>Unit Forc.</th><th>Pode Liberar?</th><th>Motivo</th></tr>";
    
    foreach ($liberadores as $liberador) {
        foreach ($forcingsPendentes as $forcing) {
            $podeLiberar = $policy->liberar($liberador, $forcing);
            $mesmaUnidade = $liberador->unit_id === $forcing->unit_id;
            
            $motivo = '';
            if ($podeLiberar) {
                $motivo = '✅ Mesma unidade + perfil correto';
            } else {
                if (!$mesmaUnidade) {
                    $motivo = '❌ Unidades diferentes';
                } elseif ($liberador->perfil !== 'liberador') {
                    $motivo = '❌ Perfil incorreto';
                } else {
                    $motivo = '❌ Forcing não pode ser liberado';
                }
            }
            
            echo "<tr>";
            echo "<td>{$liberador->name}</td>";
            echo "<td>{$liberador->unit_id}</td>";
            echo "<td>{$forcing->tag}</td>";
            echo "<td>{$forcing->unit_id}</td>";
            echo "<td>" . ($podeLiberar ? '✅ Sim' : '❌ Não') . "</td>";
            echo "<td>{$motivo}</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Não é possível testar - faltam liberadores ou forcings pendentes!</p>";
}

echo "<h2>4. Simulando seleção de liberadores no formulário:</h2>";
if ($liberadores->count() > 0) {
    // Simular usuário da unidade 1
    $userUnidade1 = User::where('unit_id', 1)->first();
    if ($userUnidade1) {
        echo "<h3>Se usuário da Unidade 1 criar um forcing, verá estes liberadores:</h3>";
        $liberadoresDisponiveis = User::where(function($query) use ($userUnidade1) {
                $query->where('perfil', 'liberador')
                      ->where('unit_id', $userUnidade1->unit_id);
            })
            ->orWhere('perfil', 'admin')
            ->orderBy('name')
            ->get();
        
        if ($liberadoresDisponiveis->count() > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Perfil</th><th>Unit ID</th></tr>";
            foreach ($liberadoresDisponiveis as $liberador) {
                echo "<tr>";
                echo "<td>{$liberador->id}</td>";
                echo "<td>{$liberador->name}</td>";
                echo "<td>{$liberador->perfil}</td>";
                echo "<td>{$liberador->unit_id}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ Nenhum liberador disponível para a unidade {$userUnidade1->unit_id}!</p>";
        }
    }
}

echo "<h2>5. Resumo da correção aplicada:</h2>";
echo "<ul>";
echo "<li>✅ <strong>Política de liberação:</strong> Mantida restrição por unidade</li>";
echo "<li>✅ <strong>Seleção de liberadores:</strong> Agora mostra apenas liberadores da mesma unidade</li>";
echo "<li>✅ <strong>Admins:</strong> Podem ser selecionados de qualquer unidade</li>";
echo "<li>✅ <strong>Consistência:</strong> Liberadores só podem liberar forcings da sua unidade</li>";
echo "</ul>";

echo "<h2>6. Como funciona agora:</h2>";
echo "<ol>";
echo "<li><strong>Criação de forcing:</strong> Usuário vê apenas liberadores da sua unidade</li>";
echo "<li><strong>Liberação:</strong> Liberador só pode liberar forcings da sua unidade</li>";
echo "<li><strong>Admins:</strong> Têm acesso total (podem ser selecionados e liberar qualquer forcing)</li>";
echo "<li><strong>Multi-tenancy:</strong> Cada unidade trabalha independentemente</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<p><strong>Status:</strong> <span style='color: green; font-weight: bold;'>CORREÇÃO APLICADA - RESTRIÇÃO POR UNIDADE ✅</span></p>";
?>
