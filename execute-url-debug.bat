@echo off
echo === DIAGNÓSTICO AVANÇADO DE URLs ===
echo.

echo 1. Enviando script de diagnóstico...
scp debug-url-generation.sh devaxis-forcing@forcing.devaxis.com.br:~/
if errorlevel 1 (
    echo ERRO: Falha no upload
    pause
    exit /b 1
)
echo ✓ Script enviado
echo.

echo 2. Executando diagnóstico avançado...
ssh devaxis-forcing@forcing.devaxis.com.br "chmod +x ~/debug-url-generation.sh && ~/debug-url-generation.sh"
echo.

echo 3. Limpando arquivos temporários...
ssh devaxis-forcing@forcing.devaxis.com.br "rm ~/debug-url-generation.sh"
echo.

echo === DIAGNÓSTICO CONCLUÍDO ===
pause

