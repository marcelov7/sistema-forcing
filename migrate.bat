@echo off
echo 🚀 Iniciando migração manual do sistema Forcing...

REM Conectar ao servidor e fazer backup
echo 📦 Conectando ao servidor...
ssh root@31.97.168.137 "cd /home/forcing/htdocs/forcing.digitalinnovation.com.br && mkdir -p backups/$(date +%%Y%%m%%d_%%H%%M%%S) && cp -r * backups/$(date +%%Y%%m%%d_%%H%%M%%S)/ 2>/dev/null || true"

echo 🔧 Enviando middleware otimizado...
scp "app\Http\Middleware\CheckProfile.php" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/app/Http/Middleware/
scp "app\Http\Middleware\SuperAdminMiddleware.php" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/app/Http/Middleware/

echo ⚙️ Enviando services otimizados...
scp "app\Services\OptimizedForcingNotificationService.php" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/app/Services/

echo 🎮 Enviando controllers otimizados...
scp "app\Http\Controllers\ForcingController.php" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/app/Http/Controllers/
scp "app\Http\Controllers\HomeController.php" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/app/Http/Controllers/

echo 🎨 Enviando sistema de acessibilidade...
scp "public\css\colorblind-accessibility.css" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/public/css/
scp "public\js\colorblind-simple.js" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/public/js/

echo 📱 Enviando layout atualizado...
scp "resources\views\layouts\app.blade.php" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/resources/views/layouts/

echo ⚙️ Enviando configurações...
scp ".env" root@31.97.168.137:/home/forcing/htdocs/forcing.digitalinnovation.com.br/

echo 🔄 Executando comandos pós-migração...
ssh root@31.97.168.137 "cd /home/forcing/htdocs/forcing.digitalinnovation.com.br && php artisan config:cache && php artisan route:cache && php artisan view:cache && chown -R forcing:forcing . && find . -type f -exec chmod 644 {} \; && find . -type d -exec chmod 755 {} \; && chmod -R 775 storage bootstrap/cache && systemctl restart nginx && systemctl restart php8.2-fpm"

echo ✅ Migração concluída!
echo 🌐 Sistema disponível em: https://forcing.digitalinnovation.com.br
pause
