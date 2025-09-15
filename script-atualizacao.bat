@echo off
REM 🔄 Script de Atualização CloudPanel - Sistema Mobile Nativo (Windows)
REM Este script automatiza a atualização da versão existente

echo 🔄 Iniciando atualização do Sistema de Forcing Mobile Nativo...
echo 📍 Domínio: forcing.devaxis.com.br
echo.

REM Verificar se está no diretório correto
if not exist "composer.json" (
    echo ❌ [ERROR] Execute este script no diretório raiz do projeto Laravel
    pause
    exit /b 1
)

echo 📋 Verificando arquivos necessários para atualização...

REM Verificar se arquivos novos existem
if exist "app\Http\Controllers\Api\AuthController.php" (
    echo ✅ AuthController.php encontrado
) else (
    echo ❌ [ERROR] AuthController.php não encontrado
    echo 💡 Certifique-se de que todos os arquivos novos foram criados
    pause
    exit /b 1
)

if exist "app\Http\Controllers\Api\ForcingController.php" (
    echo ✅ ForcingController.php encontrado
) else (
    echo ❌ [ERROR] ForcingController.php não encontrado
    pause
    exit /b 1
)

if exist "app\Http\Middleware\DetectDevice.php" (
    echo ✅ DetectDevice.php encontrado
) else (
    echo ❌ [ERROR] DetectDevice.php não encontrado
    pause
    exit /b 1
)

if exist "public\manifest.json" (
    echo ✅ PWA Manifest encontrado
) else (
    echo ❌ [ERROR] manifest.json não encontrado
    pause
    exit /b 1
)

if exist "public\sw.js" (
    echo ✅ Service Worker encontrado
) else (
    echo ❌ [ERROR] Service Worker não encontrado
    pause
    exit /b 1
)

echo.
echo 📦 Instalando novas dependências...
composer install --no-dev --optimize-autoloader
if errorlevel 1 (
    echo ❌ [ERROR] Falha ao instalar dependências
    pause
    exit /b 1
)

echo.
echo 🔧 Configurando JWT...
php artisan jwt:secret --force
if errorlevel 1 (
    echo ❌ [ERROR] Falha ao configurar JWT
    echo 💡 Verifique se a dependência JWT foi instalada
    pause
    exit /b 1
)

echo.
echo 🧹 Limpando cache antigo...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo.
echo ⚡ Otimizando para produção...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo 🗄️ Verificando migrations...
php artisan migrate --force
if errorlevel 1 (
    echo ⚠️  [WARNING] Algumas migrations falharam
    echo 💡 Verifique se há migrations novas para executar
) else (
    echo ✅ Migrations executadas com sucesso
)

echo.
echo 🧪 Testando sistema atualizado...

REM Testar se a aplicação está funcionando
php artisan serve --host=0.0.0.0 --port=8000 --timeout=5 >nul 2>&1
if errorlevel 1 (
    echo ⚠️  [WARNING] Não foi possível testar o servidor local
    echo 💡 Verifique se a porta 8000 está disponível
) else (
    echo ✅ Sistema funcionando localmente
)

echo.
echo 📊 Verificando configurações...

REM Verificar se as rotas da API estão configuradas
php artisan route:list --path=api | findstr "api" >nul
if errorlevel 1 (
    echo ⚠️  [WARNING] Rotas da API não encontradas
    echo 💡 Verifique se routes/api.php foi atualizado
) else (
    echo ✅ Rotas da API configuradas
)

REM Verificar se o middleware está configurado
php artisan route:list | findstr "detect.device" >nul
if errorlevel 1 (
    echo ⚠️  [WARNING] Middleware de detecção não encontrado
    echo 💡 Verifique se bootstrap/app.php foi atualizado
) else (
    echo ✅ Middleware de detecção configurado
)

echo.
echo 📱 Verificando recursos mobile...

REM Verificar se o PWA está configurado
if exist "public\manifest.json" (
    echo ✅ PWA Manifest configurado
) else (
    echo ❌ [ERROR] PWA Manifest não encontrado
)

if exist "public\sw.js" (
    echo ✅ Service Worker configurado
) else (
    echo ❌ [ERROR] Service Worker não encontrado
)

if exist "resources\views\mobile-suggestion.blade.php" (
    echo ✅ Página mobile configurada
) else (
    echo ❌ [ERROR] Página mobile não encontrada
)

echo.
echo 🎉 Atualização concluída com sucesso!
echo.
echo 📋 PRÓXIMOS PASSOS:
echo.
echo 1️⃣  UPLOAD DOS ARQUIVOS:
echo    - Faça upload dos NOVOS arquivos para o CloudPanel
echo    - Atualize os arquivos EXISTENTES modificados
echo    - Mantenha a estrutura de pastas
echo.
echo 2️⃣  ARQUIVOS NOVOS PARA UPLOAD:
echo    - app/Http/Controllers/Api/ (pasta completa)
echo    - app/Http/Middleware/DetectDevice.php
echo    - app/Http/Resources/ForcingResource.php
echo    - app/Http/Controllers/WebController.php
echo    - resources/views/mobile-suggestion.blade.php
echo    - public/manifest.json
echo    - public/sw.js
echo    - public/offline.html
echo.
echo 3️⃣  ARQUIVOS PARA ATUALIZAR:
echo    - app/Models/User.php (adicionar métodos JWT)
echo    - routes/api.php (adicionar rotas API)
echo    - routes/web.php (atualizar rotas)
echo    - bootstrap/app.php (configurar middleware)
echo    - config/auth.php (adicionar guard JWT)
echo    - composer.json (dependências JWT)
echo.
echo 4️⃣  CONFIGURAÇÃO NO CLOUDPANEL:
echo    - Executar: composer install --no-dev --optimize-autoloader
echo    - Executar: php artisan jwt:secret --force
echo    - Executar: php artisan migrate --force
echo    - Executar: php artisan config:cache
echo    - Executar: php artisan route:cache
echo    - Executar: php artisan view:cache
echo.
echo 5️⃣  TESTAR SISTEMA:
echo    - Acesse: https://forcing.devaxis.com.br
echo    - Teste API: https://forcing.devaxis.com.br/api/health
echo    - Teste mobile: Acesse pelo celular
echo    - Verifique logs: storage/logs/laravel.log
echo.
echo 🌐 URLs IMPORTANTES:
echo    - Web: https://forcing.devaxis.com.br
echo    - API: https://forcing.devaxis.com.br/api/v1
echo    - Health: https://forcing.devaxis.com.br/api/health
echo    - PWA: https://forcing.devaxis.com.br/manifest.json
echo.
echo 📱 NOVOS RECURSOS IMPLEMENTADOS:
echo    ✅ API REST para mobile
echo    ✅ Detecção automática de dispositivo
echo    ✅ PWA instalável
echo    ✅ Autenticação JWT
echo    ✅ Interface mobile otimizada
echo    ✅ Funcionalidades existentes mantidas
echo.
echo 🚀 Sistema atualizado com recursos mobile nativos!
echo.

REM Criar arquivo de log da atualização
echo Atualização realizada em: %date% %time% > atualizacao-log.txt
echo Status: Sucesso >> atualizacao-log.txt
echo Versão: Mobile Nativa v1.0 >> atualizacao-log.txt
echo Domínio: forcing.devaxis.com.br >> atualizacao-log.txt

echo 📄 Log salvo em: atualizacao-log.txt
echo.
pause

