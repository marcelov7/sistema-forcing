#!/bin/bash

echo "=== DIAGNÓSTICO AVANÇADO DE GERAÇÃO DE URLs ==="
echo ""

# Definir PATH
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

# Navegar para o projeto
cd ~/htdocs/forcing.devaxis.com.br

echo "1. Verificando configurações atuais:"
echo "APP_URL: $(grep APP_URL .env)"
echo "APP_ENV: $(grep APP_ENV .env)"
echo ""

echo "2. Testando geração de URL via Artisan Tinker:"
php artisan tinker --execute="
echo 'APP_URL: ' . config('app.url') . PHP_EOL;
echo 'URL::to(\"/\"): ' . url('/') . PHP_EOL;
echo 'route(\"login\"): ' . route('login') . PHP_EOL;
echo 'Request::getHttpHost(): ' . (app('request')->getHttpHost() ?: 'NULL') . PHP_EOL;
echo 'Request::getSchemeAndHttpHost(): ' . (app('request')->getSchemeAndHttpHost() ?: 'NULL') . PHP_EOL;
"
echo ""

echo "3. Verificando variáveis de ambiente PHP:"
php -r "
echo 'SERVER_NAME: ' . (\$_SERVER['SERVER_NAME'] ?? 'NULL') . PHP_EOL;
echo 'HTTP_HOST: ' . (\$_SERVER['HTTP_HOST'] ?? 'NULL') . PHP_EOL;
echo 'REQUEST_SCHEME: ' . (\$_SERVER['REQUEST_SCHEME'] ?? 'NULL') . PHP_EOL;
echo 'HTTPS: ' . (\$_SERVER['HTTPS'] ?? 'NULL') . PHP_EOL;
echo 'SERVER_PORT: ' . (\$_SERVER['SERVER_PORT'] ?? 'NULL') . PHP_EOL;
"
echo ""

echo "4. Testando via curl interno:"
echo "curl -I http://localhost:"
curl -I http://localhost 2>/dev/null | head -5 || echo "Falha no curl localhost"
echo ""

echo "5. Verificando se há cache de configuração:"
if [ -f "bootstrap/cache/config.php" ]; then
    echo "❌ Cache de configuração encontrado - removendo..."
    rm -f bootstrap/cache/config.php
    echo "✓ Cache removido"
else
    echo "✓ Sem cache de configuração"
fi
echo ""

echo "6. Verificando providers de URL:"
php artisan tinker --execute="
echo 'URL Provider: ' . get_class(app('url')) . PHP_EOL;
echo 'Request Provider: ' . get_class(app('request')) . PHP_EOL;
"
echo ""

echo "7. Forçando recriação de configuração:"
php artisan config:cache
echo ""

echo "8. Testando novamente após cache:"
php public/index.php | head -5
echo ""

echo "9. Verificando se há middleware interferindo:"
php artisan route:list --path=login --columns=uri,name,action,middleware
echo ""

echo "=== DIAGNÓSTICO CONCLUÍDO ==="

