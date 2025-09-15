#!/bin/bash

echo "=== SCRIPT DE CORREÇÃO DO SERVIDOR ==="
echo "Executando limpeza completa de cache e verificações..."
echo ""

# Definir PATH corretamente
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

# Navegar para o diretório do projeto
cd ~/htdocs/forcing.devaxis.com.br

echo "1. Verificando APP_URL atual:"
grep APP_URL .env
echo ""

echo "2. Limpando todos os caches do Laravel:"
php artisan config:clear
php artisan cache:clear  
php artisan route:clear
php artisan view:clear
echo ""

echo "3. Verificando se o banco SQLite existe:"
if [ -f "database/database.sqlite" ]; then
    echo "✓ Banco SQLite encontrado"
    ls -la database/database.sqlite
else
    echo "✗ Banco SQLite não encontrado - criando..."
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi
echo ""

echo "4. Executando migrações:"
php artisan migrate --force
echo ""

echo "5. Testando uma rota simples:"
echo "Testando: php artisan route:list --path=login"
php artisan route:list --path=login
echo ""

echo "6. Verificando permissões dos arquivos críticos:"
echo "Permissões do public/index.php:"
ls -la public/index.php
echo ""
echo "Permissões do .env:"
ls -la .env
echo ""

echo "7. Testando o Laravel via linha de comando:"
echo "Executando: php public/index.php"
echo "Resultado:"
php public/index.php | head -10
echo ""

echo "8. Verificando se o servidor web pode acessar os arquivos:"
echo "Conteúdo do .htaccess:"
cat public/.htaccess
echo ""

echo "=== SCRIPT CONCLUÍDO ==="
echo "Por favor, teste o site agora: https://forcing.devaxis.com.br"

