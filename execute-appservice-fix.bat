@echo off
echo === CORREÇÃO DO APPSERVICEPROVIDER ===
echo.

echo 1. Enviando script de correção...
scp fix-appserviceprovider.sh devaxis-forcing@forcing.devaxis.com.br:~/
if errorlevel 1 (
    echo ERRO: Falha no upload
    pause
    exit /b 1
)
echo ✓ Script enviado
echo.

echo 2. Executando correção...
ssh devaxis-forcing@forcing.devaxis.com.br "chmod +x ~/fix-appserviceprovider.sh && ~/fix-appserviceprovider.sh"
echo.

echo 3. Limpando arquivo temporário...
ssh devaxis-forcing@forcing.devaxis.com.br "rm ~/fix-appserviceprovider.sh"
echo.

echo === CORREÇÃO CONCLUÍDA ===
echo TESTE AGORA: https://forcing.devaxis.com.br
echo.
pause

