@echo off
echo === CORREÇÃO FORÇADA DE URL ===
echo.

echo 1. Enviando script de correção...
scp force-url-fix.sh devaxis-forcing@forcing.devaxis.com.br:~/
if errorlevel 1 (
    echo ERRO: Falha no upload
    pause
    exit /b 1
)
echo ✓ Script enviado
echo.

echo 2. Executando correção forçada...
ssh devaxis-forcing@forcing.devaxis.com.br "chmod +x ~/force-url-fix.sh && ~/force-url-fix.sh"
echo.

echo 3. Limpando arquivo temporário...
ssh devaxis-forcing@forcing.devaxis.com.br "rm ~/force-url-fix.sh"
echo.

echo === CORREÇÃO CONCLUÍDA ===
echo Teste agora: https://forcing.devaxis.com.br
echo.
pause

