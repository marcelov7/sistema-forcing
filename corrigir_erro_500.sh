#!/bin/bash

# Script de Correção - Erro 500 após atualização do index.blade.php
# Execute este script no servidor para corrigir problemas comuns

echo "🔧 INICIANDO CORREÇÃO DO ERRO 500..."

# 1. Navegar para o diretório do projeto
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

echo "📂 Diretório atual: $(pwd)"

# 2. Verificar se os arquivos existem
echo "📋 Verificando arquivos críticos..."
if [ ! -f "resources/views/forcing/index.blade.php" ]; then
    echo "❌ Arquivo index.blade.php não encontrado!"
    exit 1
fi

# 3. Limpar todos os caches
echo "🧹 Limpando caches do Laravel..."
php artisan config:clear
php artisan cache:clear  
php artisan view:clear
php artisan route:clear

# 4. Verificar permissões
echo "🔐 Verificando e corrigindo permissões..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R clp:clp storage/
chown -R clp:clp bootstrap/cache/

# 5. Verificar sintaxe PHP
echo "🔍 Verificando sintaxe do arquivo index.blade.php..."
php -l resources/views/forcing/index.blade.php

# 6. Recriar caches
echo "🔄 Recriando caches..."
php artisan config:cache
php artisan route:cache

# 7. Verificar logs de erro
echo "📋 Últimos erros no log:"
tail -n 10 storage/logs/laravel.log

# 8. Testar conexão com banco
echo "🗄️ Testando conexão com banco de dados..."
php artisan migrate:status

echo "✅ Correção concluída!"
echo "🌐 Teste o acesso: https://forcing.devaxis.com.br"
