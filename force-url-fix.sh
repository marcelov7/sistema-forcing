#!/bin/bash

echo "=== CORREÇÃO FORÇADA DE URL ==="
echo ""

# Definir PATH
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

# Navegar para o projeto
cd ~/htdocs/forcing.devaxis.com.br

echo "1. Backup do .env atual:"
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo "✓ Backup criado"
echo ""

echo "2. Forçando configurações no .env:"
# Garantir que APP_URL está correto
sed -i 's/^APP_URL=.*/APP_URL=https:\/\/forcing.devaxis.com.br/' .env

# Adicionar configurações específicas para forçar HTTPS
echo "" >> .env
echo "# Configurações forçadas para HTTPS" >> .env
echo "FORCE_HTTPS=true" >> .env
echo "APP_FORCE_HTTPS=true" >> .env

echo "✓ Configurações adicionadas"
echo ""

echo "3. Verificando .env atualizado:"
grep -E "^APP_URL|^FORCE_HTTPS|^APP_FORCE_HTTPS" .env
echo ""

echo "4. Removendo todos os caches:"
rm -rf bootstrap/cache/config.php
rm -rf bootstrap/cache/routes.php
rm -rf bootstrap/cache/services.php
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✓ Caches removidos"
echo ""

echo "5. Criando cache de configuração otimizado:"
php artisan config:cache
echo "✓ Cache criado"
echo ""

echo "6. Testando geração de URL:"
echo "Resultado do teste:"
php public/index.php | head -3
echo ""

echo "7. Se ainda não funcionou, vamos forçar via AppServiceProvider:"
# Criar backup do AppServiceProvider
cp app/Providers/AppServiceProvider.php app/Providers/AppServiceProvider.php.backup 2>/dev/null || echo "AppServiceProvider não encontrado - criando..."

# Verificar se já existe a configuração
if ! grep -q "URL::forceScheme" app/Providers/AppServiceProvider.php 2>/dev/null; then
    echo "Adicionando forceScheme ao AppServiceProvider..."
    
    # Criar o arquivo se não existir
    if [ ! -f "app/Providers/AppServiceProvider.php" ]; then
        mkdir -p app/Providers
        cat > app/Providers/AppServiceProvider.php << 'EOF'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (config('app.env') === 'production' || config('app.force_https')) {
            URL::forceScheme('https');
        }
        
        // Forçar host correto
        if (config('app.url')) {
            $url = parse_url(config('app.url'));
            if (isset($url['host'])) {
                URL::forceRootUrl(config('app.url'));
            }
        }
    }
}
EOF
    else
        # Adicionar ao arquivo existente
        sed -i '/public function boot()/a\        if (config('\''app.env'\'') === '\''production'\'' || config('\''app.force_https'\'')) {\n            URL::forceScheme('\''https'\'');\n        }\n        \n        if (config('\''app.url'\'')) {\n            $url = parse_url(config('\''app.url'\''));\n            if (isset($url['\''host'\''])) {\n                URL::forceRootUrl(config('\''app.url'\''));\n            }\n        }' app/Providers/AppServiceProvider.php
    fi
    
    echo "✓ AppServiceProvider atualizado"
else
    echo "✓ AppServiceProvider já configurado"
fi
echo ""

echo "8. Limpando cache novamente após mudanças:"
php artisan config:clear
php artisan cache:clear
echo ""

echo "9. Teste final:"
php public/index.php | head -3
echo ""

echo "=== CORREÇÃO CONCLUÍDA ==="
echo "Teste o site: https://forcing.devaxis.com.br"
