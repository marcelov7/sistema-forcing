#!/bin/bash

echo "🔧 DIAGNÓSTICO COMPLETO E CORREÇÃO..."

cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

echo "1. 📋 Verificando permissões detalhadas:"
ls -la resources/views/
ls -la resources/views/forcing/

echo ""
echo "2. 🔍 Últimos erros do Laravel:"
tail -n 30 storage/logs/laravel.log

echo ""
echo "3. 🔐 Corrigindo todas as permissões:"
chown -R clp:clp .
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

echo ""
echo "4. 🧹 Limpeza completa de cache:"
rm -rf storage/framework/views/*
rm -rf storage/framework/cache/data/*
rm -rf bootstrap/cache/*.php

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "5. 🔍 Verificando sintaxe dos arquivos principais:"
php -l resources/views/layouts/app.blade.php
php -l resources/views/forcing/index.blade.php

echo ""
echo "6. 📊 Verificando status do servidor web:"
systemctl status nginx | head -n 10

echo ""
echo "7. 🌐 Testando resposta do servidor:"
curl -I https://forcing.devaxis.com.br

echo ""
echo "✅ Diagnóstico concluído!"
