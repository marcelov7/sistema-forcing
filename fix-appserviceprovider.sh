#!/bin/bash

echo "=== CORREÇÃO DO APPSERVICEPROVIDER ==="
echo ""

# Definir PATH
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

# Navegar para o projeto
cd ~/htdocs/forcing.devaxis.com.br

echo "1. Fazendo backup do AppServiceProvider com erro:"
cp app/Providers/AppServiceProvider.php app/Providers/AppServiceProvider.php.error.backup
echo "✓ Backup criado"
echo ""

echo "2. Criando AppServiceProvider correto:"
cat > app/Providers/AppServiceProvider.php << 'EOF'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forçar HTTPS em produção ou quando configurado
        if (config('app.env') === 'production' || config('app.force_https')) {
            URL::forceScheme('https');
        }
        
        // Forçar host correto baseado no APP_URL
        if (config('app.url')) {
            $url = parse_url(config('app.url'));
            if (isset($url['host'])) {
                URL::forceRootUrl(config('app.url'));
            }
        }
    }
}
EOF

echo "✓ AppServiceProvider criado corretamente"
echo ""

echo "3. Verificando sintaxe PHP:"
php -l app/Providers/AppServiceProvider.php
echo ""

echo "4. Removendo cache de configuração:"
rm -rf bootstrap/cache/config.php
echo "✓ Cache removido"
echo ""

echo "5. Testando Laravel:"
php artisan config:clear
echo ""

echo "6. Criando cache otimizado:"
php artisan config:cache
echo ""

echo "7. Teste final da geração de URL:"
echo "Resultado:"
php public/index.php | head -5
echo ""

echo "=== CORREÇÃO CONCLUÍDA ==="
echo "Teste o site: https://forcing.devaxis.com.br"

