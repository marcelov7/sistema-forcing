<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap da aplicação Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Verificando e corrigindo coluna data_liberacao ===\n";
    
    // Verificar se a coluna existe
    echo "Verificando se a coluna data_liberacao existe...\n";
    
    $hasColumn = false;
    try {
        $columns = DB::select("PRAGMA table_info(forcing)");
        foreach ($columns as $column) {
            if ($column->name === 'data_liberacao') {
                $hasColumn = true;
                break;
            }
        }
    } catch (Exception $e) {
        echo "Erro ao verificar colunas: " . $e->getMessage() . "\n";
    }
    
    if ($hasColumn) {
        echo "✅ Coluna data_liberacao já existe!\n";
    } else {
        echo "❌ Coluna data_liberacao não existe. Criando...\n";
        
        try {
            DB::statement('ALTER TABLE forcing ADD COLUMN data_liberacao DATETIME NULL');
            echo "✅ Coluna data_liberacao criada com sucesso!\n";
        } catch (Exception $e) {
            echo "❌ Erro ao criar coluna: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    // Verificar novamente
    $columns = DB::select("PRAGMA table_info(forcing)");
    echo "\n=== Colunas da tabela forcing ===\n";
    foreach ($columns as $column) {
        echo "- {$column->name} ({$column->type})\n";
    }
    
    // Migrar dados existentes se necessário
    echo "\n=== Migrando dados existentes ===\n";
    $updated = DB::update("
        UPDATE forcing 
        SET data_liberacao = updated_at 
        WHERE status = 'liberado' 
        AND liberador_id IS NOT NULL 
        AND (data_liberacao IS NULL OR data_liberacao = '')
    ");
    
    echo "✅ Migração concluída! {$updated} registros atualizados.\n";
    
    echo "\n=== Limpando cache ===\n";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅ Cache limpo!\n";
    
    echo "\n🎉 Processo concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
