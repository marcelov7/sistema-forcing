<?php

// Script para corrigir dados de liberação no SQLite
$databasePath = __DIR__ . '/database/database.sqlite';

if (!file_exists($databasePath)) {
    echo "Banco de dados não encontrado: $databasePath\n";
    exit(1);
}

try {
    $pdo = new PDO("sqlite:$databasePath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se a coluna data_liberacao existe
    $stmt = $pdo->query("PRAGMA table_info(forcing)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasDataLiberacao = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'data_liberacao') {
            $hasDataLiberacao = true;
            break;
        }
    }
    
    if (!$hasDataLiberacao) {
        echo "Adicionando coluna data_liberacao...\n";
        $pdo->exec("ALTER TABLE forcing ADD COLUMN data_liberacao DATETIME NULL");
        echo "Coluna adicionada com sucesso!\n";
    }
    
    // Corrigir dados existentes
    echo "Corrigindo dados existentes...\n";
    $stmt = $pdo->prepare("
        UPDATE forcing 
        SET data_liberacao = data_retirada, 
            data_retirada = NULL 
        WHERE status = 'liberado' 
        AND data_retirada IS NOT NULL 
        AND data_liberacao IS NULL
    ");
    
    $affected = $stmt->execute();
    echo "Dados corrigidos! Registros afetados: " . $stmt->rowCount() . "\n";
    
    echo "Script executado com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
