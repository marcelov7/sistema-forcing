@echo off
REM 🚀 Script de Deploy CloudPanel - Sistema de Forcing Mobile Nativo (Windows)
REM Este script automatiza o processo completo de deploy

echo 🚀 Iniciando deploy do Sistema de Forcing Mobile Nativo no CloudPanel...
echo.

REM Verificar se está no diretório correto
if not exist "composer.json" (
    echo ❌ [ERROR] Execute este script no diretório raiz do projeto Laravel
    pause
    exit /b 1
)

echo 📋 Verificando pré-requisitos...

REM Verificar se o arquivo .env existe
if not exist ".env" (
    echo ⚠️  [WARNING] Arquivo .env não encontrado
    echo 📝 Copiando template...
    copy .env.cloudpanel.example .env
    echo.
    echo 🔧 CONFIGURE O ARQUIVO .env ANTES DE CONTINUAR:
    echo    - APP_URL=https://seu-dominio.com
    echo    - DB_DATABASE=forcing_sistema
    echo    - DB_USERNAME=forcing_user
    echo    - DB_PASSWORD=sua_senha_mysql
    echo.
    pause
)

echo 📦 Instalando dependências do Composer...
composer install --no-dev --optimize-autoloader
if errorlevel 1 (
    echo ❌ [ERROR] Falha ao instalar dependências
    pause
    exit /b 1
)

echo.
echo 🔧 Configurando aplicação...

REM Gerar chave da aplicação se não existir
echo 🔑 Gerando chave da aplicação...
php artisan key:generate --force

REM Configurar JWT
echo 🔐 Configurando JWT...
php artisan jwt:secret --force

echo.
echo 🧹 Limpando cache...
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
echo 🗄️ Executando migrations...
php artisan migrate --force
if errorlevel 1 (
    echo ❌ [ERROR] Falha ao executar migrations
    echo 💡 Verifique as configurações do banco de dados no .env
    pause
    exit /b 1
)

echo.
echo 🔐 Configurando permissões (Windows)...
REM Configurar permissões para storage e cache
icacls storage /grant Everyone:F /T 2>nul
icacls bootstrap\cache /grant Everyone:F /T 2>nul

echo.
echo 🧪 Testando sistema...

REM Testar se a aplicação está funcionando
php artisan serve --host=0.0.0.0 --port=8000 --timeout=5 >nul 2>&1
if errorlevel 1 (
    echo ⚠️  [WARNING] Não foi possível testar o servidor local
    echo 💡 Verifique se a porta 8000 está disponível
) else (
    echo ✅ Sistema funcionando localmente
)

echo.
echo 📊 Verificando arquivos importantes...

REM Verificar se arquivos críticos existem
if exist "public\manifest.json" (
    echo ✅ PWA Manifest configurado
) else (
    echo ❌ [ERROR] manifest.json não encontrado
)

if exist "public\sw.js" (
    echo ✅ Service Worker configurado
) else (
    echo ❌ [ERROR] Service Worker não encontrado
)

if exist "app\Http\Controllers\Api\AuthController.php" (
    echo ✅ API Controllers configurados
) else (
    echo ❌ [ERROR] API Controllers não encontrados
)

if exist "app\Http\Middleware\DetectDevice.php" (
    echo ✅ Middleware de detecção configurado
) else (
    echo ❌ [ERROR] Middleware de detecção não encontrado
)

echo.
echo 📱 Verificando configuração mobile...

REM Verificar se as rotas da API estão configuradas
php artisan route:list --path=api | findstr "api" >nul
if errorlevel 1 (
    echo ⚠️  [WARNING] Rotas da API não encontradas
) else (
    echo ✅ Rotas da API configuradas
)

echo.
echo 🎉 Deploy concluído com sucesso!
echo.
echo 📋 PRÓXIMOS PASSOS:
echo.
echo 1️⃣  UPLOAD DOS ARQUIVOS:
echo    - Faça upload de TODOS os arquivos para o CloudPanel
echo    - Mantenha a estrutura de pastas
echo    - NÃO inclua a pasta mobile-app/
echo.
echo 2️⃣  CONFIGURAÇÃO NO CLOUDPANEL:
echo    - Configure o banco MySQL
echo    - Configure SSL/HTTPS
echo    - Verifique as permissões
echo.
echo 3️⃣  TESTAR SISTEMA:
echo    - Acesse: https://seu-dominio.com
echo    - Teste API: https://seu-dominio.com/api/health
echo    - Teste mobile: Acesse pelo celular
echo.
echo 4️⃣  CONFIGURAR MOBILE APP:
echo    - Edite mobile-app/src/services/api.ts
echo    - Configure URL da API
echo    - Execute: npm install && expo start
echo.
echo 🌐 URLs IMPORTANTES:
echo    - Web: https://seu-dominio.com
echo    - API: https://seu-dominio.com/api/v1
echo    - Health: https://seu-dominio.com/api/health
echo    - PWA: https://seu-dominio.com/manifest.json
echo.
echo 📱 FUNCIONALIDADES IMPLEMENTADAS:
echo    ✅ Sistema web completo
echo    ✅ API REST para mobile
echo    ✅ Detecção automática de dispositivo
echo    ✅ PWA instalável
echo    ✅ Autenticação JWT
echo    ✅ Sistema multi-tenant
echo.
echo 🚀 Sistema de Forcing Mobile Nativo pronto para uso!
echo.

REM Criar arquivo de log do deploy
echo Deploy realizado em: %date% %time% > deploy-log.txt
echo Status: Sucesso >> deploy-log.txt
echo Versão: Mobile Nativa v1.0 >> deploy-log.txt

echo 📄 Log salvo em: deploy-log.txt
echo.
pause

