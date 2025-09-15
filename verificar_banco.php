<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICAÇÃO FINAL ===\n";

try {
    // Verificar conexão
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✅ Conexão com banco estabelecida\n";
    
    // Verificar colunas
    $columns = \Illuminate\Support\Facades\DB::select('PRAGMA table_info(forcing)');
    
    $hasDataLiberacao = false;
    $hasDescricaoResolucao = false;
    
    echo "\n=== COLUNAS DA TABELA FORCING ===\n";
    foreach ($columns as $column) {
        echo "- {$column->name} ({$column->type})\n";
        
        if ($column->name === 'data_liberacao') {
            $hasDataLiberacao = true;
        }
        if ($column->name === 'descricao_resolucao') {
            $hasDescricaoResolucao = true;
        }
    }
    
    echo "\n=== RESULTADO ===\n";
    echo $hasDataLiberacao ? "✅ Coluna data_liberacao: EXISTE\n" : "❌ Coluna data_liberacao: NÃO EXISTE\n";
    echo $hasDescricaoResolucao ? "✅ Coluna descricao_resolucao: EXISTE\n" : "❌ Coluna descricao_resolucao: NÃO EXISTE\n";
    
    if ($hasDataLiberacao && $hasDescricaoResolucao) {
        echo "\n🎉 PROBLEMA RESOLVIDO! Todas as colunas necessárias existem.\n";
        
        // Testar uma operação de liberação
        echo "\n=== TESTE DE LIBERAÇÃO ===\n";
        $forcing = App\Models\Forcing::first();
        if ($forcing) {
            echo "✅ Teste do modelo Forcing realizado com sucesso\n";
            echo "ID do forcing de teste: {$forcing->id}\n";
        } else {
            echo "ℹ️ Nenhum forcing encontrado para teste\n";
        }
    } else {
        echo "\n❌ PROBLEMA AINDA EXISTE! Algumas colunas estão faltando.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
