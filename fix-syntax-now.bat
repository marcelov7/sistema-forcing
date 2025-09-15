@echo off
echo === CORREÇÃO RÁPIDA DO APPSERVICEPROVIDER ===
echo.

echo Conectando e corrigindo o arquivo...
ssh devaxis-forcing@forcing.devaxis.com.br "export PATH='/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin' && cd ~/htdocs/forcing.devaxis.com.br && echo 'Corrigindo AppServiceProvider...' && cat > app/Providers/AppServiceProvider.php << 'PHPEOF'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production' || config('app.force_https')) {
            URL::forceScheme('https');
        }
        
        if (config('app.url')) {
            $url = parse_url(config('app.url'));
            if (isset($url['host'])) {
                URL::forceRootUrl(config('app.url'));
            }
        }
    }
}
PHPEOF
echo 'Arquivo corrigido!' && php -l app/Providers/AppServiceProvider.php && echo 'Sintaxe OK!' && php artisan config:clear && echo 'Cache limpo!' && php public/index.php | head -5"

echo.
echo === TESTE O SITE AGORA: https://forcing.devaxis.com.br ===
pause

