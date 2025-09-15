#!/bin/bash

echo "========================================"
echo "   DEPLOY SSH - ALTERACOES ELETRICAS"
echo "========================================"
echo

echo "[1/8] Conectando via SSH..."
ssh devaxis-forcing@forcing.devaxis.com.br << 'EOF'
echo "[2/8] Navegando para o diretorio do projeto..."
cd /home/devaxis-forcing/forcing

echo "[3/8] Verificando status do Git..."
git status

echo "[4/8] Fazendo pull das atualizacoes..."
git pull origin main

echo "[5/8] Instalando dependencias..."
composer install --no-dev --optimize-autoloader

echo "[6/8] Executando migracoes..."
php artisan migrate --force

echo "[7/8] Limpando e otimizando cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[8/8] Ajustando permissoes..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R devaxis-forcing:devaxis-forcing storage/
chown -R devaxis-forcing:devaxis-forcing bootstrap/cache/

echo
echo "========================================"
echo "   DEPLOY CONCLUIDO COM SUCESSO!"
echo "========================================"
echo
echo "Verificacoes pos-deploy:"
echo "- Acesse: https://forcing.devaxis.com.br"
echo "- Verifique o menu 'Alteracoes Eletricas'"
echo "- Teste criar uma nova alteracao"
echo "- Teste o botao PDF"
echo
echo "Para verificar logs:"
echo "tail -f storage/logs/laravel.log"
echo
EOF

echo
echo "Deploy finalizado!"

