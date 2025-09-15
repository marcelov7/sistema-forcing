<?php

// Script para corrigir definitivamente o problema da coluna data_liberacao

echo "=== CORREÇÃO DEFINITIVA DA COLUNA DATA_LIBERACAO ===\n";

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    echo "❌ Arquivo de banco não encontrado: $dbPath\n";
    exit(1);
}

try {
    // Conectar diretamente ao SQLite
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao SQLite\n";
    
    // Verificar se a coluna existe
    $stmt = $pdo->query("PRAGMA table_info(forcing)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasDataLiberacao = false;
    echo "\n=== COLUNAS ATUAIS ===\n";
    foreach ($columns as $column) {
        echo "- {$column['name']} ({$column['type']})\n";
        if ($column['name'] === 'data_liberacao') {
            $hasDataLiberacao = true;
        }
    }
    
    if ($hasDataLiberacao) {
        echo "\n✅ Coluna data_liberacao já existe!\n";
    } else {
        echo "\n❌ Coluna data_liberacao não existe. Adicionando...\n";
        
        // Adicionar a coluna
        $pdo->exec("ALTER TABLE forcing ADD COLUMN data_liberacao DATETIME NULL");
        echo "✅ Coluna data_liberacao adicionada!\n";
        
        // Verificar novamente
        $stmt = $pdo->query("PRAGMA table_info(forcing)");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $hasDataLiberacao = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'data_liberacao') {
                $hasDataLiberacao = true;
                break;
            }
        }
        
        if ($hasDataLiberacao) {
            echo "✅ Confirmado: Coluna data_liberacao criada com sucesso!\n";
        } else {
            echo "❌ Erro: Coluna data_liberacao ainda não existe após criação!\n";
            exit(1);
        }
    }
    
    // Verificar se a coluna descricao_resolucao existe
    $hasDescricaoResolucao = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'descricao_resolucao') {
            $hasDescricaoResolucao = true;
            break;
        }
    }
    
    if (!$hasDescricaoResolucao) {
        echo "\n❌ Coluna descricao_resolucao não existe. Adicionando...\n";
        $pdo->exec("ALTER TABLE forcing ADD COLUMN descricao_resolucao TEXT NULL");
        echo "✅ Coluna descricao_resolucao adicionada!\n";
    } else {
        echo "\n✅ Coluna descricao_resolucao já existe!\n";
    }
    
    // Migrar dados existentes
    echo "\n=== MIGRANDO DADOS EXISTENTES ===\n";
    $stmt = $pdo->prepare("
        UPDATE forcing 
        SET data_liberacao = updated_at 
        WHERE status = 'liberado' 
        AND liberador_id IS NOT NULL 
        AND (data_liberacao IS NULL OR data_liberacao = '')
    ");
    $stmt->execute();
    $updated = $stmt->rowCount();
    
    echo "✅ {$updated} registros migrados!\n";
    
    // Verificar a estrutura final
    echo "\n=== ESTRUTURA FINAL ===\n";
    $stmt = $pdo->query("PRAGMA table_info(forcing)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        $mark = in_array($column['name'], ['data_liberacao', 'descricao_resolucao']) ? '✅' : '-';
        echo "$mark {$column['name']} ({$column['type']})\n";
    }
    
    echo "\n🎉 CORREÇÃO CONCLUÍDA COM SUCESSO!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
