<?php

// Script para migrar dados antigos de liberação
$databasePath = __DIR__ . '/database/database.sqlite';

if (!file_exists($databasePath)) {
    echo "Banco de dados não encontrado: $databasePath\n";
    exit(1);
}

try {
    $pdo = new PDO("sqlite:$databasePath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Migrando dados de forcing liberados...\n";
    
    // Atualizar forcings com status 'liberado' que não têm data_liberacao
    $stmt = $pdo->prepare("
        UPDATE forcing 
        SET data_liberacao = updated_at 
        WHERE status = 'liberado' 
        AND liberador_id IS NOT NULL 
        AND (data_liberacao IS NULL OR data_liberacao = '')
    ");
    
    $stmt->execute();
    $affected = $stmt->rowCount();
    
    echo "Migração concluída! Registros atualizados: $affected\n";
    
    // Mostrar alguns exemplos dos dados migrados
    echo "\nVerificando dados migrados:\n";
    $stmt = $pdo->query("
        SELECT id, status, liberador_id, data_liberacao, updated_at 
        FROM forcing 
        WHERE status = 'liberado' 
        LIMIT 5
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo "ID: {$row['id']}, Status: {$row['status']}, Liberador: {$row['liberador_id']}, Data Liberação: {$row['data_liberacao']}\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
