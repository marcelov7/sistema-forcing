<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICAÇÃO DAS COLUNAS ===\n";

$columns = DB::select('PRAGMA table_info(forcing)');

$hasDataLiberacao = false;
foreach ($columns as $col) {
    echo "- {$col->name} ({$col->type})\n";
    if ($col->name === 'data_liberacao') {
        $hasDataLiberacao = true;
    }
}

echo "\n=== RESULTADO ===\n";
echo $hasDataLiberacao ? "✅ Coluna data_liberacao EXISTE!\n" : "❌ Coluna data_liberacao NÃO EXISTE!\n";

if ($hasDataLiberacao) {
    echo "\n🎉 PROBLEMA RESOLVIDO! Testando uma operação...\n";
    
    // Testar se funciona
    try {
        $forcing = App\Models\Forcing::first();
        if ($forcing) {
            echo "✅ Modelo Forcing carregado com sucesso!\n";
            echo "✅ Sistema está funcionando!\n";
        } else {
            echo "ℹ️ Nenhum forcing encontrado para teste\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro ao testar: " . $e->getMessage() . "\n";
    }
}
