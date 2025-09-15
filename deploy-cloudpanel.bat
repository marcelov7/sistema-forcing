@echo off
REM 🚀 Script de Deploy CloudPanel - Sistema de Forcing (Windows)
REM Este script automatiza o processo de deploy no CloudPanel

echo 🚀 Iniciando deploy do Sistema de Forcing no CloudPanel...

REM Verificar se está no diretório correto
if not exist "composer.json" (
    echo [ERROR] Execute este script no diretório raiz do projeto Laravel
    pause
    exit /b 1
)

echo 📋 Verificando pré-requisitos...

REM Verificar se o arquivo .env existe
if not exist ".env" (
    echo [ERROR] Arquivo .env não encontrado. Configure o ambiente primeiro.
    pause
    exit /b 1
)

REM Verificar se as dependências estão instaladas
if not exist "vendor" (
    echo 📦 Instalando dependências do Composer...
    composer install --no-dev --optimize-autoloader
    if errorlevel 1 (
        echo [ERROR] Falha ao instalar dependências
        pause
        exit /b 1
    )
)

echo 🔧 Configurando aplicação...

REM Gerar chave da aplicação se não existir
php artisan key:generate

REM Limpar cache
echo 🧹 Limpando cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

REM Otimizar para produção
echo ⚡ Otimizando para produção...
php artisan config:cache
php artisan route:cache
php artisan view:cache

REM Verificar banco de dados
echo 🗄️ Verificando banco de dados...
php artisan migrate --force

REM Verificar se o JWT está configurado
php artisan jwt:secret --force

REM Verificar permissões (Windows)
echo 🔐 Configurando permissões...
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T

REM Testar API
echo 🧪 Testando API...
php artisan serve --host=0.0.0.0 --port=8000

echo ✅ Deploy concluído com sucesso!
echo.
echo 📱 Para configurar a aplicação mobile:
echo 1. Atualize a URL da API no arquivo mobile-app\src\services\api.ts
echo 2. Execute 'npm install' na pasta mobile-app\
echo 3. Execute 'expo start' para iniciar o app mobile
echo.
echo 🌐 URLs importantes:
echo - API Health: https://seu-dominio.com/api/health
echo - API Login: https://seu-dominio.com/api/v1/auth/login
echo - Dashboard: https://seu-dominio.com
echo.
echo 📊 Próximos passos:
echo 1. Configure SSL no CloudPanel
echo 2. Teste todas as funcionalidades
echo 3. Configure backup automático
echo 4. Monitore logs em storage\logs\laravel.log

echo.
echo 🎉 Sistema de Forcing pronto para uso!
pause
