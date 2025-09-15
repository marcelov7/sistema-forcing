@echo off
setlocal enabledelayedexpansion

REM Script para atualizar a versão do PWA automaticamente (Windows)
REM Este script deve ser executado antes de cada deploy

echo 🔄 Atualizando versão do PWA...

REM Gera uma nova versão baseada na data/hora
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "YY=%dt:~2,2%" & set "YYYY=%dt:~0,4%" & set "MM=%dt:~4,2%" & set "DD=%dt:~6,2%"
set "HH=%dt:~8,2%" & set "Min=%dt:~10,2%"
set "NEW_VERSION=forcing-pwa-v%YYYY%-%MM%-%DD%-%HH%%Min%"

echo 📋 Gerando nova versão: %NEW_VERSION%

REM Verifica se o arquivo sw.js existe
if not exist "public\sw.js" (
    echo ❌ Arquivo sw.js não encontrado!
    pause
    exit /b 1
)

REM Atualiza a versão no sw.js usando PowerShell
powershell -Command "(Get-Content 'public\sw.js') -replace 'const CACHE_VERSION = ''[^'']*''', 'const CACHE_VERSION = ''%NEW_VERSION%''' | Set-Content 'public\sw.js'"

echo ✅ Versão atualizada para: %NEW_VERSION%
echo 📝 Cache será limpo automaticamente no próximo deploy

REM Gera hash para assets
set "ASSETS_HASH=%time:~6,2%%time:~9,2%%time:~12,2%"
set "ASSETS_HASH=%ASSETS_HASH: =0%"
echo 🔑 Hash de assets: %ASSETS_HASH%

REM Cria um arquivo de versão para referência
echo PWA_VERSION=%NEW_VERSION% > .pwa-version
echo ASSETS_HASH=%ASSETS_HASH% >> .pwa-version
echo DEPLOY_DATE=%date% %time% >> .pwa-version

echo.
echo 🎉 Atualização de versão concluída!
echo 💡 Próximos passos:
echo    1. Faça commit das alterações
echo    2. Deploy para produção
echo    3. Os usuários receberão a atualização automaticamente

echo.
echo 📊 Informações do Cache:
echo    • Nome do cache: forcing-cache-%NEW_VERSION%
echo    • Estratégia: Network-first para HTML, Cache-first para assets
echo    • Limpeza automática: Sim
echo    • Notificação de update: Sim

pause

